<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
global $can;
$can->needsEdit();

$order_id    = CValue::getOrSession('order_id');
$category_id = CValue::getOrSession('category_id');
$societe_id  = CValue::getOrSession('societe_id');
$_autofill   = CValue::get('_autofill');

// Loads the expected Order
$order = new CProductOrder();
if (!$order->load($order_id)) {
  $order->group_id = CGroups::loadCurrent()->_id;
  if ($msg = $order->store()) {
    CAppUI::setMsg($msg);
  }
  
  CAppUI::redirect(CValue::read($_SERVER, "QUERY_STRING")."&order_id=$order->_id");
  return;
}
$order->updateFormFields();
if ($_autofill) {
  $order->autofill();
}

// Categories list
$category = new CProductCategory();
$list_categories = $category->loadList(null, 'name');

// Suppliers list
$societe = new CSociete();
$list_societes = $societe->loadList(null, 'name');

// Smarty template
$smarty = new CSmartyDP();

$smarty->assign('order',           $order);
$smarty->assign('_autofill',       $_autofill);

$smarty->assign('list_categories', $list_categories);
$smarty->assign('category_id',     $category_id);

$smarty->assign('list_societes',   $list_societes);
$smarty->assign('category_id',     $category_id);

$smarty->display('vw_aed_order.tpl');

?>