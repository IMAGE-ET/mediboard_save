<?php 
/* $Id: httpreq_vw_list_categories.php 9329 2010-07-01 12:48:40Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage dmi
 * @version $Revision: 9329 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */
 
CCanDo::checkRead();

$barcode = CValue::get("barcode");

$parsed = CBarcodeParser::parse($barcode);
$comp = $parsed['comp'];
$strict = false;

$products = array();
$lots = array();

$lot = isset($comp['lot']) ? $comp['lot'] : trim($comp['raw'], ".%\n\r\t +-");
$dmi_category_id = CAppUI::conf("dmi CDMI product_category_id");

$object = new CProductOrderItemReception;
$where = array(
  "product_order_item_reception.cancelled" => "= '0'",
  "(product_order_item_reception.lapsing_date > '".mbDate()."' OR product_order_item_reception.lapsing_date IS NULL)",
  "(product_order_item_reception.code != '' AND product_order_item_reception.code IS NOT NULL)",
  "product.category_id" => "= '$dmi_category_id'",
);

$ljoin = array(
  "product_order_item" => "product_order_item_reception.order_item_id = product_order_item.order_item_id",
  "product_reference"  => "product_order_item.reference_id = product_reference.reference_id",
  "product"            => "product_reference.product_id = product.product_id",
);

if (isset($comp['id'])) {
  $lot = $object->load($comp['id']);
  $lots = array($lot->_id => $lot);
}
else {
  $lots = $object->seek($lot, $where, 50, null, $ljoin);
}

foreach($lots as $_id => $_lot) {
  if($_lot->getUsedQuantity() >= $_lot->quantity) {
    $_lot->loadRefOrderItem();
    $_lot->_ref_order_item->loadReference();
    
    $_dmi = new CDMI;
    $_dmi->product_id = $_lot->_ref_order_item->_ref_reference->product_id;
    
    if ($_dmi->loadMatchingObject() && $_dmi->type !== "deposit") {
      unset($lots[$_id]); 
      continue;
    }
  }
  else {
    $_lot->loadRefOrderItem();
    $_lot->_ref_order_item->loadReference();
  }
  
  $_lot->_ref_order_item->_ref_reference->loadRefProduct();
  $product = $_lot->_ref_order_item->_ref_reference->_ref_product;
  
  $strict = true;
  
  if (!isset($products[$product->_id])) {
    $product->_lots = array(
      $_lot->_id => $_lot,
    );
    $products[$product->_id] = $product;
  }
  else 
    $products[$product->_id]->_lots[$_lot->_id] = $_lot;
    
  $products[$product->_id]->_strict = true;
}

//if ( empty($products) ) {
  $object = new CProduct;
  
  $keys = array("scc_prod", "ref", "cip", "raw");
  $values = array_intersect_key($comp, array_flip($keys));
  
  $where = array(
    "product.category_id" => "= '$dmi_category_id'",
  );
  foreach ($values as $field => $value) {
    if (!$value) continue;
    $products += $object->seek($value, $where, 50);
  }
  
  $reception = new CProductOrderItemReception;
  
  $where = array(
    "product.category_id" => "= '$dmi_category_id'",
    "dmi.product_id" => "IS NOT NULL",
    "product_order_item_reception.code != '' AND product_order_item_reception.code IS NOT NULL",
    "product_order_item_reception.cancelled" => "= '0'",
  );
  
  $ljoin['dmi'] = "dmi.product_id = product.product_id";
  
  foreach ($products as $_product) {
    if (isset($_product->_strict)) continue;
    
    $where["product.product_id"] = "= '$_product->_id'";
    $_product->loadRefsFwd();
    $_product->_lots = $reception->loadList($where, null, null, null, $ljoin);
  }
//}

foreach ($products as $_id_product => $_product) {
  foreach($_product->_lots as $_id => $_lot) {
    // on n'affiche pas les lots consomm�s
    $consumed = ($_lot->getUsedQuantity() >= $_lot->quantity);

    if ($consumed) {
      unset($_product->_lots[$_id]);
      continue;
    }

    $_lot->loadRefOrderItem()->loadReference();

    // Si lot consomm�
//    if($consumed) {
//      $_dmi = new CDMI;
//      $_dmi->product_id = $_product->_id;
//      
//      if ($_dmi->loadMatchingObject() && $_dmi->type !== "deposit") {
//        unset($_product->_lots[$_id]);
//        
//        // Si tous les lots du produit sont consomm�s
//        /*if (count($_product->_lots) == 0) {
//          unset($products[$_id_product]);
//          continue 2;
//        }*/
//        continue;
//      }
//    }
    $_lot->_selected = isset($comp['lot']) && $comp['lot'] && ($_lot->code === $comp['lot']);
  }
  
  $_product->loadRefsBack();
  foreach($_product->_ref_references as $_reference) {
    $_reference->loadRefSociete();
  }
  
  $lot = new CProductOrderItemReception;
  $_product->_new_lot = $lot;
}

$societe = new CSociete;
$list_societes = $societe->loadList(null, "name");

$smarty = new CSmartyDP();
$smarty->assign("parsed", $parsed);
$smarty->assign("strict", $strict);
$smarty->assign("products", $products);
$smarty->assign("list_societes", $list_societes);
$smarty->display("inc_parse_barcode.tpl");
