<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision$
 *  @author Fabien M�nager
 */

global $can, $g;

$can->needsAdmin();

$stock_id         = mbGetValueFromGetOrSession('stock_id');
$category_id      = mbGetValueFromGetOrSession('category_id');
$service_id       = mbGetValueFromGetOrSession('service_id');

// Loads the stock 
$stock_service = new CProductStockService();

// If stock_id has been provided, we load the associated product
if ($stock_id) {
  $stock_service->stock_id = $stock_id;
  $stock_service->loadMatchingObject();
  $stock_service->loadRefsFwd();
  $stock_service->_ref_product->loadRefsFwd();
}
$stock_service->updateFormFields();

// Categories list
$list_categories = new CProductCategory();
$list_categories = $list_categories->loadList(null, 'name');

// Functions list
$where = array('group_id' => "= $g");
$list_services = new CService();
$list_services = $list_services->loadList($where, 'nom');

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign('stock_service', $stock_service);

$smarty->assign('category_id', $category_id);
$smarty->assign('service_id',  $service_id);

$smarty->assign('list_categories', $list_categories);
$smarty->assign('list_services',   $list_services);

$smarty->display('vw_idx_stock_service.tpl');

?>