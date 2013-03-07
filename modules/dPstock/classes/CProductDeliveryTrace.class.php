<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage stock
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

class CProductDeliveryTrace extends CMbObject {
  public $delivery_trace_id;

  // DB Fields
  public $delivery_id;
  public $date_delivery;
  public $date_reception;
  public $code;
  public $quantity;
  public $target_location_id;

  /** @var CProductDelivery */
  public $_ref_delivery;

  /** @var CMediusers */
  public $_ref_preparateur;

  /** @var CProductStockLocation */
  public $_ref_target_location;

  public $_date_min;
  public $_date_max;

  public $_deliver;
  public $_undeliver;
  public $_receive;
  public $_unreceive;
  public $_datetime_min;
  public $_code_cis;
  public $_code_cip;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_delivery_trace';
    $spec->key   = 'delivery_trace_id';
    return $spec;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['echanges_phast']  = 'CExchangePhast object_id';
    return $backProps;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs[$this->_spec->key] .= " show|1";
    $specs['delivery_id']    = 'ref notNull class|CProductDelivery cascade';
    $specs['code']           = 'str maxLength|32';

    $type = (CProductStock::$allow_quantity_fractions ? "float" : "num");
    $specs['quantity']       = "$type notNull";

    $specs['date_delivery']  = 'dateTime';
    $specs['date_reception'] = 'dateTime';
    $specs['target_location_id'] = 'ref class|CProductStockLocation'; // can be null if nominative
    $specs['_date_min']      = 'dateTime notNull';
    $specs['_date_max']      = 'dateTime notNull moreThan|_date_min';
    return $specs;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_ref_delivery->updateFormFields();
    $this->_view = $this->_ref_delivery->_view;
  }

  function store() {
    $this->completeField('delivery_id', 'quantity');

    $this->loadRefsFwd();
    $stock = $this->getStock();
    $stock->loadRefsFwd();

    $infinite_group_stock = CAppUI::conf('dPstock CProductStockGroup infinite_quantity') == '1';
    $negative_allowed     = CAppUI::conf('dPstock CProductStockGroup negative_allowed')  == '1';

    $stock_service = new CProductStockService();
    $stock_service->product_id = $stock->product_id;
    $stock_service->object_id = $this->_ref_delivery->service_id;
    $stock_service->object_class = "CService"; // XXX
    //$stock_service->location_id = $this->target_location_id;
    $stock_service->loadMatchingObject();

    if ($this->date_delivery && !$negative_allowed && !$infinite_group_stock &&
        (($this->quantity == 0) || ($stock->quantity < $this->quantity))) {
      $unit = $stock->_ref_product->_unit_title ? $stock->_ref_product->_unit_title : $stock->_ref_product->_view;
      return "Impossible de d�livrer ce nombre de $unit";
    }

    // Un-deliver
    if ($this->_undeliver) {
      $this->_undeliver = null;

      // If we can't delete (it has a back ref or something else: interop, etc)
      if ($msg = $this->delete()) {
        $this->quantity = 0;
        return parent::store();
      }

      return;
    }

    // If we want to deliver, just provide a delivery date
    if ($this->date_delivery && !$infinite_group_stock) {
      $stock->quantity -= $this->quantity;
      if ($msg = $stock->store()) {
        return $msg;
      }
    }

    // Un-receive
    else if ($this->_unreceive) {
      if ($stock_service->_id/* && CAppUI::conf('dPstock CProductStockService infinite_quantity') == 0*/) {
        $stock_service->quantity -= $this->quantity;
      }

      $this->_unreceive = null;
      $this->date_reception = '';
    }

    // If we want to receive, just provide a reception date
    // if not dispensation nominative
    if ($this->date_reception && !$this->_ref_delivery->patient_id) {

    /*if (!$this->_ref_delivery->patient_id && (
          !$this->_id && $this->date_reception || $this->fieldModified("date_reception")
        )) {*/

      // If a stock already exist, its quantity is updated
      if ($stock_service->_id) {
        $stock_service->quantity += $this->quantity;
      }

      // if not, the stock is created
      else {
        $stock_service->order_threshold_min = abs($this->quantity) + 1;
        $stock_service->order_threshold_max = $stock_service->order_threshold_min * 2;
        $stock_service->quantity = $this->quantity;

        $default_location = CProductStockLocation::getDefaultLocation(
          $this->_ref_delivery->loadRefService(), 
          $stock->_ref_product
        );

        $stock_service->location_id = $default_location->_id;
      }

      if ($stock_service->object_id && $stock_service->object_class) {
        if ($msg = $stock_service->store()) {
          return $msg;
        }
      }
    }

    // dispensation nominative
    if (!$this->_ref_delivery->patient_id) {
      if (!$stock_service->_id) {
        $stock_service->quantity = $this->quantity;
        $stock_service->order_threshold_min = 0;
      }

      if ($this->_ref_delivery->service_id) {
        if ($msg = $stock_service->store()) {
          return $msg;
        }
      }
    }

    // Calcul du stock du sejour
    if (
        $this->_ref_delivery->sejour_id &&
        $this->_code_cis &&
        $this->_code_cip &&
        $this->_datetime_min &&
        CModule::getActive("pharmacie")
    ) {
      $stock_sejour = CStockSejour::getFromCIS($this->_code_cis, $this->_ref_delivery->sejour_id);

      // Mise a jour de la quantit� du stock en quantit� d'administration
      $ratio = CBcbProduit::get($this->_code_cip)->_ratio_cis_cip;
      $quantity = $this->quantity / $ratio;

      // Mise � jour du stock
      if ($stock_sejour->_id) {
        $count_quantity = $stock_sejour->countQuantityForDates($this->_datetime_min);
        $quantite = $stock_sejour->quantite + $quantity - $count_quantity;
      }

      // Cr�ation du stock s�jour
      else {
        $quantite = $this->quantity;
      }

      $stock_sejour->quantite = round($quantite, 4);
      $stock_sejour->datetime = $this->_datetime_min;

      if ($msg = $stock_sejour->store()) {
        return $msg;
      }
    }

    return parent::store();
  }

  function delete(){
    $this->completeField('delivery_id', 'quantity', 'date_delivery', 'date_reception');

    $this->loadRefsFwd();
    $stock = $this->getStock();
    $stock->loadRefsFwd();

    $infinite_group_stock = CAppUI::conf('dPstock CProductStockGroup infinite_quantity') == '1';

    $stock_service = new CProductStockService();
    $stock_service->product_id = $stock->product_id;
    $stock_service->object_id = $this->_ref_delivery->service_id;
    $stock_service->object_class = "CService"; // XXX
    $stock_service->loadMatchingObject();

    if (!$infinite_group_stock && $this->date_delivery) {
      $stock->quantity += $this->quantity;
      if ($msg = $stock->store()) {
        return $msg;
      }
    }

    if ($stock_service->_id && $this->date_reception /* && CAppUI::conf('dPstock CProductStockService infinite_quantity') == 0*/) {
      $stock_service->quantity -= $this->quantity;
      if ($msg = $stock_service->store()) {
        return $msg;
      }
    }

    // Calcul du stock du sejour
    if ($this->_code_cis && $this->_ref_delivery->sejour_id && CModule::getActive("pharmacie")) {
      $stock_sejour = CStockSejour::getFromCIS($this->_code_cis, $this->_ref_delivery->sejour_id);

      if ($stock_sejour->_id) {
        $codes_cip = CMbArray::pluck(CBcbProduit::getProduitsFromCIS($this->_code_cis), "CODE_CIP");

        $ds = $this->getDS();
        $where = array();
        $where["product.code"]               = $ds->prepareIn($codes_cip);
        $where["product_delivery.sejour_id"] = $ds->prepare("= '{$this->_ref_delivery->sejour_id}'");

        $ljoin = array();
        $ljoin["product_stock_group"] = "product_stock_group.stock_id = product_delivery.stock_id AND product_delivery.stock_class = 'CProductStockGroup'";
        $ljoin["product"]             = "product.product_id = product_stock_group.product_id";

        $delivery = new CProductDelivery();
        $delivery->loadObject($where, "product_delivery.date_dispensation DESC", null, $ljoin);

        // Si la delivrance actuelle est la derniere pour ce sejour et ce CIS
        if ($delivery->_id == $this->delivery_id) {
          // Mise a jour de la quantit� du stock en quantit� d'administration
          $code_cip = $this->_ref_delivery->loadRefStock()->loadRefProduct()->code;
          $product = CBcbProduit::get($code_cip);

          if ($product->code_cip) {
            $ratio = $product->_ratio_cis_cip;

            // Mise � jour du stock
            $stock_sejour->datetime = $this->_ref_delivery->datetime_min;
            $stock_sejour->quantite -= ($this->quantity / $ratio);

            $stock_sejour->quantite = round($stock_sejour->quantite, 4);

            if ($msg = $stock_sejour->store()) {
              return $msg;
            }
          }
        }
      }
    }

    return parent::delete();
  }

  /**
   * @return CProductStock
   */
  function getStock() {
    return $this->_ref_delivery->loadRefStock();
  }

  function loadRefsFwd() {
    $this->loadRefDelivery();
    $this->loadRefTargetLocation();
  }

  /**
   * @return CProductDelivery
   */
  function loadRefDelivery() {
    return $this->_ref_delivery = $this->loadFwdRef("delivery_id", true);
  }

  /**
   * @return CProductStockLocation
   */
  function loadRefTargetLocation() {
    return $this->_ref_target_location = $this->loadFwdRef("target_location_id", true);
  }

  function getPerm($permType) {
    return $this->getStock()->getPerm($permType);
  }

  /**
   * @return CMediusers
   */
  function loadRefPreparateur() {
    return $this->_ref_preparateur = $this->loadFirstLog()->loadRefUser()->loadRefMediuser();
  }
}
