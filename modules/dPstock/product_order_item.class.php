<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPtock
 *	@version $Revision$
 *  @author Fabien M�nager
 */

class CProductOrderItem extends CMbObject {
  // DB Table key
  var $order_item_id      = null;

  // DB Fields
  var $reference_id       = null;
  var $order_id           = null;
  var $quantity           = null;
  var $unit_price         = null; // In the case the reference price changes

  // Object References
  //    Single
  var $_ref_order         = null;
  var $_ref_reference     = null;
  
  //    Multiple
  var $_ref_receptions    = null;

  // Form fields
  var $_price             = null;
  var $_date_received     = null;
  var $_quantity_received = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_order_item';
    $spec->key   = 'order_item_id';
    return $spec;
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    $specs['reference_id']       = 'notNull ref class|CProductReference';
    $specs['order_id']           = 'notNull ref class|CProductOrder';
    $specs['quantity']           = 'notNull num pos';
    $specs['unit_price']         = 'currency';
    $specs['_price']             = 'currency';
    $specs['_quantity_received'] = 'num';
    return $specs;
  }

  function receive($quantity, $code = null) {
    $this->loadRefsFwd();
    
    if ($this->_id) {
      $reception = new CProductOrderItemReception();
      $reception->order_item_id = $this->_id;
      $reception->quantity = $quantity;
      $reception->date = mbDateTime();
      $reception->code = $code;
      return $reception->store();
    } else {
      return $this->_spec->class.'::receive failed : order_item must be stored before';
    }
  }
  
  function isReceived() {
    $this->updateReceived();
  	return ($this->_quantity_received >= $this->quantity);
  }
  
  function getStock() {
    if (!$this->_ref_order || $this->_ref_reference) {
      $this->loadRefsFwd();
    }
    $stock = new CProductStockGroup();
    $where = array();
    $where['group_id']   = "= '{$this->_ref_order->group_id}'";
    $where['product_id'] = "= '{$this->_ref_reference->product_id}'";
    $stock->loadObject($where);
    
    return $stock;
  }
  
  function updateReceived() {
    $this->loadRefsBack();
    $quantity = 0;
    foreach ($this->_ref_receptions as $reception) {
      $quantity += $reception->quantity;
    }
    $this->_quantity_received = $quantity;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->updateReceived();

    $this->loadReference();
    $this->_view = $this->_ref_reference->_view;
    $this->_price = $this->unit_price * $this->quantity;
  }
  
  function loadReference() {
    $this->_ref_reference = new CProductReference();
    $this->_ref_reference->load($this->reference_id);
  }
  
  function loadOrder() {
    $this->_ref_order = new CProductOrder();
    $this->_ref_order->load($this->order_id);
  }

  function loadRefsFwd() {
    parent::loadRefsFwd();

    $this->loadReference();
    $this->loadOrder();
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs['receptions'] = 'CProductOrderItemReception order_item_id';
    return $backRefs;
  }
  
  function loadRefsBack() {
    $this->_ref_receptions = $this->loadBackRefs('receptions', 'date DESC');
  }

  function store() {
    global $AppUI;
    
    $this->loadRefsFwd();
    
    if($this->order_id && $this->reference_id && !$this->_id) {
      $where['order_id']     = "= '$this->order_id'";
      $where['reference_id'] = "= '$this->reference_id'";

      $duplicateKey = new CProductOrderItem();
      
      if($duplicateKey->loadObject($where)) {
        $duplicateKey->loadRefsFwd();
        $this->_id = $duplicateKey->_id;
        $this->quantity += $duplicateKey->quantity;
        $this->unit_price = $duplicateKey->unit_price;
      } else {
        $this->unit_price = $this->_ref_reference->price;
      }
    }
    
    if ($stock = $this->getStock()) {
      $stock->loadRefOrders();
      if ($stock->_zone_future > 2) {
        $AppUI->setMsg('Attention : le stock optimum risque d\'�tre d�pass�', UI_MSG_WARNING);
      }
    }
    
    return parent::store();
  }
}
?>