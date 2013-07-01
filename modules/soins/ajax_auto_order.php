<?php /* $Id: do_delivery_aed.php 6067 2009-04-14 08:04:15Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: 6067 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$service_id = CValue::get('service_id');
$date_min   = CValue::get('date_min');
$date_max   = CValue::get('date_max');

$service = new CService();
$orders  = array();

if ($service->load($service_id) && $date_min && $date_max) {
  $stocks = $service->loadBackRefs('product_stock_services');
  
  if ($stocks)
  foreach($stocks as $stock) {
    $stock->loadRefsFwd();
    $stock_group = CProductStockGroup::getFromCode($stock->_ref_product->code);

    $target_quantity = $stock->order_threshold_optimum ? $stock->order_threshold_optimum : $stock->order_threshold_max;
      
    if (CAppUI::conf('dPstock CProductStockService infinite_quantity') != 1) {
      $effective_quantity = $stock->quantity;
      
      $where = array(
        'product_delivery.date_dispensation' => "BETWEEN '$date_min 00:00:00' AND '$date_max 23:59:59'",
        'product_delivery.stock_id'    => " = '$stock_group->_id'",
        'product_delivery.stock_class' => " = '$stock_group->_class'",
        'product.category_id'          => " = '".CAppUI::conf('dPmedicament CBcbProduitLivretTherapeutique product_category_id')."'"
      );

      $ljoin = array(
        'product_stock_group' => 'product_delivery.stock_id = product_stock_group.stock_id',
        'product' => 'product.product_id = product_stock_group.product_id',
      );

      $delivery   = new CProductDelivery;
      $deliveries = $delivery->loadList($where, null, null, null, $ljoin);
      
      foreach ($deliveries as $delivery) {
        if ($delivery->order == 1 && $delivery->quantity > 0) {
          $effective_quantity += $delivery->quantity;
        }
      }
    
      if ($target_quantity > $effective_quantity) {
        // This the GROUP stock!
        $orders[$stock_group->_id] = $target_quantity - $effective_quantity;
      }
    }
    else {
      $orders[$stock_group->_id] = $target_quantity;
    }
  }
}

echo json_encode($orders);
CApp::rip();
