<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExClassFieldTranslation extends CMbObject {
  var $ex_class_field_translation_id = null;
  
  var $ex_class_field_id = null;
  var $lang = null;
  
  var $std   = null;
  var $desc  = null;
  var $court = null;
  
  var $_ref_ex_class_field = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class_field_translation";
    $spec->key   = "ex_class_field_translation_id";
    $spec->uniques["lang"] = array("ex_class_field_id", "lang");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["ex_class_field_id"] = "ref notNull class|CExClassField cascade";
    $props["lang"]  = "enum list|fr|en"; // @todo: en fonction des repertoires
    $props["std"]   = "str";
    $props["desc"]  = "str";
    $props["court"] = "str";
    return $props;
  }
  
  function getKey(){
    $field = $this->loadRefExClassField();
    $class = $field->loadRefExClass();
    return "CExObject_{$class->_id}-{$field->name}";
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    
    global $locales;
    $key = $this->getKey();
    $locales[$key] = $this->std;
    $locales["{$key}-desc"]  = $this->desc  ? $this->desc  : $this->std;
    $locales["{$key}-court"] = $this->court ? $this->court : $this->std;
    
    $this->_view = $this->std;
  }
	
	function fillIfEmpty($str) {
    if (!$this->_id) {
      $this->std = $this->desc = $this->court = $str;
			$this->updateFormFields();
      $this->std = $this->desc = $this->court = "";
    }
	}
  
  function loadRefExClassField($cache = true){
    return $this->_ref_ex_class_field = $this->loadFwdRef("ex_class_field_id", $cache);
  }
}
