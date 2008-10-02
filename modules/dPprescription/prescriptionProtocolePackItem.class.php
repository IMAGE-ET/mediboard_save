<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

class CPrescriptionProtocolePackItem extends CMbObject {
  // DB Table key
  var $prescription_protocole_pack_item_id = null;
  
  // DB Fields
  var $prescription_protocole_pack_id = null;
  var $prescription_id = null;
  
  // Object references
  var $_ref_prescription = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prescription_protocole_pack_item';
    $spec->key   = 'prescription_protocole_pack_item_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["prescription_protocole_pack_id"] = "notNull ref class|CPrescriptionProtocolePack";
    $specs["prescription_id"]                = "notNull ref class|CPrescription";
    return $specs;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->loadRefPrescription();
    $this->_view = $this->_ref_prescription->_view;
  }
  
  
  function loadRefPrescription(){
    $this->_ref_prescription = new CPrescription();
    $this->_ref_prescription->load($this->prescription_id);
  }
  
  function loadRefsFwd(){
    parent::loadRefsFwd();
    $this->loadRefPrescription();
  }
}

?>