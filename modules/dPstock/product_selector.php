<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Stock
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$product_id  = CValue::getOrSession('product_id', null);

$product = new CProduct();
$category_id = 0;
if ($product->load($product_id)) {
  $product->loadRefsFwd();
  $category_id = $product->_ref_category->_id;
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign('selected_product',  $product->_id);
$smarty->assign('selected_category', $category_id);

$smarty->display('product_selector.tpl');

