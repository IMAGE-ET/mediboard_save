<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage pharmacie
 *  @version $Revision$
 *  @author Fabien M�nager
 */

global $can;

$can->needsRead();

$where = array();
$where['status'] = ' = "planned"';
$order_by = 'date DESC';
$delivery = new CProductDelivery();

$list_deliveries = $delivery->loadList($where, $order_by, 20);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign('list_deliveries',  $list_deliveries);

$smarty->display('inc_deliveries_list.tpl');

?>