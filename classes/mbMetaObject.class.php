<?php

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author Alexis Granger
*/

/**
 * Class CMbMetaObject 
 * @abstract Adds Mediboard abstraction layer functionality for meta-objects
 */

global $AppUI;

global $AppUI;
require_once($AppUI->getSystemClass("mbobject"));

class CMbMetaObject extends CMbObject{
  // DB Fields	
  var $object_id    = null;
  var $object_class = null;

  // Object References
  var $_ref_object       = null;
	
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["object_id"]    = "notNull ref class|CMbObject meta|object_class";
    $specs["object_class"] = "notNull str maxLength|25";
    return $specs;
  }
    
  function setObject($object) {
    $this->_ref_object = $object;
    $this->object_id = $object->_id;
    $this->object_class = $object->_class_name;
  }
  
  /**
   * Load target of meta object
   */
  function loadTargetObject() {
    if ($this->_ref_object) {
      return;
    }
    
    if (!$this->object_class) {
      return;
    }
    
    if (!class_exists($this->object_class)) {
      trigger_error("Unable to create instance of '$this->object_class' class", E_USER_ERROR);
      return;
    }

  	$this->_ref_object = new $this->object_class;
    if (!$this->_ref_object->load($this->object_id)) {
      $this->_ref_object->load(null);
      $this->_ref_object->_view = "Element supprim�";
    }
  }
    
  function loadRefsFwd() {	
    parent::loadRefsFwd();
    $this->loadTargetObject();
  }  
}


?>