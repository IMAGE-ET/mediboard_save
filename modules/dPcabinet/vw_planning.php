<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canEdit) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

$_firstconsult_time  = null;
$_lastconsult_time   = null;

// L'utilisateur est-il praticien?
$chir = null;
$mediuser = new CMediusers();
$mediuser->load($AppUI->user_id);
if ($mediuser->isPraticien()) {
  $chir = $mediuser->createUser();
}

// Type de vue
$vue = mbGetValueFromGetOrSession("vue1");

// Praticien selectionné
$chirSel = mbGetValueFromGetOrSession("chirSel", $chir ? $chir->user_id : null);

// Plage de consultation selectionnée
$plageconsult_id = mbGetValueFromGetOrSession("plageconsult_id");
$plageSel = new CPlageconsult();
$plageSel->load($plageconsult_id);
$plageSel->loadRefs();
foreach($plageSel->_ref_consultations as $key => $value) {
  if ($vue && $plageSel->_ref_consultations[$key]->paye)
    unset($plageSel->_ref_consultations[$key]);
  else {
    $plageSel->_ref_consultations[$key]->loadRefPatient();
    $plageSel->_ref_consultations[$key]->getNumDocsAndFiles();    
  }
}
if ($plageSel->chir_id != $chirSel) {
  $plageconsult_id = null;
  mbSetValueToSession("plageconsult_id", $plageconsult_id);
  $plageSel = new CPlageconsult();
}
if($plageSel->_affected){
  reset($plageSel->_ref_consultations);
  $firstconsult = current($plageSel->_ref_consultations)->heure;
  $_firstconsult_time = substr($firstconsult, 0, 5);

  end($plageSel->_ref_consultations);
  $lastconsult = current($plageSel->_ref_consultations)->heure;
  $_lastconsult_time  = substr($lastconsult, 0, 5);
}
// Liste des chirurgiens
$mediusers = new CMediusers();
$listChirs = $mediusers->loadPraticiens(PERM_EDIT);

// Période
$today = mbDate();
$debut = mbGetValueFromGetOrSession("debut", $today);
$debut = mbDate("last sunday", $debut);
$fin   = mbDate("next sunday", $debut);
$debut = mbDate("+1 day", $debut);

$prec = mbDate("-1 week", $debut);
$suiv = mbDate("+1 week", $debut);

// Sélection des plages
$plage = new CPlageconsult();
$where["chir_id"] = "= '$chirSel'";
for($i = 0; $i < 7; $i++) {
  $date = mbDate("+$i day", $debut);
  $where["date"] = "= '$date'";
  $plagesPerDay = $plage->loadList($where);
  foreach($plagesPerDay as $key => $value) {
    $plagesPerDay[$key]->loadFillRate();
  }
  $plages[$date] = $plagesPerDay;
}

// Liste des heures
$listHours = array();
for($i = 8; $i <= 20; $i++) {
  $listHours[$i] = $i;
}

// Liste des minutes
$listMins = array();
$listMins[] = 00;
$listMins[] = 15;
$listMins[] = 30;
$listMins[] = 45;


// Création du tableau de visualisation
$arrayAffichage = array();
foreach($plages as $keyDate=>$valDate){
  foreach($listHours as $keyHours=>$valHours){
    foreach($listMins as $kayMins=>$valMins){
      // Initialisation du tableau
      $arrayAffichage["$keyDate $valHours:$valMins"] = "empty";
    }
  }
}
foreach($plages as $keyPlages=>$valPlages){
  foreach($valPlages as $keyvalPlages=>$valvalPlages){
    // Test validité des plages dans le semainier    
    $heure_fin = $valvalPlages->_hour_fin;
    $heure_deb = $valvalPlages->_hour_deb;
    $min_deb   = $valvalPlages->_min_deb;
    $min_fin   = $valvalPlages->_min_fin;
    $outPlage = false;
    foreach(array("min_deb","min_fin") as $minute){
      $minute_trouve = array_search(${$minute},$listMins);
      if($minute_trouve===false){
        $afterValue = 0;
        foreach($listMins as $valueMin){
          if(${$minute} > $valueMin && $afterValue!==null){
            $afterValue = $valueMin;
          }elseif($afterValue!==null){
            // Entre l'ancienne valeur et celle ci
            $centerValue = $afterValue + ($valueMin-$afterValue)/2;
            $afterValue = null;
            if(${$minute}>$centerValue){
              ${$minute} = $valueMin;
            }else{
              ${$minute} = $afterValue;
            }
          }
        }
        if($afterValue!==null){
        	${$minute} = $afterValue;
        }
      } 
    }
    
    if($heure_fin>20 && $heure_deb<=20 && $min_deb<45){
      $heure_fin = 20;
      $min_fin   = 45;
    }elseif($heure_deb<8 && $heure_fin>=8 && $min_fin>0){
      $heure_deb = 8;
      $min_deb   = 0;
    }elseif($heure_fin>20 || $heure_deb<8){
      // Plages Hors semainier
      $outPlage = true;
    }
    
    if(!$outPlage){
      // Mémorisation des objets
      $nbquartheure = ($heure_fin-$heure_deb)*4;
      $nbquartheure = $nbquartheure - array_search($min_deb,$listMins) + array_search($min_fin,$listMins);
      
      $valvalPlages->_nbQuartHeure = $nbquartheure;
      $arrayAffichage[$valvalPlages->date." ".$heure_deb.":".$min_deb] = $valvalPlages;
      // Détermination des horaire non vides
      $heure_encours = array_search($heure_deb,$listHours);
      $min_encours   = array_search($min_deb,$listMins);    
      $dans_plage = true;
      while($dans_plage == true){      
        $min_encours ++;
        if(!array_key_exists($min_encours,$listMins)){
          $min_encours=0;
          $heure_encours ++;
          if(!array_key_exists($heure_encours,$listHours)){
            $heure_encours=8;
          }
        }        
        if($heure_encours==$heure_fin && $listMins[$min_encours]==$min_fin){
          $dans_plage = false;
        }else{
          $arrayAffichage[$valvalPlages->date." ".$heure_encours.":".$listMins[$min_encours]] = "full";	
        }          
      }
    }
  }
}
// Recherche d'heure completement vides
foreach($plages as $keyDate=>$valDate){
  foreach($listHours as $keyHours=>$valHours){
    $heure_vide = 1;
    foreach($listMins as $kayMins=>$valMins){
      // Vérification données
      if(!is_string($arrayAffichage["$keyDate $valHours:$valMins"]) || (is_string($arrayAffichage["$keyDate $valHours:$valMins"]) && $arrayAffichage["$keyDate $valHours:$valMins"]!= "empty")){
        $heure_vide = 0;
      }
    }
    if($heure_vide==1){
      $first = "hours";
      foreach($listMins as $kayMins=>$valMins){
        // Mémorisation heure vide
        $arrayAffichage["$keyDate $valHours:$valMins"] = $first;
        $first = "full";
      }
    }
  }
}


// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("_firstconsult_time", $_firstconsult_time);
$smarty->assign("_lastconsult_time", $_lastconsult_time);
$smarty->assign("arrayAffichage", $arrayAffichage);
$smarty->assign("plageconsult_id", $plageconsult_id);
$smarty->assign("vue", $vue);
$smarty->assign("chirSel", $chirSel);
$smarty->assign("plageSel", $plageSel);
$smarty->assign("listChirs", $listChirs);
$smarty->assign("plages", $plages);
$smarty->assign("today", $today);
$smarty->assign("debut", $debut);
$smarty->assign("fin", $fin);
$smarty->assign("prec", $prec);
$smarty->assign("suiv", $suiv);
$smarty->assign("listHours", $listHours);
$smarty->assign("listMins", $listMins);

$smarty->display("vw_planning.tpl");
?>