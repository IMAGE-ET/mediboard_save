<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * Form class
 */
class CExClass extends CMbObject {
  public $ex_class_id;

  public $name;
  public $conditional;
  public $group_id;
  public $native_views;
  
  public $pixel_positionning;

  /** @var CExClassField[] */
  public $_ref_fields;

  /** @var CExClassEvent[] */
  public $_ref_events;

  /** @var CExClassFieldGroup[] */
  public $_ref_groups;
  
  public $_fields_by_name;
  public $_dont_create_default_group;
  public $_duplicate;
  public $_formula_field;
  
  private $_latest_ex_object_cache = array();

  /** @var self[] */
  static $_list_cache = array();
  
  static $_native_views = array(
    //"atcd"       => array("CSejour", "CConsultation"),
    //"constantes" => array("CSejour", "CConsultation"),
    //"corresp"    => array("CPatient"),
    "atcd"       => "CSejour",
    "constantes" => "CSejour",
    "corresp"    => "CPatient",
  );

  /** @var CExClassFieldGroup[] */
  public $_groups;

  /** @var CExObject */
  public $_ex_object;

  /** @var array */
  public $_grid;

  /** @var array */
  public $_out_of_grid;

  /** @var CMbObject[] */
  public $_host_objects;

  /**
   * Compare values with each other with a comparison operator
   *
   * @param string|float $a        Operand A
   * @param string       $operator Operator
   * @param string|float $b        Operand B
   *
   * @return bool
   */
  static function compareValues($a, $operator, $b) {
    // =|!=|>|>=|<|<=|startsWith|endsWith|contains default|=
    switch ($operator) {
      default:
      case "=":
        return $a == $b;
        
      case "!=": 
        return $a != $b;
        
      case ">": 
        return $a > $b;
        
      case ">=": 
        return $a >= $b;
        
      case "<": 
        return $a < $b;
        
      case "<=": 
        return $a <= $b;
        
      case "startsWith": 
        return strpos($a, $b) === 0;
        
      case "endsWith": 
        return substr($a, -strlen($b)) == $b;
        
      case "contains": 
        return strpos($a, $b) !== false;
        
      case "hasValue": 
        return $a != "";

      case "hasNoValue":
        return $a == "";
    }
  }

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class";
    $spec->key   = "ex_class_id";
    $spec->uniques["ex_class"] = array("group_id", "name");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["name"]         = "str notNull seekable";
    $props["conditional"]  = "bool notNull default|0";
    $props["pixel_positionning"] = "bool notNull default|0";
    $props["group_id"]     = "ref class|CGroups";
    $props["native_views"] = "set vertical list|".implode("|", array_keys(self::$_native_views));
    return $props;
  }

  /**
   * @see parent::loadEditView()
   */
  function loadEditView() {
    parent::loadEditView();

    CExObject::initLocales();

    CExObject::$_locales_cache_enabled = false;

    if ($this->pixel_positionning) {
      $grid = null;
      $out_of_grid = null;
      $this->getPixelGrid();

      foreach ($this->_ref_groups as $_ex_group) {
        $_ex_group->loadRefsSubgroups(true);
        $_subgroups = $_ex_group->loadRefsSubgroups(true);
        foreach ($_subgroups as $_subgroup) {
          $_subgroup->countBackRefs("children_groups");
          $_subgroup->countBackRefs("children_fields");
          $_subgroup->countBackRefs("children_messages");
        }
      }
    }
    else {
      list($grid, $out_of_grid) = $this->getGrid(4, 40, false);
    }

    $events = $this->loadRefsEvents();
    foreach ($events as $_event) {
      $_event->countBackRefs("constraints");
    }
    
    $this->_groups = CGroups::loadGroups();
    $this->_ex_object = $this->getExObjectInstance();
    
    $this->_grid = $grid;
    $this->_out_of_grid = $out_of_grid;
    
    if (!$this->_id) {
      $this->group_id = CGroups::loadCurrent()->_id;
    }
    
    $classes = CExClassEvent::getReportableClasses();
    $instances = array();
    foreach ($classes as $_class) {
      $instances[$_class] = new $_class;
    }

    $this->_host_objects = $instances;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["field_groups"] = "CExClassFieldGroup ex_class_id";
    $backProps["events"]       = "CExClassEvent ex_class_id";
    $backProps["ex_triggers"]  = "CExClassFieldTrigger ex_class_triggered_id";
    $backProps["identifiants"] = "CIdSante400 object_id cascade";
    $backProps["ex_links"]     = "CExLink ex_class_id";
    return $backProps;
  }

  /**
   * @see parent::getExportedBackRefs()
   */
  function getExportedBackRefs(){
    $export = parent::getExportedBackRefs();
    $export["CExClass"]           = array("field_groups");
    $export["CExClassFieldGroup"] = array("class_fields", "host_fields", "class_messages");
    $export["CExConcept"]         = array("list_items");
    $export["CExList"]            = array("list_items");
    return $export;
  }

  /**
   * Builds a WHERE statement from search data
   *
   * @param array $search Structure containign search data
   *
   * @return array
   */
  function getWhereConceptSearch($search) {
    $comp_map = array(
      "eq"  => "=",
      "lte" => "<=",
      "lt"  => "<",
      "gte" => ">=",
      "gt"  => ">",
    );
          
    $_fields = $this->loadRefsAllFields();
    $_table  = $this->getTableName();
    $where   = array();
    
    foreach ($_fields as $_field) {
      if (!isset($search[$_field->concept_id])) {
        continue;
      }
      
      $_ds    = $_field->_spec->ds;
      $_col   = "$_table.$_field->name";
      
      foreach ($search[$_field->concept_id] as $_val) {
        $_val_a = $_val['a'];
        $_comp  = $_val['comp'];
        
        if (isset($comp_map[$_comp])) {
          $where[$_col] = $_ds->prepare($comp_map[$_comp]."%", $_val_a);
        }
        else {
          switch ($_comp) {
            case "contains": 
              $where[$_col] = $_ds->prepareLike("%$_val_a%");
              break;
            case "begins": 
              $where[$_col] = $_ds->prepareLike("$_val_a%");
              break;
            case "ends": 
              $where[$_col] = $_ds->prepareLike("%$_val_a");
              break;
            case "between": 
              $_val_b = $_val['b'];
              $where[$_col] = $_ds->prepare("BETWEEN % AND %", $_val_a, $_val_b);
              break;
          }
        }
      }
    }

    return $where;
  }

  /**
   * Get a CExObject instance
   *
   * @param bool $cache Use cache
   *
   * @return CExObject
   */
  function getExObjectInstance($cache = false){
    static $instances = array();
    
    if ($cache && isset($instances[$this->_id])) {
      return clone $instances[$this->_id];
    }
    
    $ex_object = new CExObject($this->_id);
    
    if ($cache) {
      $instances[$this->_id] = $ex_object;
    }
    
    return $ex_object;
  }
  
  /**
   * Gets the latest CExObject for the given CMbObject host object
   *
   * @param CMbObject $object The host object
   *
   * @return CExObject The resolved CExObject
   */
  function getLatestExObject(CMbObject $object){
    if (isset($this->_latest_ex_object_cache[$object->_class][$object->_id])) {
      return $this->_latest_ex_object_cache[$object->_class][$object->_id];
    }

    $ex_link = new CExLink();
    $where = array(
      "object_class" => " = '$object->_class'",
      "object_id"    => " = '$object->_id'",
      "ex_class_id"  => " = '$this->_id'",
      "level"        => $ex_link->getDS()->prepareIn(array("object", "ref1", "ref2")),
    );
    $ex_link->loadObject($where, "ex_object_id DESC");

    $ex_object = $ex_link->loadRefExObject();

    return $this->_latest_ex_object_cache[$object->_class][$object->_id] = $ex_object;
  }

  /**
   * Get the CExObject class name
   *
   * @return string
   */
  function getExClassName(){
    return "CExObject_{$this->_id}";
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields(){
    parent::updateFormFields();
    $this->getFormulaField();

    $this->_view = $this->name;
  }

  /**
   * Get the list of formula fields of the form
   *
   * @return array|null
   */
  function getFormulaField(){
    static $list = null;

    if ($list === null) {
      $ds = $this->getDS();

      $request = new CRequest();
      $request->addSelect(array("ex_class_field_group.ex_class_id", "ex_class_field.name"));
      $request->addTable("ex_class_field");
      $where = array(
        "ex_class_field.result_in_title"   => "= '1'",
      );
      $request->addWhere($where);
      $ljoin = array(
        "ex_class_field_group" => "ex_class_field_group.ex_class_field_group_id = ex_class_field.ex_group_id",
      );
      $request->addLJoin($ljoin);

      $list = $ds->loadHashList($request->getRequest());
    }

    return $this->_formula_field = CValue::read($list, $this->_id, null);
  }

  /**
   * Get the formula field
   *
   * @param string $field_name Field name
   * @param array  $where      The WHERE statement
   *
   * @return array|null
   */
  function getFormulaResult($field_name, $where) {
    $ds = $this->getDS();
    $table = $this->getTableName();

    $where["ex_link.ex_class_id"] = "= '$this->_id'";

    $ljoin = array(
      "ex_link" => "ex_link.ex_object_id = $table.ex_object_id"
    );

    $request = new CRequest();
    $request->addSelect($field_name);
    $request->addTable($table);
    $request->addWhere($where);
    $request->addLJoin($ljoin);
    $request->addOrder("ex_link.ex_object_id DESC");

    return $ds->loadResult($request->getRequest());
  }

  /**
   * Load the "field_groups" back refs
   *
   * @param bool $cache Use cache
   *
   * @return CExClassFieldGroup[]
   */
  function loadRefsGroups($cache = true){
    static $groups_cache = array();

    if ($cache && isset($groups_cache[$this->_id])) {
      return $this->_ref_groups = $groups_cache[$this->_id];
    }
    
    $this->_ref_groups = $this->loadBackRefs("field_groups", "rank, ex_class_field_group_id");
    
    if ($cache) {
      $groups_cache[$this->_id] = $this->_ref_groups;
    }
    
    return $this->_ref_groups;
  }

  /**
   * Load the fields
   *
   * @param bool $cache Use cache
   *
   * @return CExClassField[]
   */
  function loadRefsAllFields($cache = false){
    $groups = $this->loadRefsGroups();
    $fields = array();
    foreach ($groups as $_group) {
      $_fields = $_group->loadRefsFields($cache);
      $fields = array_merge($_fields, $fields);
    }
    return $fields;
  }

  /**
   * Load the "events" back refs
   *
   * @return CExClassEvent[]
   */
  function loadRefsEvents(){
    return $this->_ref_events = $this->loadBackRefs("events");
  }

  /**
   * Get the table name
   *
   * @return string
   */
  function getTableName(){
    return "ex_object_{$this->_id}";
  }

  /**
   * Load CExObjects and inject the CExObject instance into $ex_object
   *
   * @param CMbObject $object     The host object
   * @param null      &$ex_object The variable where the CExObject will be injected
   *
   * @return CExObject[]
   */
  function loadExObjects(CMbObject $object, &$ex_object = null) {
    $ex_object = new CExObject($this->_id);
    $ex_object->setObject($object);

    /** @var CExObject[] $list */
    $list = $ex_object->loadMatchingList();
    
    foreach ($list as $_object) {
      $_object->_ex_class_id = $this->_id;
      $_object->setExClass();
    }
    
    return $list;
  }

  /**
   * Load all the elements to be pu on the pixel grid
   *
   * @return CExClassFieldGroup[]
   */
  function getPixelGrid(){
    $groups = $this->loadRefsGroups(true);

    foreach ($groups as $_ex_group) {
      // Subgroups
      $_ex_group->loadRefsSubgroups(true);
      CStoredObject::massCountBackRefs($_ex_group->_ref_subgroups, "properties");

      foreach ($_ex_group->_ref_subgroups as $_ex_subgroup) {
        $_ex_subgroup->getDefaultProperties();
      }

      // Fields
      $_ex_group->loadRefsRootFields();
      CStoredObject::massCountBackRefs($_ex_group->_ref_fields, "properties");

      foreach ($_ex_group->_ref_fields as $_ex_field) {
        $_ex_field->getSpecObject();
        $_ex_field->getDefaultProperties();
      }

      // Messages
      $_ex_group->loadRefsRootMessages();
      CStoredObject::massCountBackRefs($_ex_group->_ref_messages, "properties");

      foreach ($_ex_group->_ref_messages as $_ex_message) {
        $_ex_message->getDefaultProperties();
      }
    }

    return $groups;
  }

  /**
   * Build the grid
   *
   * @param int  $w      Grid width
   * @param int  $h      Grid height
   * @param bool $reduce Reduced the grid if it contains empty rows or empty columns
   *
   * @return array
   */
  function getGrid($w = 4, $h = 40, $reduce = true) {
    $big_grid = array();
    $big_out_of_grid = array();
    $groups = $this->loadRefsGroups(true);
    $empty_cell = array("type" => null, "object" => null);

    foreach ($groups as $_ex_group) {
      $grid = array_fill(0, $h, array_fill(0, $w, $empty_cell));
      
      $out_of_grid = array(
        "field"         => array(),
        "label"         => array(),
        "message_title" => array(),
        "message_text"  => array(),
      );

      $_fields = $_ex_group->loadRefsFields();
      CStoredObject::massCountBackRefs($_fields, "properties");
      
      foreach ($_fields as $_ex_field) {
        $_ex_field->getSpecObject();
        $_ex_field->getDefaultProperties();

        $label_x = $_ex_field->coord_label_x;
        $label_y = $_ex_field->coord_label_y;
        
        $field_x = $_ex_field->coord_field_x;
        $field_y = $_ex_field->coord_field_y;
        
        // label
        if ($label_x === null || $label_y === null) {
          $out_of_grid["label"][$_ex_field->name] = $_ex_field;
        }
        else {
          $grid[$label_y][$label_x] = array(
            "type"   => "label",
            "object" => $_ex_field,
          );
        }
        
        // field
        if ($field_x === null || $field_y === null) {
          $out_of_grid["field"][$_ex_field->name] = $_ex_field;
        }
        else {
          $grid[$field_y][$field_x] = array(
            "type"   => "field",
            "object" => $_ex_field,
          );
        }
      }
    
      // Host fields
      $_host_fields = $_ex_group->loadRefsHostFields();
      foreach ($_host_fields as $_host_field) {
        $label_x = $_host_field->coord_label_x;
        $label_y = $_host_field->coord_label_y;
        
        $value_x = $_host_field->coord_value_x;
        $value_y = $_host_field->coord_value_y;
        
        // label
        if ($label_x !== null && $label_y !== null) {
          $grid[$label_y][$label_x] = array(
            "type"   => "label",
            "object" => $_host_field,
          );
        }
        
        // value
        if ($value_x !== null && $value_y !== null) {
          $grid[$value_y][$value_x] = array(
            "type"   => "value",
            "object" => $_host_field,
          );
        }
      }
    
      // Messages
      $_ex_messages = $_ex_group->loadRefsMessages();
      CStoredObject::massCountBackRefs($_ex_messages, "properties");

      foreach ($_ex_messages as $_message) {
        $_message->getDefaultProperties();

        $title_x = $_message->coord_title_x;
        $title_y = $_message->coord_title_y;
        
        $text_x = $_message->coord_text_x;
        $text_y = $_message->coord_text_y;
        
        // label
        if ($title_x === null || $title_y === null) {
          $out_of_grid["message_title"][$_message->_id] = $_message;
        }
        else {
          $grid[$title_y][$title_x] = array(
            "type"   => "message_title",
            "object" => $_message,
          );
        }
        
        // value
        if ($text_x === null || $text_y === null) {
          $out_of_grid["message_text"][$_message->_id] = $_message;
        }
        else {
          $grid[$text_y][$text_x] = array(
            "type"   => "message_text",
            "object" => $_message,
          );
        }
      }
      
      if ($reduce) {
        $max_filled = 0;
        
        foreach ($grid as $_y => $_line) {
          $n_filled = 0;
          $x_filled = 0;
          
          foreach ($_line as $_x => $_cell) {
            if ($_cell !== $empty_cell) {
              $n_filled++;
              $x_filled = max($_x, $x_filled);
            }
          }
          
          if ($n_filled == 0) {
            unset($grid[$_y]);
          }

          $max_filled = max($max_filled, $x_filled);
        }
        
        if (empty($out_of_grid)) {
          foreach ($grid as $_y => $_line) {
            $grid[$_y] = array_slice($_line, 0, $max_filled+1);
          }
        }
      }
      
      $big_grid       [$_ex_group->_id] = $grid;
      $big_out_of_grid[$_ex_group->_id] = $out_of_grid;
    }
    
    return array(
      $big_grid, $big_out_of_grid, $groups,
      "grid"        => $big_grid, 
      "out_of_grid" => $big_out_of_grid, 
      "groups"      => $groups,
    );
  }

  /**
   * @see parent::store()
   */
  function store(){
    if ($this->_id && $this->_duplicate) {
      $this->_duplicate = null;
      return $this->duplicate();
    }
    
    if ($msg = $this->check()) {
      return $msg;
    }
    
    $is_new = !$this->_id;
    
    if ($is_new) {
      if ($msg = parent::store()) {
        return $msg;
      }
      
      // Groupe par d�faut
      if (!$this->_dont_create_default_group) {
        $ex_group = new CExClassFieldGroup;
        $ex_group->name = "Groupe g�n�ral";
        $ex_group->ex_class_id = $this->_id;
        $ex_group->store();
      }
      
      $table_name = $this->getTableName();
      $query = "CREATE TABLE `$table_name` (
        `ex_object_id`     INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `group_id`         INT(11) UNSIGNED NOT NULL,
        
        `object_id`        INT(11) UNSIGNED NOT NULL,
        `object_class`     VARCHAR(80) NOT NULL,
        
        `reference_id`     INT(11) UNSIGNED NOT NULL,
        `reference_class`  VARCHAR(80) NOT NULL,
        
        `reference2_id`    INT(11) UNSIGNED NOT NULL,
        `reference2_class` VARCHAR(80) NOT NULL,

        `additional_id`    INT (11) UNSIGNED,
        `additional_class` VARCHAR(80),
        
        INDEX ( `group_id` ),
        INDEX `object`     ( `object_class`,     `object_id` ),
        INDEX `reference1` ( `reference_class`,  `reference_id` ),
        INDEX `reference2` ( `reference2_class`, `reference2_id` ),
        INDEX `additional` ( `additional_class`, `additional_id` )
      ) /*! ENGINE=MyISAM */;";
      
      $ds = $this->_spec->ds;
      if (!$ds->query($query)) {
        return "La table '$table_name' n'a pas pu �tre cr��e (".$ds->error().")";
      }
    }
    
    return parent::store();
  }

  /**
   * @see parent::delete()
   */
  function delete(){
    if ($msg = $this->canDeleteEx()) {
      return $msg;
    }
    
    // suppression des objets des champs sans supprimer les colonnes de la table
    $fields = $this->loadRefsAllFields();
    foreach ($fields as $_field) {
      $_field->_dont_drop_column = true;
      $_field->delete();
    }
    
    /*$table_name = $this->getTableName();
    $query = "DROP TABLE `$table_name`";
    
    $ds = $this->_spec->ds;
    if (!$ds->query($query)) {
      return "La table '$table_name' n'a pas pu �tre supprim�e (".$ds->error().")";
    }*/
    
    return parent::delete();
  }
  
  /**
   * Duplicates the object
   *
   * - field_groups
   *   - class_fields
   *     - enum_translations
   *     - field_translations
   *     - list_items
   *     - ex_triggers
   *     - (predicates)
   * 
   *   - host_fields
   *   - class_messages
   * 
   * - constraints
   * - ex_triggers
   *
   * @return null|string Store-like message
   */
  function duplicate(){
    if (!$this->_id) {
      return null;
    }
    
    // Load all field values
    $this->load();
    
    $new = new self;
    $new->cloneFrom($this);
    
    $new->name .= " (Copie)";
    $new->_dont_create_default_group = true;
    
    if ($msg = $new->store()) {
      return $msg;
    }
    
    // field_groups
    foreach ($this->loadRefsGroups() as $_group) {
      if ($msg = $this->duplicateObject($_group, "ex_class_id", $new->_id, $_new_group)) {
        continue;
      }
      
      $fwd_field = "ex_group_id";
      $fwd_value = $_new_group->_id;
      
      // class_fields
      foreach ($_group->loadRefsFields() as $_field) {
        if ($msg = $this->duplicateObject($_field, "ex_group_id", $_new_group->_id, $_new_field, array("predicate_id"))) {
          continue;
        }
        
        $_fwd_field = "ex_class_field_id";
        $_fwd_value = $_new_field->_id;
        
        // field_translations
        $this->duplicateBackRefs($_field, "field_translations", $_fwd_field, $_fwd_value);
        
        // enum_translations
        $this->duplicateBackRefs($_field, "enum_translations", $_fwd_field, $_fwd_value);
        
        // list_items
        $this->duplicateBackRefs($_field, "list_items", "field_id", $_fwd_value);
        
        // ex_triggers
        $this->duplicateBackRefs($_field, "ex_triggers", $_fwd_field, $_fwd_value);
        
        // predicates
        //$this->duplicateBackRefs($_field, "predicates", $_fwd_field, $_fwd_value);
      }
      
      // host_fields
      $this->duplicateBackRefs($_group, "host_fields", $fwd_field, $fwd_value);
      
      // class_messages
      $this->duplicateBackRefs($_group, "class_messages", $fwd_field, $fwd_value);
    }
    
    // ex_triggers
    $this->duplicateBackRefs($this, "ex_triggers", "ex_class_triggered_id", $new->_id);
    
    CExObject::clearLocales();

    return null;
  }

  /**
   * Duplicates an object
   *
   * @param CMbObject $object         The object to duplicate
   * @param string    $fwd_field      Forward field
   * @param mixed     $fwd_value      Forward value
   * @param CMbObject &$new           The new object (input)
   * @param array     $exclude_fields Excluded fields
   *
   * @return null|string
   */
  private function duplicateObject(CMbObject $object, $fwd_field, $fwd_value, &$new = null, $exclude_fields = array()) {
    $class = $object->_class;

    /** @var CExObject $new */
    $new = new $class;
    $new->cloneFrom($object);

    foreach ($exclude_fields as $_field) {
      $new->$_field = null;
    }

    $new->$fwd_field = $fwd_value;
    
    return $new->store();
  }

  /**
   * Duplicate back refs
   *
   * @param CMbObject $object    Object to duplicate back refs of
   * @param string    $backname  Back reference name
   * @param string    $fwd_field Forward field name
   * @param mixed     $fwd_value Forward field value
   *
   * @return void
   */
  private function duplicateBackRefs(CMbObject $object, $backname, $fwd_field, $fwd_value) {
    foreach ($object->loadBackRefs($backname) as $_back) {
      /** @var CMbObject $_back */
      $this->duplicateObject($_back, $fwd_field, $fwd_value);
    }
  }
}
