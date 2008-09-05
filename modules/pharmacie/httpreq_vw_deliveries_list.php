<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage pharmacie
 *  @version $Revision$
 *  @author Fabien M�nager
 */

global $can;
$can->needsRead();

$service_id = mbGetValueFromGetOrSession('service_id');
$mode       = mbGetValueFromGetOrSession('mode');
$delivered  = mbGetValueFromGetOrSession('delivered') == 'true';

// Calcul de date_max et date_min
$date_min = mbGetValueFromGetOrSession('_date_min');
$date_max = mbGetValueFromGetOrSession('_date_max');
mbSetValueToSession('_date_min', $date_min);
mbSetValueToSession('_date_max', $date_max);

$order_by = 'date_dispensation DESC';
$where = array ();
if ($service_id) {
  $where['service_id'] = " = $service_id";
}
//$where['date_delivery'] = $delivered ? 'IS NOT NULL' : 'IS NULL';
$where[] = "date_dispensation BETWEEN '$date_min' AND '$date_max'";
$delivery = new CProductDelivery();
$deliveries = $delivery->loadList($where, $order_by, 20);

$deliveries_nominatif = array();
$deliveries_global = array();
$stocks_service = array();

// Creation d'un tableau de patient
$patients = array();
if (count($deliveries)) {
  foreach($deliveries as $_delivery){
  	if (!$delivered || ($delivered && $_delivery->isDelivered())) {
	    $_delivery->loadRefsFwd();
	    $_delivery->loadRefsBack();
	    $_delivery->_ref_stock->loadRefsFwd();
    
	    if($_delivery->patient_id){
	    	$_delivery->loadRefPatient();
	      $deliveries_nominatif[$_delivery->_id] = $_delivery;
	    } else {
	    	$_delivery->loadRefService();
	    	$deliveries_global[$_delivery->_id] = $_delivery;
	    }
      $stocks_service[$_delivery->_id] = CProductStockService::getFromCode($_delivery->_ref_stock->_ref_product->code, $service_id);
    }
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign('mode',    $mode);
$smarty->assign('deliveries_global',    $deliveries_global);
$smarty->assign('deliveries_nominatif', $deliveries_nominatif);
$smarty->assign('stocks_service',       $stocks_service);

$smarty->display('inc_deliveries_list.tpl');

?>