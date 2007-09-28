<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

$_firstconsult_time  = null;
$_lastconsult_time   = null;

// L'utilisateur est-il praticien ?
$chir = null;
$mediuser = new CMediusers();
$mediuser->load($AppUI->user_id);
if ($mediuser->isPraticien()) {
  $chir = $mediuser->createUser();
}

// Type de vue
$vue = mbGetValueFromGetOrSession("vue1");

// Praticien selectionn�
$chirSel = mbGetValueFromGetOrSession("chirSel", $chir ? $chir->user_id : null);

// P�riode
$today = mbDate();
$debut = mbGetValueFromGetOrSession("debut", $today);
$debut = mbDate("last sunday", $debut);
$fin   = mbDate("next sunday", $debut);
$debut = mbDate("+1 day", $debut);

$is_in_period = ($today >= $debut) && ($today <= $fin);

$prec = mbDate("-1 week", $debut);
$suiv = mbDate("+1 week", $debut);

// Plage de consultation selectionn�e
$plageconsult_id = mbGetValueFromGetOrSession("plageconsult_id", null);
$plageSel = new CPlageconsult();
if(($plageconsult_id === null) && $chirSel && $is_in_period) {
  $nowTime = mbTime();
  $where = array(
    "chir_id" => "= '$chirSel'",
    "date"    => "= '$today'",
    "debut"   => "<= '$nowTime'",
    "fin"     => ">= '$nowTime'"
  );
  $plageSel->loadObject($where);
}
if(!$plageSel->plageconsult_id) {
  $plageSel->load($plageconsult_id);
} else {
  $plageconsult_id = $plageSel->plageconsult_id;
}
$plageSel->loadRefs();

if ($plageSel->_affected) {
  $firstconsult = reset($plageSel->_ref_consultations);
  $_firstconsult_time = substr($firstconsult->heure, 0, 5);
  $lastconsult = end($plageSel->_ref_consultations);
  $_lastconsult_time  = substr($lastconsult->heure, 0, 5);
}

// D�tails sur les consultation affich�es
foreach ($plageSel->_ref_consultations as $keyConsult => &$consultation) {
  if ($vue && $consultation->paye) {
    unset($plageSel->_ref_consultations[$keyConsult]);
    continue;
  }
  $consultation->loadRefPatient();
  $consultation->loadRefCategorie();
  $consultation->getNumDocsAndFiles();    
}

if ($plageSel->chir_id != $chirSel) {
  $plageconsult_id = null;
  $plageSel = new CPlageconsult();
}

mbSetValueToSession("plageconsult_id", $plageconsult_id);

// Liste des chirurgiens
$mediusers = new CMediusers();
$listChirs = $mediusers->loadPraticiens(PERM_EDIT);

// S�lection des plages
$plage    = new CPlageconsult();
$listDays = array();
$where = array();
$where["chir_id"] = "= '$chirSel'";
for($i = 0; $i < 7; $i++) {
  $date = mbDate("+$i day", $debut);
  $where["date"] = "= '$date'";
  $plagesPerDay = $plage->loadList($where);
  if(!(($i == 5 || $i == 6) && !count($plagesPerDay))) {
    foreach($plagesPerDay as $key => $value) {
      $plagesPerDay[$key]->loadFillRate();
    }
    $plages[$date] = $plagesPerDay;
  }
  $listDays[] = $date;
}

// Liste des heures et minutes
$listHours = CPlageconsult::$hours;
$listMins = CPlageconsult::$minutes;

// Cr�ation du tableau de visualisation
$arrayAffichage = array();
foreach ($plages as $keyDate=>$valDate){
  foreach ($listHours as $keyHours=>$valHours){
    foreach ($listMins as $kayMins=>$valMins){
      // Initialisation du tableau
      $arrayAffichage["$keyDate $valHours:$valMins"] = "empty";
    }
  }
}

foreach ($plages as $keyPlages=>$valPlages){
  foreach ($valPlages as $keyvalPlages=>$valvalPlages){
    // Test validit� des plages dans le semainier    
    $heure_fin = $valvalPlages->_hour_fin;
    $heure_deb = $valvalPlages->_hour_deb;
    $min_deb   = $valvalPlages->_min_deb;
    $min_fin   = $valvalPlages->_min_fin;
    $outPlage = false;
    foreach (array("min_deb","min_fin") as $minute){
      $minute_trouve = array_search(${$minute},$listMins);
      if ($minute_trouve===false){
        $afterValue = 0;
        foreach ($listMins as $valueMin){
          if (${$minute} > $valueMin && $afterValue!==null){
            $afterValue = $valueMin;
          } elseif($afterValue!==null){
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
    
    if($heure_fin>CPlageConsult::$hours_stop && $heure_deb<=CPlageConsult::$hours_stop && $min_deb<end(CPlageconsult::$minutes)){
      $heure_fin = CPlageConsult::$hours_stop;
      $min_fin   = end(CPlageconsult::$minutes);
    }elseif($heure_deb<CPlageconsult::$hours_start && $heure_fin>=CPlageconsult::$hours_start && $min_fin>0){
      $heure_deb = CPlageconsult::$hours_start;
      $min_deb   = reset(CPlageconsult::$minutes);;
    }elseif($heure_fin>CPlageConsult::$hours_stop || $heure_deb<CPlageconsult::$hours_start){
      // Plages Hors semainier
      $outPlage = true;
    }
    
    if(!$outPlage){
      // M�morisation des objets
      $nbquartheure = ($heure_fin-$heure_deb)*count($listMins);
      $nbquartheure = $nbquartheure - array_search($min_deb,$listMins) + array_search($min_fin,$listMins);
      
      $valvalPlages->_nbQuartHeure = $nbquartheure;
      $arrayAffichage[$valvalPlages->date." ".$heure_deb.":".$min_deb] = $valvalPlages;

      // D�termination des horaire non vides
      $heure_encours = array_search($heure_deb,$listHours) + CPlageconsult::$hours_start;
      $min_encours   = array_search($min_deb,$listMins);    
      $dans_plage = true;
      while($dans_plage == true){      
        $min_encours ++;
        if(!array_key_exists($min_encours,$listMins)){
          $min_encours=0;
          $heure_encours ++;
          if(!in_array($heure_encours, $listHours)){
            $heure_encours=CPlageconsult::$hours_start;
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
      // V�rification donn�es
      if(!is_string($arrayAffichage["$keyDate $valHours:$valMins"]) || (is_string($arrayAffichage["$keyDate $valHours:$valMins"]) && $arrayAffichage["$keyDate $valHours:$valMins"]!= "empty")){
        $heure_vide = 0;
      }
    }
    if($heure_vide==1){
      $first = "hours";
      foreach($listMins as $kayMins=>$valMins){
        // M�morisation heure vide
        $arrayAffichage["$keyDate $valHours:$valMins"] = $first;
        $first = "full";
      }
    }
  }
}


// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("_firstconsult_time", $_firstconsult_time);
$smarty->assign("_lastconsult_time" , $_lastconsult_time);
$smarty->assign("arrayAffichage"    , $arrayAffichage);
$smarty->assign("plageconsult_id"   , $plageconsult_id);
$smarty->assign("vue"               , $vue);
$smarty->assign("chirSel"           , $chirSel);
$smarty->assign("plageSel"          , $plageSel);
$smarty->assign("listChirs"         , $listChirs);
$smarty->assign("listDays"          , $listDays);
$smarty->assign("plages"            , $plages);
$smarty->assign("today"             , $today);
$smarty->assign("debut"             , $debut);
$smarty->assign("fin"               , $fin);
$smarty->assign("prec"              , $prec);
$smarty->assign("suiv"              , $suiv);
$smarty->assign("listHours"         , $listHours);
$smarty->assign("listMins"          , $listMins);

$smarty->display("vw_planning.tpl");
?>