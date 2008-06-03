<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision: $
 *  @author S�bastien Fillonneau
 */

/**
 * The CEiItem class
 */
class CEiItem extends CMbObject {
  // DB Table key
  var $ei_item_id = null;
    
  // DB Fields
  var $ei_categorie_id  = null;
  var $nom              = null;

  // Object References
  var $_ref_categorie   = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'ei_item';
    $spec->key   = 'ei_item_id';
    return $spec;
  }
  
  function updateFormFields() {
    $this->_view = $this->nom;
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "ei_categorie_id" => "notNull ref class|CEiCategorie",
      "nom"             => "notNull str maxLength|50"
    );
    return array_merge($specsParent, $specs);
  }
  
  function loadRefsFwd() {
    $this->_ref_categorie = new CEiCategorie;
    $this->_ref_categorie->load($this->ei_categorie_id);
  }
}
?>