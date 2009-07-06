<?php /* $Id: httpreq_vw_deliveries_list.php 6146 2009-04-21 14:40:08Z alexis_granger $ */

/**
 *	@package Mediboard
 *	@subpackage pharmacie
 *	@version $Revision: 6146 $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsRead();

$service_id = mbGetValueFromGetOrSession('service_id');

// Calcul de date_max et date_min
$date_min = mbGetValueFromGetOrSession('_date_min');
$date_max = mbGetValueFromGetOrSession('_date_max');
mbSetValueToSession('_date_min', $date_min);
mbSetValueToSession('_date_max', $date_max);

$order_by = 'date_dispensation DESC';
$where = array (
 'order' => " = '1'"
);

//$where[] = "date_dispensation BETWEEN '$date_min 00:00:00' AND '$date_max 23:59:59'";
$where['quantity'] = " > 0";
$delivery = new CProductDelivery();
$deliveries = $delivery->loadList($where, $order_by, 20);

$stocks_service = array();
foreach($deliveries as $_delivery) {
  $stocks_service[$_delivery->_id] = CProductStockService::getFromCode($_delivery->_ref_stock->_ref_product->code, $service_id);
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign('deliveries', $deliveries);
$smarty->assign('stocks_service', $stocks_service);
$smarty->display('inc_orders_list.tpl');

?>