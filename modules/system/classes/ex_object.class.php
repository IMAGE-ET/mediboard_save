<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CExObject extends CMbMetaObject {
  var $ex_object_id = null;
	
  var $reference_class = null;
  var $reference_id = null;
  
  var $_ex_class_id = null;
  var $_own_ex_class_id = null;
  var $_specs_already_set = false;
  
  static $_load_lite = false;
  
  /**
   * @var CExClass
   */
  public $_ref_ex_class = null;
	
  /**
   * @var CMbObject
   */
  var $_ref_reference_object = null;

  function setExClass() {
    if ($this->_specs_already_set || !$this->_ex_class_id && !$this->_own_ex_class_id) return;
    
    $ex_class = $this->loadRefExClass();
    
    $this->_own_ex_class_id = $ex_class->_id;
    $this->_ref_ex_class = $ex_class;
    
    $this->_props = $this->getProps();
    $this->_specs = @$this->getSpecs(); // when creating the field
    
    $this->_class_name = "CExObject_{$ex_class->_id}";
    
    $this->_specs_already_set = true;
  }
  
  function loadRefExClass($cache = true){
    if ($cache && $this->_ref_ex_class && $this->_ref_ex_class->_id) return $this->_ref_ex_class;
    
    $ex_class = new CExClass();
    $ex_class->load($this->_ex_class_id ? $this->_ex_class_id : $this->_own_ex_class_id);
    
    return $this->_ref_ex_class = $ex_class; // can't use loadFwdRef here
  }
  
  function setReferenceObject(CMbObject $reference) {
    $this->_ref_reference_object = $reference;
    $this->reference_class = $reference->_class_name;
    $this->reference_id = $reference->_id;
  }
	
	function getReportedValues(){
		if ($this->_id) return;
		
		$object = $this->loadTargetObject();
		
		$ex_class = $this->_ref_ex_class;
		$reference = $ex_class->resolveReferenceObject($object);
		$latest = $ex_class->getLatestExObject($reference);
		
		if (!$latest->_id) return;
		
		$fields = $this->_ref_ex_class->loadRefsAllFields(true);
    
    foreach($fields as $_field) {
    	if (!$_field->reported) continue;
    	$field_name = $_field->name;
      $this->$field_name = $latest->$field_name;
    }
	}
  
  function loadOldObject() {
    if (!$this->_old) {
      $this->_old = new self;
      $this->_old->_ex_class_id = $this->_ex_class_id;
      $this->_old->setExClass();
      $this->_old->load($this->_id);
    }
  }
	
	function store(){
		if ($msg = $this->check()) {
			return $msg;
		}
		
		if (!$this->_id && !$this->reference_id && !$this->reference_class) {
			$object = $this->loadTargetObject();
			$reference = $this->_ref_ex_class->resolveReferenceObject($object);
			$this->setReferenceObject($reference);
		}
		
		return parent::store();
	}
  
  /// Low level methods ///////////
  function bind($hash, $doStripSlashes = true) {
    $this->setExClass();
    return parent::bind($hash, $doStripSlashes);
  }
  
  function load($id = null) {
    $this->setExClass();
    return parent::load($id);
  }
  
  // Used in updateDBFields
  function getDBFields() {
    $this->setExClass();
    return parent::getDBFields();
  }
  
  function fieldModified($field, $value = null) {
    $this->setExClass();
    return parent::fieldModified($field, $value);
  }

  function loadQueryList($sql) {
    $ds = $this->_spec->ds;
    $cur = $ds->exec($sql);
    $list = array();

    while ($row = $ds->fetchAssoc($cur)) {
      $newObject = new self; // $this->_class_name >>>> "self"
      //$newObject->_ex_class_id = $this->_ex_class_id;
      $newObject->bind($row, false);
      
      $newObject->checkConfidential();
      $newObject->updateFormFields();
      $newObject->registerCache();
      $list[$newObject->_id] = $newObject;
    }

    $ds->freeResult($cur);
    return $list;
  }
	
  // needed or will throw errors in the field specs
  function checkProperty($propName) {
    $class_name = $this->_class_name;
    $this->_class_name = get_class($this);
    
    $spec = $this->_specs[$propName];
    $ret = $spec->checkPropertyValue($this);
    
    $this->_class_name = $class_name;
    return $ret;
  }
  /// End low level methods /////////
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->key = "ex_object_id";
    return $spec;
  }
  
  function getProps() {
    $ex_class = $this->loadRefExClass();
    
    $this->_spec->table = $ex_class->getTableName();
    
    $props = parent::getProps();
    $props["_ex_class_id"]    = "ref class|CExClass";
    $props["reference_class"] = "str class";
    $props["reference_id"]    = "ref class|CMbObject meta|reference_class";
    
    if (self::$_load_lite) {
      return parent::getProps();
    }
    
    $fields = $this->_ref_ex_class->loadRefsAllFields();
    
    foreach($fields as $_field) {
      if (isset($this->{$_field->name})) break; // don't redeclare them more than once
      $this->{$_field->name} = null; // declaration of the field
      $props[$_field->name] = $_field->prop; // declaration of the field spec
    }
    
    return $props;
  }
  
  function getSpecs(){
    $ex_class = $this->loadRefExClass();
    $this->_class_name = get_class($this)."_{$ex_class->_id}";
    
    $specs = parent::getSpecs();
        
    foreach($specs as $_field => $_spec) {
      if ($_spec instanceof CEnumSpec) {
        foreach ($_spec->_locales as $key => $locale) {
          $specs[$_field]->_locales[$key] = CAppUI::tr("$this->_class_name.$_field.$key");
        }
      }
    }
    
    return $specs;
  }
  
  function loadLogs(){
    $this->setExClass();
    $ds = $this->_spec->ds;
		
    $where = array(
      "object_class" => $ds->prepare("=%", $this->_class_name),
      "object_id"    => $ds->prepare("=%", $this->_id)
    );
    
    $log = new CUserLog();
    $this->_ref_logs = $log->loadList($where, "date DESC", 100);

    // loadRefsFwd will fail because the ExObject's class doesn't really exist
    foreach($this->_ref_logs as &$_log) {
      $_log->loadRefUser();
    }
    
    // the first is at the end because of the date order !
    $this->_ref_first_log = end($this->_ref_logs);
    $this->_ref_last_log  = reset($this->_ref_logs);
  }
  
  static function getValidObject($object_class) {
    if (!preg_match('/^CExObject_(\d+)$/', $object_class, $matches)) {
      return false;
    }
    
    $ex_class = new CExClass();
    if (!$ex_class->load($matches[1])) {
      return false;
    }
    
    $ex_object = new CExObject();
    $ex_object->_ex_class_id = $ex_class->_id;
    $ex_object->setExClass();
    
    return $ex_object;
  }
}
