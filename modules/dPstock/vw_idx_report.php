<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien M�nager
 */

global $can, $g;

$can->needsRead();

$list_stocks = new CProductStock();

$where = array();
$where['group_id'] = " = '$g'";
$orderby = "quantity / order_threshold_max";
$list_stocks = $list_stocks->loadList($where, $orderby, 20);
foreach($list_stocks as $stock) {
	$stock->loadRefOrders();
}

$colors = array('#F00', '#FC3', '#1D6', '#06F', '#000');

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign('list_stocks', $list_stocks);
$smarty->assign('colors', $colors);

$smarty->display('vw_idx_report.tpl');

?>