<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/

class CListeChoix extends CMbObject {
  // DB Table key
  var $liste_choix_id = null;

  // DB References
  var $chir_id     = null; // not null when associated to a user
  var $function_id = null; // not null when associated to a function

  // DB fields
  var $nom             = null;
  var $valeurs         = null;
  var $compte_rendu_id = null;
  
  // Form fields
  var $_valeurs = null;
  var $_new     = null;
  var $_del     = null;
  
  // Referenced objects
  var $_ref_chir     = null;
  var $_ref_function = null;
  var $_ref_modele   = null;

  function CListeChoix() {
    $this->CMbObject("liste_choix", "liste_choix_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
    return array (
      "chir_id"         => "ref|xor|function_id",
      "function_id"     => "ref",
      "nom"             => "str|notNull",
      "valeurs"         => "text|confidential",
      "compte_rendu_id" => "ref"
    );
  }
  
  function check() {
    if ($this->chir_id and $this->function_id) {
      return "Une liste ne peut pas appartenir � la fois � une fonction et un utilisateur";
    }
    if (!($this->chir_id or $this->function_id)) {
      return "Une liste doit appertenir � un utilisateur ou � une fonction";
    }
  }
  
  function loadRefsFwd() {
    $this->_ref_chir = new CMediusers;
    $this->_ref_chir->load($this->chir_id);
    $this->_ref_function = new CFunctions;
    $this->_ref_function->load($this->function_id);
    $this->_ref_modele = new CCompteRendu;
    $this->_ref_modele->load($this->compte_rendu_id);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    if($this->valeurs != "")
      $this->_valeurs = explode("|", $this->valeurs);
    else
      $this->_valeurs = array();
    natcasesort($this->_valeurs);
  }
  
  function updateDBFields() {
    if($this->_new !== null) {
      $this->updateFormFields();
      $this->_valeurs[] = $this->_new;
      natcasesort($this->_valeurs);
      $this->valeurs = implode("|", $this->_valeurs);
    }
    if($this->_del !== null) {
      $this->updateFormFields();
      foreach($this->_valeurs as $key => $value) {
        if($this->_del == $value)
          unset($this->_valeurs[$key]);
      }
      $this->valeurs = implode("|", $this->_valeurs);
    }
  }
  
  function getPerm($permType) {
    if(!($this->_ref_chir || $this->_ref_function)) {
      $this->loadRefsFwd();
    }
    if($this->_ref_chir->_id) {
      return $this->_ref_chir->getPerm($permType);
    } else {
      return $this->_ref_function->getPerm($permType);
    }
  }
}

?>