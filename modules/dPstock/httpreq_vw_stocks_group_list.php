<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$category_id         = CValue::get('category_id');
$stock_id            = CValue::getOrSession('stock_id');
$keywords            = CValue::get('keywords');
$start               = CValue::get('start');
$letter              = CValue::get('letter', "%");
$only_ordered_stocks = CValue::get('only_ordered_stocks');

CValue::setSession('category_id', $category_id);

$where = array();
$where["product_stock_group.group_id"] = "= '".CProductStockGroup::getHostGroup()."'";

if ($category_id) {
  $where['product.category_id'] = " = $category_id";
}

if ($keywords) {
  $where[] = "product.code LIKE '%$keywords%' OR 
              product.name LIKE '%$keywords%' OR 
              product.description LIKE '%$keywords%'";
}
$where["product.name"] = ($letter === "#" ? "RLIKE '^[^A-Z]'" : "LIKE '$letter%'");

$orderby = 'product.name ASC';

$leftjoin = array();
$leftjoin['product'] = 'product.product_id = product_stock_group.product_id'; // product to stock

if ($only_ordered_stocks) {
  $where['product_order.cancelled'] = '= 0'; // order not cancelled
  $where['product_order.deleted']   = '= 0'; // order not deleted
  $where['product_order.date_ordered'] = 'IS NOT NULL'; // order not deleted
  $where['product_order_item.renewal'] = '= 1'; // renewal line
  $leftjoin['product_reference']    = 'product_reference.product_id = product_stock_group.product_id'; // stock to reference
  $leftjoin['product_order_item']   = 'product_order_item.reference_id = product_reference.reference_id'; // reference to order item
  $leftjoin['product_order']        = 'product_order.order_id = product_order_item.order_id'; // order item to order
  $where[] = 'product_order_item.order_item_id NOT IN (
    SELECT product_order_item.order_item_id FROM product_order_item
    LEFT JOIN product_order_item_reception ON product_order_item_reception.order_item_id = product_order_item.order_item_id
    LEFT JOIN product_order ON product_order.order_id = product_order_item.order_id
    WHERE product_order.deleted = 0 AND product_order.cancelled = 0
    HAVING SUM(product_order_item_reception.quantity) < product_order_item.quantity
  )';
}

$stock = new CProductStockGroup();
$list_stocks = $stock->loadList($where, $orderby, intval($start).",".CAppUI::conf("dPstock CProductStockGroup pagination_size"), "product_stock_group.stock_id", $leftjoin);

foreach($list_stocks as $_stock) {
  $_stock->_ref_product->getPendingOrderItems(false);
}

if (!$only_ordered_stocks)
  $list_stocks_count = $stock->countList($where, null, $leftjoin);
else 
  $list_stocks_count = count($stock->loadList($where, null, null, "product_stock_group.stock_id", $leftjoin));

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('stock',             $stock);
$smarty->assign('stock_id',          $stock_id);
$smarty->assign('list_stocks',       $list_stocks);
$smarty->assign('list_stocks_count', $list_stocks_count);
$smarty->assign('start',             $start);

$smarty->display('inc_stocks_list.tpl');
?>
