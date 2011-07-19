<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPstock
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CProductCategory extends CMbObject {
  // DB Table key
  var $category_id   = null;
  
  // DB fields
  var $name          = null;
	
	var $_count_products = null;

  // Object References
  //    Multiple
  var $_ref_products = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'product_category';
    $spec->key   = 'category_id';
    return $spec;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['products'] = 'CProduct category_id';
    return $backProps;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs['name'] = 'str notNull maxLength|50 seekable show|0';
    $specs['_count_products'] = 'num show|1';
    return $specs;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->name;
  }
	
	function loadView(){
		parent::loadView();
		
		$this->countProducts();
	}

  function loadRefsBack() {
    $this->_ref_products = $this->loadBackRefs('products');
  }
  
  function countProducts(){
    $this->_count_products = $this->countBackRefs("products");
  }
}
?>