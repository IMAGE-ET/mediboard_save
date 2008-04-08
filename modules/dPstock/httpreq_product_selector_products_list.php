<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPstock
 *  @version $Revision: $
 *  @author Fabien M�nager
 */
 
global $AppUI;

$category_id      = mbGetValueFromGet('category_id');
$keywords         = mbGetValueFromGet('keywords');
$selected_product = mbGetValueFromGet('selected_product');

$product = new CProduct();
$category = new CProductCategory();
$total = null;
$count = null;
$where_or = array();

if ($keywords) {
	$where = array();
	$where_or[] = "`name` LIKE '%$keywords%'";
	$where_or[] = "`description` LIKE '%$keywords%'";
	$where_or[] = "`code` LIKE '%$keywords%'";
	$where[] = implode(' OR ', $where_or);
	
  $list_products = $product->loadList($where, 'name', 20);
  $total = $product->countList($where);
} else {
	if ($category_id == 0) {
	  $list_products = $product->loadList(null, 'name');
	} else if ($category_id == -1) {
	  $list_products = array();
	} else {
	  $category->load($category_id);
	  $category->loadRefsBack();
	  $list_products = $category->_ref_products;
	  $total = count($list_products);
	}
}
$count = count($list_products);
if ($total == $count) $total = null;

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('list_products', $list_products);
$smarty->assign('selected_product', $selected_product);
$smarty->assign('count', $count);
$smarty->assign('total', $total);

$smarty->display('inc_product_selector_products_list.tpl');
?>
