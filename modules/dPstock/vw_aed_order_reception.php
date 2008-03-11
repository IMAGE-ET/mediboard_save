<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien M�nager
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$order_id    = mbGetValueFromGetOrSession('order_id');

// Loads the expected Order
$order = new CProductOrder();

if ($order_id) {
  $order->load($order_id);
  $order->updateFormFields();
  $order->loadRefsFwd();
}

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('order', $order);

$smarty->display('vw_aed_order_reception.tpl');
?>
