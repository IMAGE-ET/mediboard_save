<?php /* $Id: do_delivery_aed.php 6067 2009-04-14 08:04:15Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision: 6067 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

if (isset ($_POST['_code'])) {
	$stock = CProductStockGroup::getFromCode($_POST['_code']);
	if ($stock) {
		$_POST['stock_id'] = $stock->_id;
		$_POST['_code'] = null;
	}
}

if (isset ($_POST['product_id'])) {
  $product = new CProduct;
  $product->load($_POST['product_id']);
  if ($product->loadRefStock()) {
    $_POST["stock_id"] = $product->_ref_stock_group->_id;
    unset($_POST['product_id']);
  }
}

$do = new CDoObjectAddEdit('CProductDelivery', 'delivery_id');
$do->doIt();

?>