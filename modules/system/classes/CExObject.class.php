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
  
  var $reference2_class = null;
  var $reference2_id = null;
  
  var $_ex_class_id = null;
  var $_own_ex_class_id = null;
  var $_specs_already_set = false;
  
  static $_load_lite = false;
  static $_multiple_load = false;
  
  static $_ex_specs = array();
  
  /**
   * @var CExClass
   */
  public $_ref_ex_class = null;
  
  /**
   * @var CMbObject
   */
  var $_ref_reference_object_1 = null;
  
  /**
   * @var CMbObject
   */
  var $_ref_reference_object_2 = null;
  
  var $_reported_fields = array();

  function __construct(){
    parent::__construct();
  
    if (self::$_multiple_load) {
      $class = get_class($this);
      unset(self::$spec[$class]);
      unset(self::$props[$class]);
      unset(self::$specs[$class]);
      unset(self::$backProps[$class]);
      unset(self::$backSpecs[$class]);
    }
  }

  function setExClass() {
    if ($this->_specs_already_set || !$this->_ex_class_id && !$this->_own_ex_class_id) return;
    
    $this->_props = $this->getProps();
    
    CBoolSpec::$_default_no = false;
    $this->_specs = @$this->getSpecs(); // when creating the field
    CBoolSpec::$_default_no = true;
    
    $ex_class = $this->_ref_ex_class;
    
    $this->_class = "CExObject_{$ex_class->_id}";
    
    $this->_own_ex_class_id = $ex_class->_id;
    $this->_ref_ex_class = $ex_class;
    
    $this->_specs_already_set = true;
  }
  
  function loadRefExClass($cache = true){
    if ($cache && $this->_ref_ex_class && $this->_ref_ex_class->_id) {
      return $this->_ref_ex_class;
    }
    
    $ex_class = new CExClass();
    $ex_class->load($this->getClassId());
    
    return $this->_ref_ex_class = $ex_class; // can't use loadFwdRef here
  }
  
  function setReferenceObject_1(CMbObject $reference) {
    $this->_ref_reference_object_1 = $reference;
    $this->reference_class = $reference->_class;
    $this->reference_id = $reference->_id;
  }
  
  function setReferenceObject_2(CMbObject $reference) {
    $this->_ref_reference_object_2 = $reference;
    $this->reference2_class = $reference->_class;
    $this->reference2_id = $reference->_id;
  }
  
  function loadRefReferenceObjects(){
    $this->_ref_reference_object_1 = $this->loadFwdRef("reference_id");
    $this->_ref_reference_object_2 = $this->loadFwdRef("reference2_id");
  }
  
  /**
   * Permet de supprimer les valeurs non presentes dans les 
   * specs du champ dans ce formulaire, mais qui le sont peut
   * etre dans le meme champ dans un autre formulaire (cas d'un concept)
  **/
  static function typeSetSpecIntersect($field, $value) {
    $field_spec = $field->getSpecObject();
    
    if (!$field_spec instanceof CSetSpec) {
      return $value;
    }
    
    $values = explode("|", $value);
    $values = array_intersect($values, $field_spec->_list);
    
    return implode("|", $values);
  }
  
  // FIXME pas DU TOUT optimis�
  /*
   * attention aux dates, il faut surement checker le log de derniere modif des champs du concept
   */
  function getReportedValues(){
    if ($this->_id) return;
    
    self::$_multiple_load = true;
    CExClassField::$_load_lite = true;
    
    $object = $this->loadTargetObject();
    
    $ex_class = $this->_ref_ex_class;
    
    $this->_ref_reference_object_1 = $ex_class->resolveReferenceObject($object, 1);
    $this->_ref_reference_object_2 = $ex_class->resolveReferenceObject($object, 2);
    
    $latest_1 = $ex_class->getLatestExObject($this->_ref_reference_object_1, 1);
    $latest_2 = $ex_class->getLatestExObject($this->_ref_reference_object_2, 2);
    $latest_host = $ex_class->getLatestExObject($this->_ref_object, "host");
    
    $fields = $this->_ref_ex_class->loadRefsAllFields(true);
    
    // on cherche les champs report�s de l'objet courant
    foreach($fields as $_field) {
      $field_name = $_field->name;
      $this->_reported_fields[$field_name] = null;
      
      // valeur par d�faut
      $spec_obj = $_field->getSpecObject();
      $this->$field_name = CExClassField::unescapeProp($spec_obj->default);
      
      // si champ pas report�, on passe au suivant
      if (!$_field->report_level) continue;
      
      $_level = $_field->report_level;
      
      // si champ bas� sur un concept, il faut parcourir 
      // tous les formulaires qui ont un champ du meme concept
      if ($_field->concept_id) {
        $_concept = $_field->loadRefConcept();
        $_concept_fields = $_concept->loadRefClassFields();
        
        $_latest = null;
        $_latest_value = null;
        
        // on regarde tous les champs du concept
        foreach($_concept_fields as $_concept_field) {
          $_ex_class = $_concept_field->loadRefExClass();
          
          // en fonction du niveau
          switch($_level) {
            case 1:      $_concept_latest = $_ex_class->getLatestExObject($this->_ref_reference_object_1, 1); break;
            case 2:      $_concept_latest = $_ex_class->getLatestExObject($this->_ref_reference_object_2, 2); break;
            case "host": $_concept_latest = $_ex_class->getLatestExObject($this->_ref_object, "host");
          }
          
          // si pas d'objet precedemment enregistr�
          if (!$_concept_latest->_id || $_concept_latest->{$_concept_field->name} == "") continue;
          
          // on regarde le log pour voir lequel a �t� saisi en dernier
          //$_log = $_concept_latest->loadLastLogForField($_concept_field->name); // FIXME ne donne rien quand type=create 
          $_log = $_concept_latest->loadLastLog();
          
          if (!$_latest) {
            $_latest = $_concept_latest;
            $_latest_value = $_latest->{$_concept_field->name};
          }
          else {
            if ($_log->date > $_latest->_ref_last_log->date) {
              $_latest = $_concept_latest;
              $_latest_value = $_latest->{$_concept_field->name};
            }
          }
          
          //mbTrace($_latest->{$_concept_field->name}, "field de $_concept_field->_ref_ex_class");
        }
        
        if ($_latest) {
          $this->_reported_fields[$field_name] = $_latest;
          $_latest->loadTargetObject();
          $this->$field_name = self::typeSetSpecIntersect($_field, $_latest_value);
        }
      }
      else {
        // ceux de la meme exclass
        if (!$latest_1->_id && !$latest_2->_id) continue;
        
        switch($_level) {
          case 1:      $_base = $latest_1;    break;
          case 2:      $_base = $latest_2;    break;
          case "host": $_base = $latest_host; break;
        }
        
        if ($_base->$field_name == "") continue;
        
        $_base->loadTargetObject();
        $_base->loadLastLog();
        
        $this->_reported_fields[$field_name] = $_base;
        $this->$field_name = self::typeSetSpecIntersect($_field, $_base->$field_name);
      }
    }
    
    self::$_multiple_load = false;
    CExClassField::$_load_lite = false;
  
    /*
    if (!$latest_1->_id && !$latest_2->_id) return;
    
    $fields = $this->_ref_ex_class->loadRefsAllFields(true);
    
    foreach($fields as $_field) {
      $field_name = $_field->name;
      
      $spec_obj = $_field->getSpecObject();
      $this->$field_name = $spec_obj->default;
      
      if (!$_field->report_level) continue;
      
      $level = $_field->report_level;
      
      $this->$field_name = (($level == 1) ? $latest_1->$field_name : $latest_2->$field_name);
    }*/
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
    
    if (!$this->_id) {
      $object = $this->loadTargetObject();
      
      if (!$this->reference_id && !$this->reference_class) {
        $reference = $this->_ref_ex_class->resolveReferenceObject($object, 1);
        $this->setReferenceObject_1($reference);
      }
      
      if (!$this->reference2_id && !$this->reference2_class) {
        $reference = $this->_ref_ex_class->resolveReferenceObject($object, 2);
        $this->setReferenceObject_2($reference);
      }
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
  
  // Used in updatePlainFields
  function getPlainFields() {
    $this->setExClass();
    return parent::getPlainFields();
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
      $newObject = new self; // $this->_class >>>> "self"
      //$newObject->_ex_class_id = $this->_ex_class_id;
      //$newObject->setExClass();
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
    $class = $this->_class;
    $this->_class = get_class($this);
    
    $spec = $this->_specs[$propName];
    $ret = $spec->checkPropertyValue($this);
    
    $this->_class = $class;
    return $ret;
  }
  /// End low level methods /////////
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->key = "ex_object_id";
    return $spec;
  }
  
  function getClassId(){
    return ($this->_ex_class_id ? $this->_ex_class_id : $this->_own_ex_class_id);
  }
  
  function getTableName(){
    return "ex_object_".$this->getClassId();
  }
  
  function getProps() {
    $this->loadRefExClass();
    $this->_spec->table = $this->getTableName();
    
    $class = get_class($this)."_".$this->getClassId();
    $props = parent::getProps();
    $props["ex_object_id"] = "ref class|$class show|0";
    $props["_ex_class_id"]    = "ref class|CExClass";
    
    $props["reference_class"] = "str class";
    $props["reference_id"]    = "ref class|CMbObject meta|reference_class";
    
    $props["reference2_class"] = "str class";
    $props["reference2_id"]    = "ref class|CMbObject meta|reference2_class";
    
    if (self::$_load_lite) {
      return $props;
    }
    
    $fields = $this->_ref_ex_class->loadRefsAllFields(true);
    
    foreach($fields as $_field) {
      if (isset($this->{$_field->name})) break; // don't redeclare them more than once
      $this->{$_field->name} = null; // declaration of the field
      $props[$_field->name] = $_field->prop; // declaration of the field spec
    }
    
    return $props;
  }
  
  function getSpecs(){
    $ex_class_id = $this->getClassId();
    $this->_class = get_class($this)."_$ex_class_id";
    
    if ($this->_id && isset(self::$_ex_specs[$ex_class_id])) {
      return self::$_ex_specs[$ex_class_id];
    }
    
    $specs = @parent::getSpecs(); // sometimes there is "list|"
        
    foreach($specs as $_field => $_spec) {
      if ($_spec instanceof CEnumSpec) {
        foreach ($_spec->_locales as $key => $locale) {
          $specs[$_field]->_locales[$key] = CAppUI::tr("$this->_class.$_field.$key");
        }
      }
    }
    
    return self::$_ex_specs[$ex_class_id] = $specs;
  }
  
  function loadLogs(){
    $this->setExClass();
    $ds = $this->_spec->ds;
    
    $where = array(
      "object_class" => $ds->prepare("=%", $this->_class),
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
