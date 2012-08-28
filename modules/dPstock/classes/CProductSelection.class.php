<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage stock
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

class CProductSelection extends CMbObject {
  // DB Table key
  var $selection_id = null;

  // DB Fields
  var $name         = null;

  // Object References
  //    Multiple
  var $_ref_items   = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "product_selection";
    $spec->key   = "selection_id";
    $spec->uniques["name"] = array("name");
    return $spec;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["selection_items"] = "CProductSelectionItem selection_id";
    return $backProps;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs["name"] = "str notNull seekable";
    return $specs;
  }

  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = $this->name;
  }

  function loadRefsBack() {
    $this->loadRefsItems();
  }
  
  function loadRefsItems() {
    $ljoin = array(
      "product" => "product.product_id = product_selection_item.product_id"
    );
    return $this->_ref_items = $this->loadBackRefs("selection_items", "product.name", null, null, $ljoin);
  }
}
