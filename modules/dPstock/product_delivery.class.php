<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien M�nager
 */

class CProductDelivery extends CMbObject {
  // DB Table key
  var $delivery_id  = null;

  // DB Fields
  var $stock_id       = null;
  var $date_dispensation = null;
  var $quantity       = null;
  var $code           = null; // Lot number, lapsing date
  var $service_id     = null;
  var $patient_id     = null;

  // Object References
  //    Single
  var $_ref_stock     = null;
  var $_ref_service   = null;
  
  var $_ref_delivery_traces = null;
  
  var $_date_min      = null;
  var $_date_max      = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_delivery';
    $spec->key   = 'delivery_id';
    return $spec;
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    $specs['stock_id']          = 'notNull ref class|CProductStockGroup';
    $specs['date_dispensation'] = 'notNull dateTime';
    $specs['quantity']          = 'notNull num';
    $specs['service_id']        = 'notNull ref class|CService';
    $specs['patient_id']        = 'ref class|CPatient';
    $specs['_date_min']         = 'notNull dateTime';
    $specs['_date_max']         = 'notNull dateTime moreThan|_date_min';
    return $specs;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_view = $this->quantity.'x '.$this->_ref_stock->_view.($this->service_id?" pour le service '{$this->_ref_service->_view}'":'');
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs['delivery_traces'] = 'CProductDeliveryTrace delivery_id';
    return $backRefs;
  }
  
  function isDelivered() {
  	$this->loadRefsBack();
  	$sum = 0;
  	foreach ($this->_ref_delivery_traces as $trace) {
  		if ($trace->date_delivery)
  		  $sum += $trace->quantity;
  	}
  	return ($sum >= $this->quantity);
  }
  
  function isReceived() {
    $this->loadRefsBack();
    $sum = 0;
    foreach ($this->_ref_delivery_traces as $trace) {
      if ($trace->date_reception)
        $sum += $trace->quantity;
    }
    return ($sum >= $this->quantity);
  }
  
  function loadRefsBack(){
    $this->_ref_delivery_traces = $this->loadBackRefs('delivery_traces');
  }

  function loadRefStock(){
    $this->_ref_stock = new CProductStockGroup();
    $this->_ref_stock->load($this->stock_id);
  }
  
  function loadRefService(){
  	$this->_ref_service = new CService();
    $this->_ref_service->load($this->service_id);  
  }
  
  function loadRefPatient(){
    $this->_ref_patient = new CPatient();
    $this->_ref_patient->load($this->patient_id);	
  }
  
  function loadRefsFwd() {
    $this->loadRefStock();
    $this->loadRefService();
    $this->loadRefPatient();
  }

  function getPerm($permType) {
    if(!$this->_ref_stock || !$this->_ref_service) {
      $this->loadRefsFwd();
    }
    if ($this->_ref_service) {
      return ($this->_ref_stock->getPerm($permType) && $this->_ref_service->getPerm($permType));
    } else {
      return ($this->_ref_stock->getPerm($permType));
    }
  }
}
?>