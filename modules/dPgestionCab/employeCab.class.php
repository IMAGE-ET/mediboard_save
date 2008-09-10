<?php /* $Id: */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision: $
* @author Romain Ollivier
*/

/**
 * The CGestionCab Class
 */
class CEmployeCab extends CMbObject {
  // DB Table key
  var $employecab_id = null;
  
  // DB References
  var $function_id = null;

  // DB Fields
  var $nom      = null;
  var $prenom   = null;
  var $function = null;
  var $adresse  = null;
  var $cp       = null;
  var $ville    = null;

  // Object References
  var $_ref_function = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'employecab';
    $spec->key   = 'employecab_id';
    return $spec;
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["params_paie"] = "CParamsPaie employecab_id";
    return $backRefs;
  }
  
  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["function_id"] = "notNull ref class|CFunctions";
    $specs["nom"]         = "notNull str";
    $specs["prenom"]      = "notNull str";
    $specs["function"]    = "notNull str";
    $specs["adresse"]     = "text confidential";
    $specs["ville"]       = "str";
    $specs["cp"]          = "numchar length|5 confidential";
    return $specs;
  }
  
  function getSeeks() {
    return array (
      "nom"    => "likeBegin",
      "prenom" => "likeBegin"
    );
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = "$this->nom $this->prenom";
  }

  // Forward references
  function loadRefsFwd() {
    // fonction (cabinet)
    $this->_ref_function = new CFunctions();
    $this->_ref_function->load($this->function_id);
  }
  
  function getPerm($permType) {
    if(!$this->_ref_function) {
      $this->loadRefsFwd();
    }
    return ($this->_ref_function->getPerm($permType));
  }
}

?>