<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author S�bastien Fillonneau
*/

global $AppUI, $can, $m, $g;

$date    = mbGetValueFromGetOrSession("date", mbDate());
$bloc_id = mbGetValueFromGetOrSession("bloc_id");

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
/*$salle = new CSalle();
$listSalles = $salle->loadListWithPerms(PERM_READ, null, "nom");*/

// Liste des blocs
$listBlocs = CGroups::loadCurrent()->loadBlocs();

$bloc = new CBlocOperatoire();
if (!$bloc->load($bloc_id) && count($listBlocs)) {
	$bloc = reset($listBlocs);
}

$bloc->loadRefsSalles();

if (!$listSalles = $bloc->_ref_salles) {
  $listSalles = array();
}

// Cr�ation du tableau de visualisation
$affichages = array();
foreach($listDays as $keyDate=>$valDate){
  foreach($listSalles as $keySalle=>$valSalle){
    foreach(CPlageOp::$hours as $keyHours=>$valHours){
      foreach(CPlageOp::$minutes as $keyMins=>$valMins){
        // Initialisation du tableau
        $affichages["$keyDate-s$keySalle-$valHours:$valMins:00"] = "empty";
      }
    }
  }
}

$listPlages = array();


// Extraction des plagesOp par date
foreach($listDays as $keyDate=>$valDate){
  // R�cup�ration des plages par jour
  $listPlage = new CPlageOp();
  $where = array();
  $where["date"] = "= '$keyDate'";
  $order = "debut";
  $listPlages[$keyDate] = $listPlage->loadList($where,$order);
  
  // D�termination des bornes du semainier
  $min = CPlageOp::$hours_start.":".reset(CPlageOp::$minutes).":00";
  $max = CPlageOp::$hours_stop.":".end(CPlageOp::$minutes).":00";
  
  // D�termination des bornes de chaque plage
  foreach($listPlages[$keyDate] as $plage){
    $plage->loadRefsFwd();
    $plage->_ref_chir->loadRefsFwd();
    $plage->getNbOperations();
    //$plage->loadAffectationsPersonnel();
  
    $plage->fin = min($plage->fin, $max);
    $plage->debut = max($plage->debut, $min);
  
    $plage->updateFormFields();
    $plage->makeView();
  
    if($plage->debut >= $plage->fin){  
      unset($listPlages[$keyDate][$plage->_id]);
    }
  }
  
  
  foreach($listPlages[$keyDate] as $plage){
    $plage->_nbQuartHeure = mbTimeCountIntervals($plage->debut, $plage->fin, "00:".CPlageOp::$minutes_interval.":00");
    for($time = $plage->debut; $time < $plage->fin; $time = mbTime("+15 minutes", $time) ){
      $affichages["$keyDate-s$plage->salle_id-$time"] = "full";
    } 
    $affichages["$keyDate-s$plage->salle_id-$plage->debut"] = $plage->_id;
  }
}

// Liste des Sp�cialit�s
$listSpec = new CFunctions();
$listSpec = $listSpec->loadSpecialites();

//Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("listPlages"     , $listPlages        );
$smarty->assign("listDays"       , $listDays          );
$smarty->assign("listBlocs"      , $listBlocs         );
$smarty->assign("bloc"           , $bloc              );
$smarty->assign("listSalles"     , $listSalles        );
$smarty->assign("listHours"      , CPlageOp::$hours   );
$smarty->assign("listMins"       , CPlageOp::$minutes );
$smarty->assign("affichages"     , $affichages        );
$smarty->assign("date"           , $date              );
$smarty->assign("listSpec"       , $listSpec          );

$smarty->display("vw_planning_week.tpl");
?>
