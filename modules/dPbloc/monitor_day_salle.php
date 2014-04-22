<?php

/**
 * dPbloc
 *
 * @category Bloc
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::read();

$listBlocs = CGroups::loadCurrent()->loadBlocs();
$listSalles = array();
foreach ($listBlocs as $_bloc) {
  $listSalles = $listSalles + $_bloc->loadRefsSalles();
}

$salle = new CSalle();
$salle->load(CValue::get("salle_id"), reset($listSalles)->_id);
$salle->loadRefBloc();

$date = CValue::get("date", CMbDT::date());

// Liste des jours
$listDays = array();
for ($i = 0; $i < 19*7; $i += 7) {
  $dateArr = CMbDT::date("+$i day", $date);
  $listDays[$dateArr] = $dateArr;  
}

// Cr�ation du tableau de visualisation
$affichages = array();
foreach ($listDays as $keyDate=>$valDate) {
  foreach (CPlageOp::$hours as $keyHours=>$valHours) {
    foreach (CPlageOp::$minutes as $keyMins=>$valMins) {
      // Initialisation du tableau
      $affichages["$keyDate-$valHours:$valMins:00"] = "empty";
      $affichages["$keyDate-HorsPlage"] = array();
    }
  }
}

$listPlages         = array();
$operation          = new COperation();
$nbIntervHorsPlage  = 0;
$listPlage          = new CPlageOp();
$nbIntervNonPlacees = 0;

foreach ($listDays as $keyDate => $valDate) {
  // R�cup�ration des plages par jour
  $where = array();
  $where["date"]     = "= '$keyDate'";
  $where["salle_id"] = "= '$salle->_id'";
  $order             = "debut";
  $listPlages[$keyDate] = $listPlage->loadList($where, $order);
  
  // R�cup�ration des interventions hors plages du jour
  $where = array();
  $where["date"]      = "= '$keyDate'";
  $where["annulee"]   = "= '0'";
  $where["salle_id"]  = "= '$salle->_id'";
  $order = "time_operation";
  /** @var COperation[] $horsPlages */
  $horsPlages = $operation->loadList($where, $order);
  
  // D�termination des bornes du semainier
  $min = CPlageOp::$hours_start.":".reset(CPlageOp::$minutes).":00";
  $max = CPlageOp::$hours_stop.":".end(CPlageOp::$minutes).":00";
  
  // D�termination des bornes de chaque plage
  foreach ($listPlages[$keyDate] as $plage) {
    /** @var CPlageOp $plage */
    $plage->loadRefsFwd();
    $plage->loadRefsNotes();
    $plage->_ref_chir->loadRefsFwd();
    $plage->multicountOperations();
    $nbIntervNonPlacees += $plage->_count_operations - $plage->_count_operations_placees;
    $plage->loadAffectationsPersonnel();
  
    $plage->fin = min($plage->fin, $max);
    $plage->debut = max($plage->debut, $min);
  
    $plage->updateFormFields();
    $plage->makeView();
  
    if ($plage->debut >= $plage->fin) {
      unset($listPlages[$keyDate][$plage->_id]);
    }
  }
  
  // Remplissage du tableau de visualisation
  foreach ($listPlages[$keyDate] as $plage) {
    $plage->debut = CMbDT::timeGetNearestMinsWithInterval($plage->debut, CPlageOp::$minutes_interval);
    $plage->fin   = CMbDT::timeGetNearestMinsWithInterval($plage->fin  , CPlageOp::$minutes_interval);
    $plage->_nbQuartHeure = CMbDT::timeCountIntervals($plage->debut, $plage->fin, "00:".CPlageOp::$minutes_interval.":00");
    for ($time = $plage->debut; $time < $plage->fin; $time = CMbDT::time("+".CPlageOp::$minutes_interval." minutes", $time) ) {
      $affichages["$keyDate-$time"] = "full";
    } 
    $affichages["$keyDate-$plage->debut"] = $plage->_id;
  }

  // Ajout des interventions hors plage
  foreach ($horsPlages as $_op) {
    if ($_op->salle_id) {
      $affichages["$keyDate-HorsPlage"][$_op->_id] = $_op;
    }
  }
}

//Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("listPlages"        , $listPlages        );
$smarty->assign("listDays"          , $listDays          );
$smarty->assign("listBlocs"         , $listBlocs         );
$smarty->assign("salle"             , $salle             );
$smarty->assign("listHours"         , CPlageOp::$hours   );
$smarty->assign("listMins"          , CPlageOp::$minutes );
$smarty->assign("affichages"        , $affichages        );
$smarty->assign("date"              , $date              );

$smarty->display("monitor_day_salle.tpl");