<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsRead();

$product_id = CValue::get('product_id');

$product = new CProduct();
$product->load($product_id);

if(!$product->_id) {
  CAppUI::stepAjax("Produit de code <strong>$product_id</strong> non trouv�", UI_MSG_ERROR);
}
else {
	$where = array(
    "product.product_id" => "= $product_id"
  );
  $leftjoin = array(
    "product_order_item" => "product_order_item_reception.order_item_id = product_order_item.order_item_id",
    "product_reference" => "product_order_item.reference_id = product_reference.reference_id",
    "product" => "product_reference.product_id=product.product_id"
  );
	
  //chargement de la reception
  $product_order_item_reception = new CProductOrderItemReception();
  $list = $product_order_item_reception->loadList($where,null,null,null,$leftjoin);
  foreach ($list as $_poir) {
  	$_poir->loadRefsFwd();
  }
}

if(count($list) > 0) {
  foreach ($list as $_id => $_reception) {
    $remaining = $_reception->getQuantity() - $_reception->countBackRefs("lines_dmi");
    if ($remaining < 1) {
      unset($list[$_id]);
    }
  }
  
  if(count($list) == 0) {
    CAppUI::stepAjax("Tous les articles <strong>$product->_view</strong> sont d�j� consomm�s", UI_MSG_WARNING);
  }
}
else {
	CAppUI::stepAjax("Aucun article enregistr� pour le produit <strong>$product->_view</strong>", UI_MSG_WARNING);
}

$reference = new CProductReference;
$reference->product_id = $product->_id;
$list_references = $reference->loadMatchingList();

$list_societes = array();

if (count($list_references)) {
  foreach($list_references as $_reference) {
    $_reference->loadRefSociete();
  }
}
else {
  $societe = new CSociete;
  $list_societes = $societe->loadList(null, "name");
}

$smarty = new CSmartyDP();
$smarty->assign("list", $list);
$smarty->assign("list_references", $list_references);
$smarty->assign("list_societes", $list_societes);
$smarty->assign("product", $product);
$smarty->display('inc_search_product_order_item_reception.tpl');
