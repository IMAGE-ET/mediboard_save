<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$date    = CValue::getOrSession("date", mbDate());

$date = mbDate("last sunday", $date);
$fin  = mbDate("next sunday", $date);
$date = mbDate("+1 day", $date);

// Liste des jours
$listDays = array();
for($i = 0; $i < 7; $i++) {
  $dateArr = mbDate("+$i day", $date);
  $listDays[$dateArr] = $dateArr;  
}

// Liste des blocs
$listBlocs = CGroups::loadCurrent()->loadBlocs();
$bloc_id   = CValue::getOrSession("bloc_id", reset($listBlocs)->_id);
if(!key_exists($bloc_id, $listBlocs)) {
  $bloc_id = reset($listBlocs)->_id;
}

$bloc = new CBlocOperatoire();
$bloc->load($bloc_id);
$bloc->loadRefsSalles();
$nbAlertesInterv = count($bloc->loadRefsAlertesIntervs());

if (!$listSalles = $bloc->_ref_salles) {
  $listSalles = array();
}

// Cr�ation du tableau de visualisation
$affichages = array();
foreach($listDays as $keyDate=>$valDate){
  foreach($listSalles as $keySalle=>$valSalle){
    if ($valSalle->isLocked($valDate)) {
      $valSalle->_blocage[$valDate] = 1;
    }
    foreach(CPlageOp::$hours as $keyHours=>$valHours){
      foreach(CPlageOp::$minutes as $keyMins=>$valMins){
        // Initialisation du tableau
        $affichages["$keyDate-s$keySalle-$valHours:$valMins:00"] = "empty";
        $affichages["$keyDate-s$keySalle-HorsPlage"] = array();
      }
    }
  }
}

$listPlages         = array();
$operation          = new COperation();
$nbIntervHorsPlage  = 0;
$listPlage          = new CPlageOp();
$nbIntervNonPlacees = 0;

// Nombre d'interventions hors plage pour la semaine
$ljoin = array();
$ljoin["sejour"] = "sejour.sejour_id = operations.sejour_id";
$where = array();
$where["date"]            = "BETWEEN '$date' AND '$fin'";
$where["annulee"]         = "= '0'";
$where[]                  = "salle_id IS NULL OR salle_id ". CSQLDataSource::prepareIn(array_keys($listSalles));
$where["sejour.group_id"] = "= '".CGroups::loadCurrent()->_id."'";
$nbIntervHorsPlage = $operation->countList($where, null, $ljoin);

foreach($listDays as $keyDate => $valDate){
  
  // R�cup�ration des plages par jour
  $where = array();
  $where["date"]     = "= '$keyDate'";
  $where["salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));
  $order             = "debut";
  $listPlages[$keyDate] = $listPlage->loadList($where,$order);
  
  // R�cup�ration des interventions hors plages du jour
  $where = array();
  $where["date"]     = "= '$keyDate'";
  $where["annulee"]   = "= '0'";
  $where["salle_id"]        = CSQLDataSource::prepareIn(array_keys($listSalles));
  $order = "time_operation";
  $horsPlages = $operation->loadList($where,$order);
  
  // D�termination des bornes du semainier
  $min = CPlageOp::$hours_start.":".reset(CPlageOp::$minutes).":00";
  $max = CPlageOp::$hours_stop.":".end(CPlageOp::$minutes).":00";
  
  // D�termination des bornes de chaque plage
  foreach($listPlages[$keyDate] as $plage){
    $plage->loadRefsFwd();
    $plage->loadRefsNotes();
    $plage->_ref_chir->loadRefsFwd();
    $plage->getNbOperations();
    $nbIntervNonPlacees += $plage->_nb_operations - $plage->_nb_operations_placees;
   $plage->loadAffectationsPersonnel();
  
    $plage->fin = min($plage->fin, $max);
    $plage->debut = max($plage->debut, $min);
  
    $plage->updateFormFields();
    $plage->makeView();
  
    if($plage->debut >= $plage->fin){  
      unset($listPlages[$keyDate][$plage->_id]);
    }
  }
  
  // Remplissage du tableau de visualisation
  foreach($listPlages[$keyDate] as $plage){
    $plage->debut = mbTimeGetNearestMinsWithInterval($plage->debut, CPlageOp::$minutes_interval);
    $plage->fin   = mbTimeGetNearestMinsWithInterval($plage->fin  , CPlageOp::$minutes_interval);
    $plage->_nbQuartHeure = mbTimeCountIntervals($plage->debut, $plage->fin, "00:".CPlageOp::$minutes_interval.":00");
    for($time = $plage->debut; $time < $plage->fin; $time = mbTime("+".CPlageOp::$minutes_interval." minutes", $time) ){
      $affichages["$keyDate-s$plage->salle_id-$time"] = "full";
    } 
    $affichages["$keyDate-s$plage->salle_id-$plage->debut"] = $plage->_id;
  }
  // Ajout des interventions hors plage
  foreach($horsPlages as $_op) {
    if($_op->salle_id) {
      $affichages["$keyDate-s".$_op->salle_id."-HorsPlage"][$_op->_id] = $_op;
    }
  }
}

// Liste des Sp�cialit�s
$listSpec = new CFunctions();
$listSpec = $listSpec->loadSpecialites();

//Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("listPlages"        , $listPlages        );
$smarty->assign("listDays"          , $listDays          );
$smarty->assign("listBlocs"         , $listBlocs         );
$smarty->assign("bloc"              , $bloc              );
$smarty->assign("listSalles"        , $listSalles        );
$smarty->assign("listHours"         , CPlageOp::$hours   );
$smarty->assign("listMins"          , CPlageOp::$minutes );
$smarty->assign("affichages"        , $affichages        );
$smarty->assign("nbIntervNonPlacees", $nbIntervNonPlacees);
$smarty->assign("nbIntervHorsPlage" , $nbIntervHorsPlage );
$smarty->assign("nbAlertesInterv"   , $nbAlertesInterv   );
$smarty->assign("date"              , $date              );
$smarty->assign("listSpec"          , $listSpec          );

$smarty->display("vw_planning_week.tpl");
?>
