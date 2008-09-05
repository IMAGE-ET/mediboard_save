<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPstock
 *  @version $Revision: $
 *  @author Fabien M�nager
 */

global $can;
$can->needsRead();

$order_by = 'date_dispensation DESC';
$delivery = new CProductDelivery();

$list_latest_deliveries = $delivery->loadList(null, $order_by, 20);

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign('list_latest_deliveries',  $list_latest_deliveries);
$smarty->display('inc_deliveries_list.tpl');

?>