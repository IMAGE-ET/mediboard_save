<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExClassHostField extends CMbObject {
  var $ex_class_host_field_id = null;
  
  var $ex_group_id = null;
  var $field = null;
	
  var $coord_label_x = null;
  var $coord_label_y = null;
  var $coord_value_x = null;
  var $coord_value_y = null;
  
  var $_ref_ex_group = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class_host_field";
    $spec->key   = "ex_class_host_field_id";
    $spec->uniques["coord_value"] = array("ex_group_id", "coord_value_x", "coord_value_y", "coord_label_x", "coord_label_y");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["ex_group_id"] = "ref notNull class|CExClassFieldGroup cascade";
    $props["field"]       = "str notNull canonical";
    
    $props["coord_value_x"] = "num min|0 max|100";
    $props["coord_value_y"] = "num min|0 max|100";
    $props["coord_label_x"] = "num min|0 max|100";
    $props["coord_label_y"] = "num min|0 max|100";
    return $props;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
		
		$this->_view = $this->field; // FIXME
  }
  
  function loadRefExGroup($cache = true){
    return $this->_ref_ex_group = $this->loadFwdRef("ex_group_id", $cache);
  }
  
  function updatePlainFields(){
    // If we change its group, we need to reset its coordinates
    if ($this->fieldModified("ex_group_id")) {
      $this->coord_title_x = "";
      $this->coord_title_y = "";
      $this->coord_text_x = "";
      $this->coord_text_y = "";
    }
    
    return parent::updatePlainFields();
  }
}
