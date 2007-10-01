<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $can, $m, $g;

$date = mbGetValueFromGetOrSession("date", mbDate());

$date = mbDate("last sunday", $date);
$fin  = mbDate("next sunday", $date);
$date = mbDate("+1 day", $date);

// Liste des jours
$listDays = array();
for($i = 0; $i < 7; $i++) {
  $dateArr = mbDate("+$i day", $date);
  $listDays[$dateArr] = $dateArr;  
}




// Liste des Salles
$salle = new CSalle();
$where = array();
$where["group_id"] = "= '$g'";
$order = "'nom'";
$listSalles = $salle->loadListWithPerms(PERM_READ, $where, $order);

// Cr�ation du tableau de visualisation
$arrayAffichage = array();
foreach($listDays as $keyDate=>$valDate){
  foreach($listSalles as $keySalle=>$valSalle){
    foreach(CPlageOp::$hours as $keyHours=>$valHours){
      foreach(CPlageOp::$minutes as $keyMins=>$valMins){
        // Initialisation du tableau
        $arrayAffichage["$keyDate-s$keySalle-$valHours:$valMins"] = "empty";
      }
    }
  }
}


// Extraction des plagesOp par date
foreach($listDays as $keyDate=>$valDate){
  // R�cup�ration des plages par jour
  $listPlages = new CPlageOp();
  $where = array();
  $where["date"] = "= '$keyDate'";
  $order = "debut";
  $listPlages = $listPlages->loadList($where,$order);

  foreach($listPlages as $keyPlages=>$valPlages){
    $listPlages[$keyPlages]->loadRefsFwd();
    $listPlages[$keyPlages]->_ref_chir->loadRefsFwd();
    $listPlages[$keyPlages]->getNbOperations();
  
    // Plages pas totalement comprises dans le semainier
    if($valPlages->_heurefin > CPlageOp::$hours_stop){
      $valPlages->_heurefin = CPlageOp::$hours_stop;
      $valPlages->_minutefin = end(CPlageOp::$minutes);
    }
    if($valPlages->_heuredeb < CPlageOp::$hours_start){
      $valPlages->_heuredeb = CPlageOp::$hours_start;
      $valPlages->_minutedeb   = reset(CPlageOp::$minutes);
    }
  
    // Initialisation des variables 
    $outPlage = false;
    $dans_plage = true;
  
    // Cas des plages non comprises dans le semainier
    if($valPlages->_heurefin <= CPlageOp::$hours_start || $valPlages->_heuredeb >= CPlageOp::$hours_stop){
      $outPlage = true;
    }
  
    // si on est dans le semainier
    if(!$outPlage){ 	
      // M�morisation dans le tableau d'affichage
      $nbquartheure = ($valPlages->_heurefin-$valPlages->_heuredeb)*4;
      $nbquartheure = $nbquartheure - array_search($valPlages->_minutedeb,CPlageOp::$minutes) + array_search($valPlages->_minutefin,CPlageOp::$minutes);
      $valPlages->_nbQuartHeure = $nbquartheure;
      $arrayAffichage["$keyDate-s".$valPlages->salle_id."-".intval($valPlages->_heuredeb).":".$valPlages->_minutedeb] = $valPlages;
       
  	  // D�termination des horaire non vides
      $heure_encours = array_search(intval($valPlages->_heuredeb),CPlageOp::$hours);
      $min_encours   = array_search($valPlages->_minutedeb,CPlageOp::$minutes);
   
  	  while($dans_plage == true){      
        $min_encours ++;
        if(!array_key_exists($min_encours,CPlageOp::$minutes)){
          $min_encours=0;
          $heure_encours ++;
          if(!array_key_exists($heure_encours,CPlageOp::$hours)){
            $heure_encours=CPlageOp::$hours_start;
          }
        } 
        if($heure_encours == $valPlages->_heurefin && CPlageOp::$minutes[$min_encours] == $valPlages->_minutefin){
          $dans_plage = false;
        }else{
          $arrayAffichage["$keyDate-s".$valPlages->salle_id."-".$heure_encours.":".CPlageOp::$minutes[$min_encours]] = "full"; 
        }
      } 
     }
   }
}

// Liste des Sp�cialit�s
$listSpec = new CFunctions();
$listSpec = $listSpec->loadSpecialites();


//Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("listDays"       , $listDays          );
$smarty->assign("listSalles"     , $listSalles        );
$smarty->assign("listHours"      , CPlageOp::$hours   );
$smarty->assign("listMins"       , CPlageOp::$minutes );
$smarty->assign("arrayAffichage" , $arrayAffichage    );
$smarty->assign("date"           , $date              );
$smarty->assign("listSpec"       , $listSpec          );

$smarty->display("vw_planning_week.tpl");
?>
