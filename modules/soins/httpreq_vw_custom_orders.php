<?php /* $Id: httpreq_vw_restockages_service_list.php 6146 2009-04-21 14:40:08Z alexis_granger $ */

/**
 *	@package Mediboard
 *	@subpackage soins
 *	@version $Revision: 6146 $
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$service_id = CValue::get('service_id');

$delivery = new CProductDelivery;
$where = array(
  "service_id"  => "= '$service_id'",
  "stock_id"    => "IS NULL",
  "stock_class" => "= 'CProductStockGroup'",
);

/** @var CProductDelivery[] $deliveries */
$deliveries = $delivery->loadList($where, "date_dispensation DESC", 50);

foreach ($deliveries as $_delivery) {
  $_delivery->loadRefStock();
  $_delivery->_ref_stock->loadRefsFwd();
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign('deliveries', $deliveries);
$smarty->display('inc_vw_custom_orders.tpl');
