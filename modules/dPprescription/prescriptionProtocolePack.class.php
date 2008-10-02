<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

class CPrescriptionProtocolePack extends CMbObject {
  // DB Table key
  var $prescription_protocole_pack_id = null;
  
  // DB Fields
  var $libelle      = null;
  var $praticien_id = null;  // Pack associ� � un praticien
  var $function_id  = null;  // Pack associ� � un cabinet
  var $object_class = null;
  
  // FwdRefs
  var $_ref_praticien = null;
  var $_ref_function  = null;
  
  // BackRefs
  var $_ref_protocole_pack_items = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prescription_protocole_pack';
    $spec->key   = 'prescription_protocole_pack_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["praticien_id"]  = "ref class|CMediusers";
    $specs["function_id"]   = "ref class|CFunctions";  
    $specs["libelle"]       = "str";
    $specs["object_class"]  = "notNull enum list|CSejour|CConsultation";
    return $specs;
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["prescription_protocole_pack_items"] = "CPrescriptionProtocolePackItem prescription_protocole_pack_id";
    return $backRefs;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_view = $this->libelle;
  }
  
  /*
   * Chargement des item de packs (protocoles)
   */
  function loadRefsPackItems(){
    $this->_ref_protocole_pack_items = $this->loadBackRefs("prescription_protocole_pack_items");
  }
  
  function loadRefsBack(){
    parent::loadRefsBack();
    $this->loadRefsPackItems();
  }

  function loadRefPraticien(){
    $this->_ref_praticien = new CMediusers();
    $this->_ref_praticien->load($this->praticien_id);  
  }
  
  function loadRefFunction(){
    $this->_ref_function = new CFunctions();
    $this->_ref_function->load($this->function_id);
  }

  function loadRefsFwd(){
    parent::loadRefsFwd();
    $this->loadRefPraticien();
    $this->loadRefFunction();
  }
}

?>