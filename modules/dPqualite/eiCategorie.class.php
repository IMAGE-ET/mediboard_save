<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision: $
 *  @author S�bastien Fillonneau
 */

/**
 * The CEiCategorie class
 */
class CEiCategorie extends CMbObject {
  // DB Table key
  var $ei_categorie_id  = null;
    
  // DB Fields
  var $nom              = null;

  // Object References
  var $_ref_items       = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'ei_categories';
    $spec->key   = 'ei_categorie_id';
    return $spec;
  }
  
  function getBackRefs() {
      $backRefs = parent::getBackRefs();
      $backRefs["items"] = "CEiItem ei_categorie_id";
     return $backRefs;
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "nom" => "notNull str maxLength|50"
    );
    return array_merge($specsParent, $specs);
  }
  
  function loadRefsBack() {
    $this->_ref_items = new CEiItem;
    $where = array();
    $where["ei_categorie_id"] = "= '$this->ei_categorie_id'";
    $order = "nom ASC";
    $this->_ref_items = $this->_ref_items->loadList($where, $order);
  }
  
}
?>