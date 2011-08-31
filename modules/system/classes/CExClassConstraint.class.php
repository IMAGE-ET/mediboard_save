<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExClassConstraint extends CMbObject {
  var $ex_class_constraint_id = null;
  
  var $ex_class_id   = null;
  var $field         = null;
  var $operator      = null;
  var $value         = null;
  
  var $_ref_ex_class = null;
  var $_ref_target_object = null;
  var $_ref_target_spec = null;
  
  var $_locale = null;
  var $_locale_desc = null;
  var $_locale_court = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class_constraint";
    $spec->key   = "ex_class_constraint_id";
    $spec->uniques["constraint"] = array("ex_class_id", "field", "value");
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["ex_class_id"] = "ref notNull class|CExClass";
    $props["field"]       = "str notNull";
    $props["operator"]    = "enum notNull list|=|!=|>|>=|<|<=|startsWith|endsWith|contains default|=";
    $props["value"]       = "str notNull";
    
    $props["_locale"]     = "str";
    $props["_locale_desc"]  = "str";
    $props["_locale_court"] = "str";
    return $props;
  }
  
  function resolveSpec($ref_object){
    $parts = explode("-", $this->field);
		
    if (count($parts) == 1) {
      $spec = $ref_object->_specs[$this->field];
    }
    else {
      $subparts = explode(".", $parts[0]);
      
      $_spec = $ref_object->_specs[$subparts[0]];
			
			if (count($subparts) > 1) {
				$class = $subparts[1];
			}
			else {
	      if (!$_spec->class) {
	        return;
	      }
				
				$class = $_spec->class;
			}
      
      $obj = new $class;
      $spec = $obj->_specs[$parts[1]];
    }
    
    return $spec;
  }
	
	function resolveObjectField($object){
    $parts = explode("-", $this->field);
    
    if (count($parts) == 1) {
      return array(
			  "object" => $object,
				"field"  => $parts[0],
			);
    }
    else {
      $subparts = explode(".", $parts[0]);
      $field = $subparts[0];
			
      $_spec = $object->_specs[$field];
      
      if (count($subparts) > 1) {
        $class = $subparts[1];
      }
      else {
        if (!$_spec->class) {
          return;
        }
        
        $class = $_spec->class;
      }
			
			return array(
			  "object" => $object->loadFwdRef($field),
				"field"  => $parts[1],
			);
    }
	}
  
  function loadTargetObject(){
    $this->loadRefExClass();
    $this->completeField("field", "value");
    
    if (!$this->_id) {
      return $this->_ref_target_object = new CMbObject;
    }
    
    $ref_object = new $this->_ref_ex_class->host_class;
    
    $spec = $this->resolveSpec($ref_object);
    
    if ($spec instanceof CRefSpec && $this->value && preg_match("/[a-z][a-z0-9_]+-[0-9]+/i", $this->value)) {
      $this->_ref_target_object = CMbObject::loadFromGuid($this->value);
    }
    else {
      // empty object
      $this->_ref_target_object = new CMbObject;
    }
    
    $this->_ref_target_spec = $spec;
    
    return $this->_ref_target_object;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    
    $this->loadRefExClass();
		
    $parts = explode("-", $this->field);
    $subparts = explode(".", $parts[0]);
		$host_class = $this->_ref_ex_class->host_class;
    
	  // first part
    if (count($subparts) > 1) {
      $this->_view = CAppUI::tr("$host_class-{$subparts[0]}")." de type ".CAppUI::tr("{$subparts[1]}");
    }
    // second part
    else {
    	if (count($parts) > 1) {
    		$this->_view = CAppUI::tr("$host_class-{$parts[0]}");
    	}
			else {
				$this->_view = CAppUI::tr("$host_class-{$this->field}");
			}
    }
		
		// 2 levels
    if (count($parts) > 1) {
      if (isset($subparts[1])) {
        $class = $subparts[1];
      }
			else {
				$host_object = new $host_class;
				$_spec = $host_object->_specs[$subparts[0]];
				$class = $_spec->class;
			}
			
      /*if ($_spec instanceof CRefSpec) {
      	$class = 
      }*/
			
			$this->_view .= " / ".CAppUI::tr("{$class}-{$parts[1]}");
    }
  }
  
  function checkConstraint(CMbObject $object) {
    $this->completeField("field", "value");
		
		$object_field = $this->resolveObjectField($object);
		
		if (!$object_field) return false;
		
    $object = $object_field["object"];
    $field  = $object_field["field"];
		
		// cas ou l'objet retrouv� n'a pas le champ (meta objet avec classe differente)
		if (!isset($object->_specs[$field])) {
			return false;
		}
		
    $value = $object->$field;
		
		if ($object->_specs[$field] instanceof CRefSpec) {
			$_obj = $object->loadFwdRef($field);
			$value = $_obj->_guid;
		}
		
    $cons = $this->value;
    
    // =|!=|>|>=|<|<=|startsWith|endsWith|contains default|=
    switch ($this->operator) {
      default:
      case "=": 
        if ($value == $cons) return true;
        break;
        
      case "!=": 
        if ($value != $cons) return true;
        break;
        
      case ">": 
        if ($value > $cons) return true;
        break;
        
      case ">=": 
        if ($value >= $cons) return true;
        break;
        
      case "<": 
        if ($value < $cons) return true;
        break;
        
      case "<=": 
        if ($value <= $cons) return true;
        break;
        
      case "startsWith": 
        if (strpos($value, $cons) === 0) return true;
        break;
        
      case "endsWith": 
        if (substr($value, -strlen($cons)) == $cons) return true;
        break;
        
      case "contains": 
        if (strpos($value, $cons) !== false) return true;
        break;
    }
    
    return false;
  }
  
  function loadRefExClass(){
    return $this->_ref_ex_class = $this->loadFwdRef("ex_class_id");
  }
}
