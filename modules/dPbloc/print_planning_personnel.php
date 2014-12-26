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

CCanDo::checkRead();

$datetime_min = CValue::get("_datetime_min");
$datetime_max = CValue::get("_datetime_max");
$salle_id     = CValue::get("salle_id");
$bloc_id      = CValue::get("_bloc_id");
$prat_id      = CValue::get("_prat_id");
$specialite   = CValue::get("_specialite");

if (is_array($bloc_id)) {
  CMbArray::removeValue("0", $bloc_id);
}

$plage = new CPlageOp();
$where = array();

$where["date"] = "BETWEEN '".CMbDT::date($datetime_min)."' AND '".CMbDT::date($datetime_max)."'";

if (!$prat_id && !$specialite) {
  $function = new CFunctions;
  $user = CMediusers::get();
  if (!$user->isFromType(array("Anesth�siste"))) {
    $functions  = $function->loadListWithPerms(PERM_READ);
    $praticiens = $user->loadPraticiens();
  }
  else {
    $functions  = $function->loadList();
    $praticiens = $praticien->loadList();
  }
  $where[] = "plagesop.chir_id ".CSQLDataSource::prepareIn(array_keys($praticiens)) .
             " OR plagesop.spec_id ".CSQLDataSource::prepareIn(array_keys($functions));
}

if ($prat_id) {
  $where["chir_id"] = "= '$prat_id'";
}

if ($specialite) {
  $where["spec_id"] = "= '$specialite'";
}

$salle = new CSalle();
$whereSalle = array();
$whereSalle["bloc_id"] = CSQLDataSource::prepareIn(
  count($bloc_id) ?
  $bloc_id :
  array_keys(CGroups::loadCurrent()->loadBlocs(PERM_READ))
);

if ($salle_id) {
  $whereSalle["sallesbloc.salle_id"] = "= '$salle_id'";
}
$listSalles = $salle->loadListWithPerms(PERM_READ, $whereSalle);

$where["salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));
$order = "date, salle_id, debut";

/** @var CPlageOp[] $plages */
$plages = $plage->loadList($where, $order);

$planning   = array();
$personnels = array();

// @todo : g�rer les hors plages

foreach ($plages as $_plage) {
  $affectations = $_plage->loadAffectationsPersonnel();

  /** @var COperation[] $operations */
  $operations = $_plage->loadRefsOperations(0);
  
  CMbObject::massLoadFwdRef($operations, "plageop_id");
  $praticiens = CMbObject::massLoadFwdRef($operations, "chir_id");
  CMbObject::massLoadFwdRef($praticiens, "function_id");
  
  foreach ($operations as $key => $_operation) {
    $_operation->loadRefPlageOp();
    if ($_operation->_datetime_best < $datetime_min ||
      $_operation->_datetime_best > $datetime_max) {
      unset($operations[$key]);
      continue;
    }
    $_operation->loadRefPatient();
    $_operation->loadRefChir()->loadRefFunction();
    $_operation->updateSalle();
  }
  
  if (count($affectations)) {
    foreach ($affectations as $_affectations_by_type) {
      foreach ($_affectations_by_type as $_affectation) {
        $_affectation->loadRefPersonnel()->loadRefUser();
        
        if (!isset($planning[$_affectation->personnel_id])) {
          $planning[$_affectation->personnel_id] = array();
          $personnels[$_affectation->personnel_id] = $_affectation->_ref_personnel;
        }
        if (!isset($planning[$_affectation->personnel_id][$_plage->date]) && count($operations)) {
          $planning[$_affectation->personnel_id][$_plage->date] = array();
        }
        if (count($operations)) {
          $planning[$_affectation->personnel_id][$_plage->date] = $operations;
        }
      }
    }
  }
  foreach ($operations as $_operation) {
    // Personnels ajout�s
    $affectations = $_operation->loadAffectationsPersonnel();
    
    if (count($affectations)) {
      foreach ($affectations as $_affectations_by_type) {
        foreach ($_affectations_by_type as $_affectation) {
          $_affectation->loadRefPersonnel()->loadRefUser();
          
          if (!isset($planning[$_affectation->personnel_id])) {
            $planning[$_affectation->personnel_id] = array();
            $personnels[$_affectation->personnel_id] = $_affectation->_ref_personnel;
          }
        }
      }
    }
  }
}

// Trier le planning par personnel
$sorter = CMbArray::pluck($personnels, "_ref_user", "_view");
array_multisort($sorter, SORT_ASC, $personnels);

array_multisort(array_keys($personnels), SORT_ASC, $planning);

$smarty = new CSmartyDP;

$smarty->assign("datetime_min", $datetime_min);
$smarty->assign("datetime_max", $datetime_max);
$smarty->assign("planning"    , $planning);
$smarty->assign("personnels"  , $personnels);

$smarty->display("print_planning_personnel.tpl");
