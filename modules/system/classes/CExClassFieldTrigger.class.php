<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExClassFieldTrigger extends CMbObject {
  var $ex_class_field_trigger_id = null;
  
  var $ex_class_field_id = null;
  var $ex_class_triggered_id = null;
  var $trigger_value = null;
  
  var $_ref_ex_class_field = null;
  var $_ref_ex_class_triggered = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class_field_trigger";
    $spec->key   = "ex_class_field_trigger_id";
    $spec->uniques["ex_class_triggered"] = array("ex_class_field_id", "ex_class_triggered_id");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["ex_class_field_id"] = "ref notNull class|CExClassField";
    $props["ex_class_triggered_id"]  = "ref notNull class|CExClass";
    $props["trigger_value"]  = "str notNull";
    return $props;
  }
  
  function loadView(){
    parent::loadView();
    $this->loadRefExClassField();
    $this->loadRefExClassTriggered();
    $this->_view = $this->_ref_ex_class_field->_view." > ".$this->_ref_ex_class_triggered->_view;
  }
  
  function loadRefExClassField($cache = true){
    return $this->_ref_ex_class_field = $this->loadFwdRef("ex_class_field_id", $cache);
  }
  
  function loadRefExClassTriggered($cache = true){
    return $this->_ref_ex_class_triggered = $this->loadFwdRef("ex_class_triggered_id", $cache);
  }
}
