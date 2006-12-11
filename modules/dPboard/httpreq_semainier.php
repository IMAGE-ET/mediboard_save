<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPbloard
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m, $listMins,$listHours,$HeureMax,$MinMax,$HeureMin,$MinMin,$aAffichage;

if (!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

// R�cup�ration des param�tres
$chirSel   = mbGetValueFromGetOrSession("chirSel", 25);
$date      = mbGetValueFromGetOrSession("date", mbDate());
$debut = mbDate("last sunday", $date);
$debut = mbDate("+1 day", $debut);

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

end($listHours);
end($listMins);
$HeureMax = intval(current($listHours));
$MinMax   = intval(current($listMins));
reset($listHours);
reset($listMins);
$HeureMin = intval(current($listHours));
$MinMin   = intval(current($listMins));

function writePlage($aAffichage,$listPlages,$type,$sHeureDeb,$sHeureFin,$sMinDeb,$sMinFin){
  global $listMins,$listHours,$HeureMax,$MinMax,$HeureMin,$MinMin;
  
  foreach($listPlages as $keyPlages=>$valPlages){
    foreach($valPlages as $keyvalPlages=>$valvalPlages){
      // Test validit� des plages dans le semainier    
      $heure_fin = intval($valvalPlages->$sHeureFin);
      $heure_deb = intval($valvalPlages->$sHeureDeb);
      $min_deb   = intval($valvalPlages->$sMinDeb);
      $min_fin   = intval($valvalPlages->$sMinFin);
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

      if($heure_fin>$HeureMax && $heure_deb<=$HeureMax && $min_deb<$MinMax){
        $heure_fin = $HeureMax;
        $min_fin   = $MinMax;
      }elseif($heure_deb<$HeureMin && $heure_fin>=$HeureMin && $min_fin>$MinMin){
        $heure_deb = $HeureMin;
        $min_deb   = $MinMin;
      }elseif($heure_fin>$HeureMax || $heure_deb<$HeureMin){
        // Plages Hors semainier
        $outPlage = true;
      }      
      
      if(!$outPlage){
        // M�morisation des objets
        $nbquartheure = ($heure_fin-$heure_deb)*4;
        $nbquartheure = $nbquartheure - array_search($min_deb,$listMins) + array_search($min_fin,$listMins);
        
        $valvalPlages->_nbQuartHeure = $nbquartheure;
        $aAffichage[$valvalPlages->date." ".$heure_deb.":".$min_deb][$type] = $valvalPlages;
        // D�termination des horaire non vides
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
            $aAffichage[$valvalPlages->date." ".$heure_encours.":".$listMins[$min_encours]][$type] = "full"; 
          }          
        }
      }
    }
  } 
  
  // Recherche d'heure completement vides
  foreach($listPlages as $keyDate=>$valDate){
    foreach($listHours as $keyHours=>$valHours){
      $heure_vide = 1;
      foreach($listMins as $kayMins=>$valMins){
        // V�rification donn�es
        if(!is_string($aAffichage["$keyDate $valHours:$valMins"][$type]) || (is_string($aAffichage["$keyDate $valHours:$valMins"][$type]) && $aAffichage["$keyDate $valHours:$valMins"][$type]!= "empty")){
          $heure_vide = 0;
        }
      }
      if($heure_vide==1){
        $first = "hours";
        foreach($listMins as $kayMins=>$valMins){
          // M�morisation heure vide
          $aAffichage["$keyDate $valHours:$valMins"][$type] = $first;
          $first = "full";
        }
      }
    }
  }
}

// Plages de Consultations
$plageConsult     = new CPlageconsult();
$plageOp          = new CPlageOp();
$listDays         = array();
$plagesConsult    = array();
$plagesOp         = array();
$where["chir_id"] = "= '$chirSel'";

for($i = 0; $i < 7; $i++) {
  $date          = mbDate("+$i day", $debut);
  $where["date"] = "= '$date'";
  
  $plagesPerDayConsult = $plageConsult->loadList($where);
  $plagesPerDayOp      = $plageOp->loadList($where);
  
  if(!( ($i == 5 || $i == 6) && !count($plagesPerDayConsult) && !count($plagesPerDayOp) )){
    foreach($plagesPerDayConsult as $key => $value) {
      $plagesPerDayConsult[$key]->loadFillRate();
    }
    foreach($plagesPerDayOp as $key => $value) {
      $plagesPerDayOp[$key]->loadRefSalle();
    }
    
    $plagesConsult[$date] = $plagesPerDayConsult;
    $plagesOp[$date]      = $plagesPerDayOp;
  }
  $listDays[] = $date;
}

// Cr�ation du tableau de visualisation
$aAffichage      = array();
foreach($plagesConsult as $keyDate=>$valDate){
  foreach($listHours as $keyHours=>$valHours){
    foreach($listMins as $kayMins=>$valMins){
      // Initialisation du tableau
      $aAffichage["$keyDate $valHours:$valMins"] = array("plagesConsult"=>"empty", "plagesOp"=>"empty");
    }
  }
}

writePlage(&$aAffichage, $plagesConsult,"plagesConsult","_hour_deb","_hour_fin","_min_deb","_min_fin");
writePlage(&$aAffichage, $plagesOp,"plagesOp","_heuredeb","_heurefin","_minutedeb","_minutefin");

// Cr�ation du template
$smarty = new CSmartyDP(1);


$smarty->assign("aAffichage"        , $aAffichage);
$smarty->assign("chirSel"           , $chirSel);
$smarty->assign("debut"             , $debut);
$smarty->assign("listHours"         , $listHours);
$smarty->assign("listMins"          , $listMins);
$smarty->assign("plagesConsult"     , $plagesConsult);

$smarty->display("inc_semainier.tpl");
?>