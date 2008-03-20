<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien M�nager
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$item_id = mbGetValueFromGet('item_id');

// Loads the expected Order Item
$item = new CProductOrderItem();
$item->load($item_id);
$item->loadRefs();

$item->_ref_reference->loadRefsFwd();

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('curr_item', $item);
$smarty->assign('order', $item->_ref_order);

$smarty->display('inc_order_item.tpl');
?>
