<?php

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author S�bastien Fillonneau
*/

class CExamNyha extends CMbObject {
  // DB Table key
  var $examnyha_id = null;
  
  // DB References
  var $consultation_id = null;
  
  // DB fields
  var $q1          = null;
  var $q2a         = null;
  var $q2b         = null;
  var $q3a         = null;
  var $q3b         = null;
  var $hesitation  = null;
  
  // Fwd References
  var $_ref_consult = null;
  
  // Form fields
  var $_classeNyha  = null;
  
  function CExamNyha() {
    $this->CMbObject("examnyha", "examnyha_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    return array (
      "consultation_id" => "notNull ref class|CConsultation",
      "q1"              => "bool",
      "q2a"             => "bool",
      "q2b"             => "bool",
      "q3a"             => "bool",
      "q3b"             => "bool",
      "hesitation"      => "notNull bool"
    );
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_classNyha = "";
    if($this->q1 == 1){
      if($this->q2a !== null && $this->q2a == 0){
        $this->_classeNyha = "Classe III";
      }
      if($this->q2a == 1 && $this->q2b !== null && $this->q2b == 1){
        $this->_classeNyha = "Classe I";
      }
      if($this->q2a == 1 && $this->q2b !== null && $this->q2b == 0){
        $this->_classeNyha = "Classe II";
      }
    }
    if($this->q1 == 0){
      if($this->q3a !== null && $this->q3a == 0){
        $this->_classeNyha = "Classe III";
      }
      if($this->q3a == 1 && $this->q3b !== null && $this->q3b == 1){
        $this->_classeNyha = "Classe III";
      }
      if($this->q3a == 1 && $this->q3b !== null && $this->q3b == 0){
        $this->_classeNyha = "Classe IV";
      }
    }
    
    $this->_view = "Classification NYHA : $this->_classeNyha"; 
  }
  
  function loadRefsFwd() {
    $this->_ref_consult = new CConsultation;
    $this->_ref_consult->load($this->consultation_id);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_consult) {
      $this->loadRefsFwd();
    }
    return $this->_ref_consult->getPerm($permType);
  }
}
?>