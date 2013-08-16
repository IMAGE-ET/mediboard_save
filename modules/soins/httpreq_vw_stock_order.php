<?php /* $Id: httpreq_vw_restockages_service_list.php 6146 2009-04-21 14:40:08Z alexis_granger $ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision: 6146 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$service_id          = CValue::get('service_id');
$start               = intval(CValue::getOrSession('start', 0));
$letter              = CValue::getOrSession("letter", "");
$only_service_stocks = CValue::getOrSession('only_service_stocks', 1);
$only_common         = CValue::getOrSession('only_common');
$keywords            = CValue::getOrSession('keywords');
$endowment_id        = CValue::get('endowment_id');
$endowment_item_id   = CValue::get('endowment_item_id');

$service = new CService();
$service->load($service_id);
$service->loadBackRefs("endowments");

if (($endowment_id === null) && count($service->_back["endowments"])) {
  $first = reset($service->_back["endowments"]);
  $endowment_id = $first->_id;
}

// Calcul de date_max et date_min
$date_min = CValue::get('_date_min');
$date_max = CValue::get('_date_max');

if (!$date_min) {
  $date_min = CValue::session('_date_delivrance_min', CMbDT::date("-1 DAY"));
}
if (!$date_max) {
  $date_max = CValue::session('_date_delivrance_max', CMbDT::date());
}

CValue::setSession('_date_delivrance_min', $date_min);
CValue::setSession('_date_delivrance_max', $date_max);

CValue::setSession('endowment_id', $endowment_id);

$delivrance = new CProductDelivery();
$delivrance->_date_min = $date_min;
$delivrance->_date_max = $date_max;

$group_id = CProductStockGroup::getHostGroup();
$single_line = false;
$limit = "$start,25";

$where = array(
  "product.name " . ($letter === "#" ? "RLIKE '^[^A-Z]'" : "LIKE '$letter%'")
);

if ($endowment_id) {
  /** @var CProductEndowmentItem[] $endowment_items */

  // Toute la liste
  if (!$endowment_item_id) {
    $endowment_item = new CProductEndowmentItem;

    $where["product_endowment_item.endowment_id"] = "= '$endowment_id'";
    $where["product_endowment_item.cancelled"] = "= '0'";
    $ljoin = array(
      'product' => 'product.product_id = product_endowment_item.product_id'
    );
    if ($keywords) {
      $where['product.name'] = $endowment_item->_spec->ds->prepareLike("%$keywords%");
    }

    $endowment_items = $endowment_item->seek($keywords, $where, $limit, true, $ljoin, 'product.name');
    $count_stocks = $endowment_item->_totalSeek;
  }
  // Seulement une ligne
  else {
    $single_line = true;
    $endowment_item = new CProductEndowmentItem;
    $endowment_item->load($endowment_item_id);
    $endowment_items = array($endowment_item->_id => $endowment_item);
    $count_stocks = 1;
  }

  $stocks = array();
  foreach ($endowment_items as $_item) {
    $_item->loadRefsFwd();
    $stock_service = CProductStockService::getFromProduct($_item->_ref_product, $service);

    $stock = new CProductStockGroup;
    $stock->product_id = $_item->_ref_product->_id;
    $stock->group_id = $group_id;
    $stock->loadMatchingObject();
    $stock->updateFormFields();
    $stock->_ref_stock_service = $stock_service;
    $stock->quantity = $_item->quantity;
    $stock->_endowment_item_id = $_item->_id;
    $stocks[] = $stock;
  }
}
else if ($only_service_stocks == 1 || $only_common == 1) {
  $stock = new CProductStockService;

  $ljoin = array(
    'product' => 'product.product_id = product_stock_service.product_id'
  );
  $where['product_stock_service.object_id'] = "= '$service_id'";
  $where['product_stock_service.object_class'] = "= 'CService'"; // XXX
  if ($only_common) {
    $where['product_stock_service.common'] = "= '1'";
  }
  if ($keywords) {
    $where['product.name'] = $stock->_spec->ds->prepareLike("%$keywords%");
  }

  /** @var CProductStockService[] $stocks_service */
  $stocks_service = $stock->seek($keywords, $where, $limit, true, $ljoin, 'product.name');
  $count_stocks = $stock->_totalSeek;

  $stocks = array();
  if ($stocks_service) {
    foreach ($stocks_service as $_id => $stock_service) {
      if ($stock_service->_ref_product->cancelled) {
        continue;
      }

      //if (count($stocks) == 20) continue;
      $stock = new CProductStockGroup;
      $stock->product_id = $stock_service->_ref_product->_id;
      $stock->group_id = $group_id;

      if ($stock->loadMatchingObject()) {
        $stock->updateFormFields();
        $stock->_ref_stock_service = $stock_service;
        $stock->quantity = max(0, $stock_service->getOptimumQuantity() - $stock_service->quantity);
        $stocks[$stock->_id] = $stock;
      }
    }
  }
}
else {
  $where["product_stock_group.group_id"] = "= '$group_id'";
  $ljoin = array(
    'product' => 'product.product_id = product_stock_group.product_id'
  );

  $stock = new CProductStockGroup;

  /** @var CProductStockGroup[] $stocks */
  $stocks = $stock->seek($keywords, $where, $limit, true, $ljoin, 'product.name');
  $count_stocks = $stock->_totalSeek;

  if ($stocks) {
    foreach ($stocks as $_id => $_stock) {
      if ($_stock->_ref_product->cancelled) {
        unset($stocks[$_id]);
      }
      else {
        $_stock->quantity = min($_stock->quantity, $_stock->getOptimumQuantity());
      }
    }
  }
}

// Load the already ordered dispensations
foreach ($stocks as &$_stock) {
  $_stock->loadRefsFwd();

  $where = array(
    'product_delivery.date_dispensation' => "BETWEEN '$date_min 00:00:00' AND '$date_max 23:59:59'",
    'product_delivery.stock_class' => "= '$_stock->_class'",
    'product_delivery.stock_id'    => "= '$_stock->_id'",
    'product_delivery.service_id'  => "= '$service_id'",
    //'product.category_id'          => "= '".CAppUI::conf('dPmedicament CBcbProduitLivretTherapeutique product_category_id')."'"
  );

  $ljoin = array(
    'product_stock_group' => 'product_delivery.stock_id = product_stock_group.stock_id',
    'product' => 'product.product_id = product_stock_group.product_id',
  );

  $delivery = new CProductDelivery;
  $_stock->_ref_deliveries = $delivery->loadList($where, 'date_dispensation', null, null, $ljoin);

  $_stock->_total_quantity = 0;

  foreach ($_stock->_ref_deliveries as $_deliv) {
    $_stock->_total_quantity += $_deliv->quantity;
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign('single_line', $single_line);
$smarty->assign('start', $start);
$smarty->assign('letter', $letter);
$smarty->assign('stocks', $stocks);
$smarty->assign('count_stocks', $count_stocks);
$smarty->assign('delivrance', $delivrance);
$smarty->assign('keywords', $keywords);
$smarty->assign('service', $service);
$smarty->assign('only_service_stocks', $only_service_stocks);
$smarty->assign('only_common', $only_common);
$smarty->assign('endowment_id', $endowment_id);

$smarty->display('inc_stock_order.tpl');
