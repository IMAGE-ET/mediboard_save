<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$now       = mbDate();

$filter = new COperation;
$filter->_date_min = CValue::get("_date_min"    , "$now");
$filter->_date_max = CValue::get("_date_max"    , "$now");

$listBlocs = CGroups::loadCurrent()->loadBlocs(PERM_READ, null, "nom");
$bloc_id   = CValue::getOrSession("bloc_id", reset($listBlocs)->_id);

$typeAff = CValue::getOrSession("typeAff");

// R�cup�ration des salles
$salle = new CSalle();
$where["bloc_id"] = "= '$bloc_id'";
$salles = $salle->loadListWithPerms(PERM_READ, $where);

// R�cup�ration des op�rations
$ljoin = array();
$ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";

$where = array();
$where["plagesop.salle_id"] = CSQLDataSource::prepareIn(array_keys($salles));

$where["materiel"] = "!= ''";
$where["operations.plageop_id"] = "IS NOT NULL";
$where["plagesop.date"] = ">= '".mbDate("-7 day")."'";

$order = "plagesop.date, rank";

$operation = new COperation;

$where["commande_mat"] = "!= '1'";
$where["annulee"]      = "!= '1'";
$operations["0"] = $operation->loadList($where, $order, null, null, $ljoin);

$where["commande_mat"] = "= '1'";
$where["annulee"]      = "= '1'";
$operations["1"] = $operation->loadList($where, $order, null, null, $ljoin);

foreach($operations as &$_operations) {
  foreach($_operations as $_operation) {
    $_operation->loadRefPatient(1);
    $_operation->loadRefChir(1);
    $_operation->_ref_chir->loadRefFunction();
    $_operation->loadRefPlageOp(1);
    $_operation->loadExtCodesCCAM();
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("typeAff"	  , $typeAff);
$smarty->assign("filter"    , $filter);
$smarty->assign("bloc_id"   , $bloc_id);
$smarty->assign("listBlocs" , $listBlocs);
$smarty->assign("operations", $operations);

$smarty->display("vw_idx_materiel.tpl");

?>