<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CProductDeliveryTrace extends CMbObject {
  // DB Table key
  var $delivery_trace_id  = null;

  // DB Fields
  var $delivery_id    = null;
  var $date_delivery  = null;
  var $date_reception = null;
  var $code           = null;
  var $quantity       = null;
  var $target_location_id = null;

  /**
   * @var CProductDelivery
   */
  var $_ref_delivery  = null;
  
  /**
   * @var CProductStockLocation
   */
  var $_ref_target_location = null;
  
  var $_date_min      = null;
  var $_date_max      = null;
  
  var $_deliver       = null;
  var $_undeliver     = null;
  var $_receive       = null;
  var $_unreceive     = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_delivery_trace';
    $spec->key   = 'delivery_trace_id';
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['echanges_phast']  = 'CPhastEchange object_id';
    return $backProps;
  }
  
  function getProps() {
    $specs = parent::getProps();
    $specs[$this->_spec->key] .= " show|1";
    $specs['delivery_id']    = 'ref notNull class|CProductDelivery';
    $specs['code']           = 'str maxLength|32';
    $specs['quantity']       = 'num notNull';
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
    $negative_allowed = CAppUI::conf('dPstock CProductStockGroup negative_allowed') == '1';
    
    $stock_service = new CProductStockService();
    $stock_service->product_id = $stock->product_id;
    $stock_service->object_id = $this->_ref_delivery->service_id;
    $stock_service->object_class = "CService"; // XXX
    //$stock_service->location_id = $this->target_location_id;
    $stock_service->loadMatchingObject();
    
    if ($this->date_delivery && 
        !$negative_allowed && 
        !$infinite_group_stock && 
        (($this->quantity == 0) || ($stock->quantity < $this->quantity))) {
      $unit = $stock->_ref_product->_unit_title ? $stock->_ref_product->_unit_title : $stock->_ref_product->_view;
      return "Impossible de d�livrer ce nombre de $unit";
    }

    // Un-deliver
    if ($this->_undeliver) {
      $this->_undeliver = null;
      return $this->delete();
    }
     
    // If we want to deliver, just provide a delivery date
    if ($this->date_delivery && !$infinite_group_stock) {
      $stock->quantity -= $this->quantity;
      if ($msg = $stock->store()) return $msg;
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
        if ($msg = $stock_service->store()) return $msg;
      }
    }
    
    // dispensation nominative
    if (!$this->_ref_delivery->patient_id) {
      if (!$stock_service->_id) {
        $stock_service->quantity = $this->quantity;
        $stock_service->order_threshold_min = 0;
      }
      
      if ($this->_ref_delivery->service_id) {
        if ($msg = $stock_service->store()) return $msg;
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
      if ($msg = $stock->store()) return $msg;
    }
    
    if ($stock_service->_id && $this->date_reception /* && CAppUI::conf('dPstock CProductStockService infinite_quantity') == 0*/) {
      $stock_service->quantity -= $this->quantity;
      if ($msg = $stock_service->store()) return $msg;
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
  function loadRefPreparateur(){
    return $this->loadLastLog()->loadRefUser(false)->loadRefMediuser();
  }
}
?>