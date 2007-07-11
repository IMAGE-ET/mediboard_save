<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/
 
global $AppUI, $can, $m, $g;

$can->needsRead();

$now       = mbDate();

$filter = new CMateriel;
$filter->_date_min = mbGetValueFromGet("_date_min"    , "$now");
$filter->_date_max = mbGetValueFromGet("_date_max"    , "$now");

$typeAff = mbGetValueFromGetOrSession("typeAff");

// R�cup�ration des op�rations
$ljoin = array();
$ljoin["plagesop"] = "operations.plageop_id = plagesop.plageop_id";

$where = array();

$salle = new CSalle;
$whereSalle = array();
$whereSalle["group_id"] = "= '$g'";
$where["plagesop.salle_id"] = db_prepare_in(array_keys($salle->loadListWithPerms(PERM_READ, $whereSalle)));

$where["materiel"] = "!= ''";
$where["operations.plageop_id"] = "IS NOT NULL";
$where["commande_mat"] = $typeAff ? "= '1'" : "!= '1'";
$where["annulee"]      = $typeAff ? "= '1'" : "!= '1'";

$order = array();
$order[] = "plagesop.date";
$order[] = "rank";

$op = new COperation;
$op = $op->loadList($where, $order, null, null, $ljoin);
foreach($op as $key => $value) {
  $op[$key]->loadRefsFwd();
  $op[$key]->_ref_sejour->loadRefsFwd();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("typeAff"	, $typeAff);
$smarty->assign("filter"    , $filter);
$smarty->assign("op"     	, $op);

$smarty->display("vw_idx_materiel.tpl");

?>