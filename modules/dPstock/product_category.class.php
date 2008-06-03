<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPstock
 *	@version $Revision: $
 *  @author Fabien M�nager
 */

class CProductCategory extends CMbObject {
  // DB Table key
  var $category_id   = null;
  
  // DB fields
  var $name          = null;

  // Object References
  //    Multiple
  var $_ref_products = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_category';
    $spec->key   = 'category_id';
    return $spec;
  }

  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs['products'] = 'CProduct category_id';
    return $backRefs;
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    return array_merge($specs, array (
      'name' => 'notNull str maxLength|50',
    ));
  }

  function getSeeks() {
    return array (
      'name' => 'like',
    );
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsBack();
    $this->_view = $this->name.' ('.count($this->_ref_products).' articles)';
  }

  function loadRefsBack() {
    $this->_ref_products = $this->loadBackRefs('products');
  }
}
?>