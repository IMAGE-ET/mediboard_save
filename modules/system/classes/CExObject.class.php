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
 * Form data
 */
class CExObject extends CMbMetaObject {
  public $ex_object_id;
  
  public $group_id;

  public $reference_class;
  public $reference_id;

  public $reference2_class;
  public $reference2_id;

  // "weak" link to an object stored after $this
  public $additional_class;
  public $additional_id;

  public $datetime_create;
  public $datetime_edit;
  public $owner_id;

  public $_ex_class_id;
  public $_own_ex_class_id;
  public $_specs_already_set = false;
  public $_native_views;
  public $_event_name;
  
  /** @var CExClass */
  public $_ref_ex_class;
  
  /** @var CMbObject */
  public $_ref_reference_object_1;

  /** @var CMbObject */
  public $_ref_reference_object_2;

  /** @var CMbObject */
  public $_ref_additional_object;

  /** @var CMediusers */
  public $_ref_owner;
  
  /** @var CGroups */
  public $_ref_group;
  
  public $_reported_fields = array();
  public $_fields_display = array();
  public $_fields_display_struct = array();
  public $_fields_default_properties = array();
  public $_formula_result;

  static $_load_lite      = false;
  static $_multiple_load  = false;
  
  static $_ex_specs       = array();
  
  static $_locales_ready = false;
  static $_locales_cache_enabled = true;

  /**
   * Custom constructor
   *
   * @param int $ex_class_id CExClass id
   */
  function __construct($ex_class_id = null){
    parent::__construct();
  
    if (self::$_multiple_load) {
      $class = get_class($this);
      unset(self::$spec[$class]);
      unset(self::$props[$class]);
      unset(self::$specs[$class]);
      unset(self::$backProps[$class]);
      unset(self::$backSpecs[$class]);
    }
    
    if ($ex_class_id) {
      $this->setExClass($ex_class_id);
    }
  }

  /**
   * Sets the CExClass ID of $this
   * The CExLinks are not declared as backrefs as it's not compatible with CExObject
   *
   * @param ref $ex_class_id CExClass ID
   *
   * @return void
   */
  function setExClass($ex_class_id = null) {
    if ($ex_class_id) {
      $this->_ex_class_id = $ex_class_id;
    }
    
    if ($this->_specs_already_set || !$this->_ex_class_id && !$this->_own_ex_class_id) {
      return;
    }
    
    if (CExObject::$_locales_cache_enabled) {
      self::initLocales();
    }
    
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

  /**
   * Load Ex class
   *
   * @param bool $cache Use object cache
   *
   * @return CExClass
   */
  function loadRefExClass($cache = true){
    if ($cache && $this->_ref_ex_class && $this->_ref_ex_class->_id) {
      return $this->_ref_ex_class;
    }
    
    $id = $this->getClassId();
    if (isset(CExClass::$_list_cache[$id])) {
      return $this->_ref_ex_class = CExClass::$_list_cache[$id];
    }
    
    $ex_class = new CExClass();
    $ex_class->load($this->getClassId());
    
    return $this->_ref_ex_class = $ex_class; // can't use loadFwdRef here
  }

  /**
   * Clears locales cache
   *
   * @return int The number of cache entries cleared
   */
  static function clearLocales() {
    $count = DSHM::remKeys("exclass-locales-*");

    self::$_locales_ready = false;

    return $count;
  }

  /**
   * Inits locale cache
   *
   * @return void
   */
  static function initLocales(){
    if (self::$_locales_ready) {
      return;
    }
    
    $lang = CAppUI::pref("LOCALE");

    $_all_locales = DSHM::get("exclass-locales-$lang");

    if (!$_all_locales) {
      $undefined = CAppUI::tr("Undefined");
      $ds = CSQLDataSource::get("std");

      $_all_locales = array();

      $request = new CRequest();
      $request->addTable("ex_class_field_translation");
      $request->addWhere(array(
        "lang" => "= '$lang'",
      ));
      $request->addLJoin(array(
        "ex_class_field"       => "ex_class_field.ex_class_field_id = ex_class_field_translation.ex_class_field_id",
        "ex_concept"           => "ex_concept.ex_concept_id = ex_class_field.concept_id",
        "ex_class_field_group" => "ex_class_field_group.ex_class_field_group_id = ex_class_field.ex_group_id",
      ));
      $request->addSelect(array(
        "ex_class_field_translation.std",
        "IF(ex_class_field_translation.desc, ex_class_field_translation.desc, ex_class_field_translation.std) AS `desc`",
        "IF(ex_class_field_translation.court, ex_class_field_translation.court, ex_class_field_translation.std) AS `court`",
        "ex_class_field.ex_class_field_id AS field_id",
        "ex_class_field.name",
        "ex_class_field.prop",
        "ex_class_field.concept_id",
        "ex_class_field_group.ex_class_id",
        "ex_concept.ex_list_id",
      ));

      $list = $ds->loadList($request->makeSelect());

      // Chargement des list_items par concept, field ou list
      $request = new CRequest();
      $request->addTable("ex_list_item");
      $request->addSelect(array(
        "ex_list_item_id",

        "list_id",
        "concept_id",
        "field_id",

        "name",
      ));
      $list_items = $ds->loadList($request->makeSelect());

      // Chargement en une seule requete de toutes les traductions de champs
      $enum_list_cache = array(
        "list"    => array(),
        "concept" => array(),
        "field"   => array(),
      );
      $mapper = array(
        "list_id"    => "list",
        "concept_id" => "concept",
        "field_id"   => "field",
      );
      foreach ($list_items as $_item) {
        $_item_id = $_item["ex_list_item_id"];
        $_item_name = $_item["name"];

        foreach ($mapper as $_field_name => $_to) {
          if ($_field_value = $_item[$_field_name]) {
            $enum_list_cache[$_to][$_field_value][$_item_id] = $_item_name;
          }
        }
      }

      foreach ($list as $_item) {
        $_locales = array();

        $key = "-{$_item['name']}";
        $_locales[$key]         = $_item["std"];
        if ($_item["desc"]) {
          $_locales["$key-desc"]  = $_item["desc"];
        }
        if ($_item["court"]) {
          $_locales["$key-court"] = $_item["court"];
        }

        $_ex_class_id = $_item['ex_class_id'];
        $_prefix = "CExObject_$_ex_class_id";

        $prop = $_item["prop"];
        if (strpos($prop, "enum") === false && strpos($prop, "set") === false) {
          if (!isset($_all_locales[$_prefix])) {
            $_all_locales[$_prefix] = array();
          }

          $_all_locales[$_prefix] = array_merge($_all_locales[$_prefix], $_locales);
          continue;
        }

        $key = ".{$_item['name']}";
        $_locales["$key."] = $undefined;

        $concept_id = $_item["concept_id"];
        $ex_list_id = $_item["ex_list_id"];
        $field_id   = $_item["field_id"];

        $enum_list = array();
        if ($concept_id) {
          if ($ex_list_id) {
            if (isset($enum_list_cache["list"][$ex_list_id])) {
              $enum_list = $enum_list_cache["list"][$ex_list_id];
            }
          }
          else {
            if (isset($enum_list_cache["concept"][$concept_id])) {
              $enum_list = $enum_list_cache["concept"][$concept_id];
            }
          }
        }
        else {
          if (isset($enum_list_cache["field"][$field_id])) {
            $enum_list = $enum_list_cache["field"][$field_id];
          }
        }

        foreach ($enum_list as $_value => $_locale) {
          $_locales["$key.$_value"] = $_locale;
        }

        if (!isset($_all_locales[$_prefix])) {
          $_all_locales[$_prefix] = array();
        }

        $_all_locales[$_prefix] = array_merge($_all_locales[$_prefix], $_locales);
      }

      DSHM::put("exclass-locales-$lang", $_all_locales, true);
    }

    foreach ($_all_locales as $_prefix => $_locales) {
      CAppUI::addLocales($_prefix, $_locales);
    }
    
    self::$_locales_ready = true;
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

  function setAdditionalObject(CMbObject $reference) {
    $this->_ref_additional_object = $reference;
    $this->additional_class = $reference->_class;
    $this->additional_id = $reference->_id;
  }
  
  function loadRefReferenceObjects(){
    $this->_ref_reference_object_1 = $this->loadFwdRef("reference_id");
    $this->_ref_reference_object_2 = $this->loadFwdRef("reference2_id");
  }

  function loadRefAdditionalObject(){
    $this->_ref_additional_object = $this->loadFwdRef("additional_id");

    if ($this->additional_id && $this->_ref_additional_object) {
      $this->_ref_additional_object->loadComplete();
    }
  }

  /**
   * @param bool $cache
   *
   * @return CGroups
   */
  function loadRefGroup($cache = true){
    return $this->_ref_group = $this->loadFwdRef("group_id", $cache);
  }
  
  function loadNativeViews(CExClassEvent $event){
    $this->_native_views = array();
    
    $views = $event->getAvailableNativeViews();
    $selected_views = explode('|', $this->_ref_ex_class->native_views);
    
    foreach ($views as $_name => $_class) {
      if (in_array($_name, $selected_views)) {
        $this->_native_views[$_name] = $this->getReferenceObject($_class);
      }
    }
    
    return $this->_native_views;
  }

  /**
   * Permet de supprimer les valeurs non presentes dans les
   * specs du champ dans ce formulaire, mais qui le sont peut
   * etre dans le meme champ dans un autre formulaire (cas d'un concept)
   *
   * @param CExClassField $field
   * @param $value
   *
   * @return string
   */
  static function typeSetSpecIntersect($field, $value) {
    $field_spec = $field->getSpecObject();
    
    if (!$field_spec instanceof CSetSpec) {
      return $value;
    }
    
    $values = explode("|", $value);
    $values = array_intersect($values, $field_spec->_list);
    
    return implode("|", $values);
  }

  /**
   * Load ExLinks
   *
   * @return CExLink[]
   */
  function loadRefsLinks(){
    $where = array(
      "ex_class_id"  => "= '$this->_ex_class_id'",
      "ex_object_id" => "= '$this->ex_object_id'",
    );

    $ex_link = new CExLink();

    /** @var CExLink[] $list */
    $list = $ex_link->loadList($where);

    foreach ($list as $_link) {
      switch ($_link->level) {
        case "object":
          $_object = $_link->loadTargetObject();
          $this->setObject($_object);
          break;

        case "ref1":
          $_object = $_link->loadTargetObject();
          $this->setReferenceObject_1($_object);
          break;

        case "ref2":
          $_object = $_link->loadTargetObject();
          $this->setReferenceObject_2($_object);
          break;

        case "add":
          $_object = $_link->loadTargetObject();
          $this->setAdditionalObject($_object);
          break;
      }
    }

    return $list;
  }

  /**
   * attention aux dates, il faut surement checker le log de derniere modif des champs du concept
   *
   * @fixme pas trop optimis�
   */
  function getReportedValues(CExClassEvent $event){
    $ex_class = $this->_ref_ex_class;
    $fields = $ex_class->loadRefsAllFields(true);

    if ($this->_id) {
      return $fields;
    }

    self::$_multiple_load = true;
    CExClassField::$_load_lite = true;

    $this->loadRefsLinks();

    $latest_ex_objects = array(
      $ex_class->getLatestExObject($this->_ref_object),
      $ex_class->getLatestExObject($this->_ref_reference_object_1),
      $ex_class->getLatestExObject($this->_ref_reference_object_2),
    );

    if ($this->_ref_object->_id) {
      $this->_ref_object->loadComplete();
    }

    if ($this->_ref_reference_object_1->_id) {
      $this->_ref_reference_object_1->loadComplete();
    }

    if ($this->_ref_reference_object_2->_id) {
      $this->_ref_reference_object_2->loadComplete();
    }

    CStoredObject::massLoadFwdRef($fields, "ex_group_id");
    $all_concepts = CStoredObject::massLoadFwdRef($fields, "concept_id");
    $all_back_fields = CStoredObject::massLoadBackRefs($all_concepts, "class_fields");

    $ex_groups = CStoredObject::massLoadFwdRef($all_back_fields, "ex_group_id");
    CStoredObject::massLoadFwdRef($ex_groups, "ex_class_id");

    // Cache de concepts
    $concepts = array();
    $ex_classes = array();
    
    // on cherche les champs report�s de l'objet courant
    foreach ($fields as $_field) {
      $field_name = $_field->name;
      $this->_reported_fields[$field_name] = null;
      
      // valeur par d�faut
      $spec_obj = $_field->getSpecObject();
      $this->$field_name = CExClassField::unescapeProp($spec_obj->default);

      $_concept = null;
      if ($_field->concept_id) {
        $_concept = $_field->loadRefConcept();
      }

      // si champ pas report�, on passe au suivant
      if (!($_field->report_class || $_field->concept_id && $_concept->native_field)) {
        continue;
      }

      // Native fields
      if ($_concept && $_concept->native_field) {
        list($_class, $_path) = explode(" ", $_concept->native_field, 2);

        if (isset($this->_preview)) {
          $this->_reported_fields[$field_name] = new $_class();
          $this->$field_name = "Test";
        }
        else {
          if ($this->_ref_object->_class == $_class) {
            $_object = $this->_ref_object;
          }
          elseif ($this->_ref_reference_object_1->_class == $_class) {
            $_object = $this->_ref_reference_object_1;
          }
          elseif ($this->_ref_reference_object_2->_class == $_class) {
            $_object = $this->_ref_reference_object_2;
          }

          list($_object, $_path) = CExClassConstraint::getFieldAndObjectStatic($_object, $_path);
          $_resolved = CExClassConstraint::resolveObjectFieldStatic($_object, $_path);

          $_obj        = $_resolved["object"];
          $_field_name = $_resolved["field"];

          $this->_reported_fields[$field_name] = $_object;
          $this->$field_name = $_obj->$_field_name;
        }

        if ($this->$field_name) {
          continue;
        }
      }
      
      $_report_class = $_field->report_class;
      
      // si champ bas� sur un concept, il faut parcourir 
      // tous les formulaires qui ont un champ du meme concept
      
      if ($_field->concept_id) {
        if (!isset($concepts[$_field->concept_id])) {
          $_concept_fields = $_concept->loadRefClassFields();

          foreach ($_concept_fields as $_concept_field) {
            if (!isset($ex_classes[$_concept_field->ex_group_id])) {
              $ex_classes[$_concept_field->ex_group_id] = $_concept_field->loadRefExClass();
            }
            else {
              $_concept_field->_ref_ex_class = $ex_classes[$_concept_field->ex_group_id];
            }
          }
          
          $concepts[$_field->concept_id] = array(
            $_concept,
            $_concept_fields,
          );
        }
        else {
          list(, $_concept_fields) = $concepts[$_field->concept_id];
        }

        /** @var CExObject $_latest */
        $_latest = null;
        $_latest_value = null;
        
        // on regarde tous les champs du concept
        foreach ($_concept_fields as $_concept_field) {
          $_ex_class = $_concept_field->_ref_ex_class;

          $_concept_latest = null;
          
          if ($this->_ref_object->_class == $_report_class) {
            $_concept_latest = $_ex_class->getLatestExObject($this->_ref_object);
          }
          elseif ($this->_ref_reference_object_1->_class == $_report_class) {
            $_concept_latest = $_ex_class->getLatestExObject($this->_ref_reference_object_1);
          }
          elseif ($this->_ref_reference_object_2->_class == $_report_class) {
            $_concept_latest = $_ex_class->getLatestExObject($this->_ref_reference_object_2);
          }
          
          // si pas d'objet precedemment enregistr�
          if (!$_concept_latest || !$_concept_latest->_id || $_concept_latest->{$_concept_field->name} == "") {
            continue;
          }
          
          if (!$_latest) {
            $_latest = $_concept_latest;
            $_latest_value = $_latest->{$_concept_field->name};
          }
          else {
            $_date = $_concept_latest->getEditDate();

            if ($_date > $_latest->getEditDate()) {
              $_latest = $_concept_latest;
              $_latest_value = $_latest->{$_concept_field->name};
            }
          }
        }
        
        if ($_latest) {
          $_latest->loadTargetObject()->loadComplete();
          
          $this->_reported_fields[$field_name] = $_latest;
          $this->$field_name = self::typeSetSpecIntersect($_field, $_latest_value);
        }
      }

      // Ceux de la meme exclass
      else {
        $escape = true;
        foreach ($latest_ex_objects as $_latest_ex_object) {
          if ($_latest_ex_object->_id) {
            $escape = false;
            break;
          }
        }
        
        if ($escape) {
          continue;
        }

        /** @var CMbObject $_base */
        /*
         * Comprendre pourquoi parfois il n'y a pas de $_latest_ex_object
         */
        $_base = null;
        foreach ($latest_ex_objects as $_latest_ex_object) {
          if (!$_latest_ex_object) {
            continue;
          }

          if ($_latest_ex_object->_ref_reference_object_1 && $_latest_ex_object->_ref_reference_object_1->_class == $_report_class) {
            $_base = $_latest_ex_object->_ref_reference_object_1;
            break;
          }
          elseif ($_latest_ex_object->_ref_reference_object_2 && $_latest_ex_object->_ref_reference_object_2->_class == $_report_class) {
            $_base = $_latest_ex_object->_ref_reference_object_2;
            break;
          }
          elseif ($_latest_ex_object->_ref_object && $_latest_ex_object->_ref_object->_class == $_report_class) {
            $_base = $_latest_ex_object->_ref_object;
            break;
          }
        }

        if ($this->_ref_object->_id && !$_base) {
          //$_field_view = CAppUI::tr("$this->_class-$_field->name");
          //CAppUI::setMsg("Report de donn�es impossible pour le champ '$_field_view'", UI_MSG_WARNING);
          continue;
        }

        if ($_base->$field_name == "") {
          continue;
        }
        
        $_base->loadTargetObject()->loadComplete();
        $_base->loadLastLog();
        
        $this->_reported_fields[$field_name] = $_base;
        $this->$field_name = self::typeSetSpecIntersect($_field, $_base->$field_name);
      }
    }
    
    self::$_multiple_load = false;
    CExClassField::$_load_lite = false;

    return $fields;
  }
  
  function loadOldObject() {
    if (!$this->_old) {
      $this->_old = new self;
      $this->_old->_ex_class_id = $this->_ex_class_id;
      $this->_old->setExClass();
      $this->_old->load($this->_id);
    }
    
    return $this->_old; 
  }

  private function camelize($str) {
    return preg_replace_callback(
      "/-+(.)?/",
      function ($m) {
        return ucwords($m[1]);
      },
      $str
    );
  }

  /**
   * @param CExClassField[] $fields Fields to get display of
   *
   * @return void
   */
  function setFieldsDisplay($fields){
    CStoredObject::massLoadBackRefs($fields, "predicates");

    $default = array();
    $this->_fields_display_struct = array();

    $all_predicates = array();
    foreach ($fields as $_field) {
      if ($_field->disabled) {
        continue;
      }

      if ($_field->_count["predicates"] > 0) {
        $all_predicates += $_field->loadRefPredicates();
      }
    }

    CStoredObject::massLoadBackRefs($all_predicates, "properties");
    CStoredObject::massLoadBackRefs($all_predicates, "display_fields");
    CStoredObject::massLoadBackRefs($all_predicates, "display_messages");
    CStoredObject::massLoadBackRefs($all_predicates, "display_subgroups");

    foreach ($fields as $_field) {
      if ($_field->disabled) {
        continue;
      }

      $_affected = array();
      $_predicates = array();

      if ($_field->_count["predicates"] > 0) {
        $_predicates = $_field->loadRefPredicates();
      }

      foreach ($_predicates as $_predicate) {
        $_struct = array(
          "operator" => $_predicate->operator,
          "value"    => $_predicate->value,
          "display"  => array(
            "fields"    => array(),
            "messages"  => array(),
            "subgroups" => array(),
          ),
          "style"    => array(
            "fields"    => array(),
            "messages"  => array(),
            "subgroups" => array(),
          ),
        );

        // Fields
        $_display_fields = $_predicate->loadBackRefs("display_fields");
        foreach ($_display_fields as $_display) {
          $_struct["display"]["fields"][] = $_display->name;
        }

        // Messages
        $_display_messages = $_predicate->loadBackRefs("display_messages");
        foreach ($_display_messages as $_display) {
          $_struct["display"]["messages"][] = $_display->_guid;
        }

        // Subgroups
        $_display_subgroups = $_predicate->loadBackRefs("display_subgroups");
        foreach ($_display_subgroups as $_display) {
          $_struct["display"]["subgroups"][] = $_display->_guid;
        }

        $_styles = $_predicate->loadRefProperties();
        foreach ($_styles as $_style) {
          /** @var CExClassField|CExClassMessage|CExClassFieldSubgroup $_ref_object */
          $_ref_object = $_style->loadTargetObject();

          $default[$_ref_object->_guid] = $_ref_object->getDefaultProperties();

          switch ($_style->object_class) {
            case "CExClassField":
              $_field_name = $_ref_object->name;
              $_struct["style"]["fields"][] = array(
                "name"      => $_field_name,
                "type"      => $_style->type,
                "camelized" => $this->camelize($_style->type),
                "value"     => $_style->value,
              );

              $_affected[$_ref_object->_guid] = array(
                "type" => "field",
                "name" => $_field_name,
              );
              break;

            case "CExClassMessage":
              $_struct["style"]["messages"][] = array(
                "guid"      => $_ref_object->_guid,
                "type"      => $_style->type,
                "camelized" => $this->camelize($_style->type),
                "value"     => $_style->value,
              );

              $_affected[$_ref_object->_guid] = array(
                "type" => "message",
                "guid" => $_ref_object->_guid,
              );
              break;

            case "CExClassFieldSubgroup":
              $_struct["style"]["subgroups"][] = array(
                "guid"      => $_ref_object->_guid,
                "type"      => $_style->type,
                "camelized" => $this->camelize($_style->type),
                "value"     => $_style->value,
              );

              $_affected[$_ref_object->_guid] = array(
                "type" => "subgroup",
                "guid" => $_ref_object->_guid,
              );
              break;
          }
        }

        $this->_fields_display_struct[$_field->name]["predicates"][] = $_struct;
      }

      if (!empty($_affected)) {
        $this->_fields_display_struct[$_field->name]["affects"] = $_affected;
      }
    }

    $this->_fields_default_properties = $default;
  }

  /**
   * @see parent::store()
   */
  function store(){
    if ($msg = $this->check()) {
      return $msg;
    }

    $new_object = !$this->_id;
    $now = CMbDT::dateTime();
    
    if ($new_object) {
      $this->group_id = CGroups::loadCurrent()->_id;
      $this->datetime_create = $now;
      $this->owner_id = CMediusers::get()->_id;
    }

    $this->datetime_edit = $now;

    if ($msg = parent::store()) {
      return $msg;
    }

    // Links
    if ($new_object) {
      $fields = array(
        "object" => array("object_class",     "object_id"),
        "ref1"   => array("reference_class",  "reference_id"),
        "ref2"   => array("reference2_class", "reference2_id"),
        "add"    => array("additional_class", "additional_id"),
      );

      foreach ($fields as $_level => $_field) {
        if ($this->{$_field[0]} && $this->{$_field[1]}) {
          $link = new CExLink();
          $link->object_class = $this->{$_field[0]};
          $link->object_id    = $this->{$_field[1]};
          $link->group_id     = $this->group_id;
          $link->ex_object_id = $this->_id;
          $link->ex_class_id  = $this->_ex_class_id;
          $link->level        = $_level;
          if ($msg = $link->store()) {
            return $msg;
          }
        }
      }
    }

    return null;
  }
  
  /// Low level methods ///////////
  /**
   * @see parent::bind()
   */
  function bind($hash, $doStripSlashes = true) {
    $this->setExClass();
    return parent::bind($hash, $doStripSlashes);
  }

  /**
   * @see parent::load()
   */
  function load($id = null) {
    $this->setExClass();
    return parent::load($id);
  }

  /**
   * @see parent::getPlainFields()
   *
   * Used in updatePlainFields
   */
  function getPlainFields() {
    $this->setExClass();
    return parent::getPlainFields();
  }

  /**
   * @see parent::fieldModified()
   */
  function fieldModified($field, $value = null) {
    $this->setExClass();
    return parent::fieldModified($field, $value);
  }

  /**
   * @see parent::loadQueryList()
   */
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

  /**
   * @see parent::checkProperty()
   *
   * needed or will throw errors in the field specs
   */
  function checkProperty($name) {
    $class = $this->_class;
    $this->_class = get_class($this);

    // Sauvegarde se props car elles sont r�initialis�es
    $props = $this->_props;
    
    $spec = $this->_specs[$name];
    $ret = $spec->checkPropertyValue($this);

    $this->_props = $props;
    
    $this->_class = $class;
    return $ret;
  }
  /// End low level methods /////////

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->key = "ex_object_id";
    return $spec;
  }

  /**
   * Get ExClass id
   *
   * @return int
   */
  function getClassId(){
    return ($this->_ex_class_id ? $this->_ex_class_id : $this->_own_ex_class_id);
  }

  /**
   * Get database table name
   *
   * @return string
   */
  function getTableName(){
    return "ex_object_".$this->getClassId();
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $this->loadRefExClass();
    $this->_spec->table = $this->getTableName();
    $class_id = $this->getClassId();

    $class = get_class($this)."_".$class_id;
    $props = parent::getProps();
    $props["ex_object_id"]     = "ref class|$class show|0";
    $props["_ex_class_id"]     = "ref class|CExClass";
    $props["_event_name"]      = "str";
    
    $props["group_id"]         = "ref class|CGroups notNull";
    
    $props["reference_class"]  = "str class";
    $props["reference_id"]     = "ref class|CMbObject meta|reference_class";

    $props["reference2_class"] = "str class";
    $props["reference2_id"]    = "ref class|CMbObject meta|reference2_class";

    $props["additional_class"] = "str class";
    $props["additional_id"]    = "ref class|CMbObject meta|additional_class";

    $props["datetime_create"]  = "dateTime";
    $props["datetime_edit"]    = "dateTime";
    $props["owner_id"]         = "ref class|CMediusers";
    
    if (self::$_load_lite || !$class_id) {
      return $props;
    }
    
    $fields = $this->_ref_ex_class->loadRefsAllFields(true);
    
    foreach ($fields as $_field) {
      // don't redeclare them more than once
      if (isset($this->{$_field->name})) {
        break;
      }
      
      $this->{$_field->name} = null; // declaration of the field
      $props[$_field->name] = $_field->prop; // declaration of the field spec
      $this->_fields_display[$_field->name] = true; // display the field by default
    }
    
    return $props;
  }

  /**
   * @see parent::getSpecs()
   */
  function getSpecs(){
    $ex_class_id = $this->getClassId();
    $this->_class = get_class($this)."_$ex_class_id";
    
    if ($this->_id && isset(self::$_ex_specs[$ex_class_id])) {
      return self::$_ex_specs[$ex_class_id];
    }
    
    $specs = @parent::getSpecs(); // sometimes there is "list|"
        
    foreach ($specs as $_field => $_spec) {
      if ($_spec instanceof CEnumSpec) {
        foreach ($_spec->_locales as $key => $locale) {
          $specs[$_field]->_locales[$key] = CAppUI::tr("$this->_class.$_field.$key");
        }
      }
    }

    if ($ex_class_id) {
      self::$_ex_specs[$ex_class_id] = $specs;
    }

    return $specs;
  }

  /**
   * @see parent::loadLogs()
   */
  function loadLogs(){
    $this->setExClass();
    $ds = $this->_spec->ds;
    
    $where = array(
      "object_class" => $ds->prepare("=%", $this->_class),
      "object_id"    => $ds->prepare("=%", $this->_id)
    );
    
    $log = new CUserLog();
    $this->_ref_logs = $log->loadList($where, "user_log_id DESC", 100, null, null, "object_id");

    // loadRefsFwd will fail because the ExObject's class doesn't really exist
    foreach ($this->_ref_logs as &$_log) {
      $_log->loadRefUser();
    }
    
    // the first is at the end because of the date order !
    $this->_ref_first_log = end($this->_ref_logs);
    $this->_ref_last_log  = reset($this->_ref_logs);
  }

  /**
   * Get the reference object of the right class
   *
   * @param string $class The class name
   *
   * @return CMbObject|null
   */
  function getReferenceObject($class) {
    $fields = array(
      "object_class"     => "object_id", 
      "reference_class"  => "reference_id", 
      "reference2_class" => "reference2_id", 
    );
    
    foreach ($fields as $_class => $_id) {
      if ($this->$_class == $class) {
        return $this->loadFwdRef($_id);
      }
    }
  }
  
  static function getValidObject($object_class) {
    if (!preg_match('/^CExObject_(\d+)$/', $object_class, $matches)) {
      return false;
    }
    
    $ex_class = new CExClass();
    if (!$ex_class->load($matches[1])) {
      return false;
    }
    
    return new CExObject($ex_class->_id);
  }

  /**
   * Counts ExObject stored for the object
   *
   * @param CMbObject $object The object to count the ExObjects for
   *
   * @return CExObject[][] The list, with ExClass IDs as key and counts as value
   */
  static function loadExObjectsFor(CMbObject $object) {
    $group_id = CGroups::loadCurrent()->_id;

    $where = array(
      "group_id = '$group_id' OR group_id IS NULL",
    );

    $ex_class = new CExClass();
    $ex_class_ids = $ex_class->loadIds($where, "name");

    $ds = $ex_class->_spec->ds;
    $where = array(
      "object_class" => $ds->prepare("= %", $object->_class),
      "object_id"    => $ds->prepare("= %", $object->_id),
    );

    $ex_objects = array();

    foreach ($ex_class_ids as $_ex_class_id) {
      $_ex_object = new CExObject($_ex_class_id);

      if ($_ex_object->countList($where) == 0) {
        continue;
      }

      $_list = $_ex_object->loadList($where);

      if (count($_list) > 0) {
        foreach ($_list as $_ex_object) {
          $_ex_object->_ex_class_id = $_ex_class_id;
          $_ex_object->load();
        }

        $ex_objects[$_ex_class_id] = $_list;
      }
    }

    return $ex_objects;
  }

  /**
   * Adds the list of forms to a template manager
   *
   * @param CTemplateManager $template The template manager
   * @param CMbObject        $object   The host object
   * @param string           $name     The field name
   *
   * @return void
   */
  static function addFormsToTemplate(CTemplateManager $template, CMbObject $object, $name) {
    static $ex_classes = null;

    if (!CAppUI::conf("forms CExClassField doc_template_integration")) {
      return;
    }

    $params = array(
      "detail"          => 3,
      "reference_id"    => $object->_id,
      "reference_class" => $object->_class,
      "target_element"  => "ex-objects-$object->_id",
      "print"           => 1,
      "limit"           => null,
    );

    $formulaires = "";

    $params["limit"] = 1;
    if ($object->_id) {
      $formulaires = CApp::fetch("forms", "ajax_list_ex_object", $params);
      $formulaires = preg_replace("/\s+/", " ", $formulaires); // Remove CRLF which CKEditor transform to <br />
    }
    $template->addProperty("$name - Formulaires - Dernier", $formulaires, null, false);

    $params["limit"] = 5;
    if ($object->_id) {
      $formulaires = CApp::fetch("forms", "ajax_list_ex_object", $params);
      $formulaires = preg_replace("/\s+/", " ", $formulaires); // Remove CRLF which CKEditor transform to <br />
    }
    $template->addProperty("$name - Formulaires - 5 derniers", $formulaires, null, false);

    $params["limit"] = 1;
    $params["only_host"] = 1;
    if ($object->_id) {
      $formulaires = CApp::fetch("forms", "ajax_list_ex_object", $params);
      $formulaires = preg_replace("/\s+/", " ", $formulaires); // Remove CRLF which CKEditor transform to <br />
    }
    $template->addProperty("$name - Formulaires - Li�s", $formulaires, null, false);

    self::$_multiple_load = true;

    CExObject::initLocales();

    if ($ex_classes === null) {
      $group_id = CGroups::loadCurrent()->_id;
      $where = array(
        "ex_class.group_id = '$group_id' OR group_id IS NULL",
        "ex_class_field.in_doc_template" => "= '1'"
      );

      $ljoin = array(
        "ex_class_field_group" => "ex_class_field_group.ex_class_id = ex_class.ex_class_id",
        "ex_class_field"       => "ex_class_field.ex_group_id = ex_class_field_group.ex_class_field_group_id",
      );

      $ex_class = new CExClass();
      /** @var CExClass[] $ex_classes */
      $ex_classes = $ex_class->loadList($where, "name", null, "ex_class.ex_class_id", $ljoin);


      foreach ($ex_classes as $_ex_class) {
        $_ex_class->_all_fields = $_ex_class->loadRefsAllFields();
      }
    }

    foreach ($ex_classes as $_ex_class) {
      $_name = "Form. ".str_replace(" - ", " ", $_ex_class->name);
      $fields = $_ex_class->_all_fields;
      $_class_name = $_ex_class->getExClassName();

      $_ex_object = null;
      if ($object->_id) {
        $_ex_object = $_ex_class->getLatestExObject($object);
      }

      if ($template->valueMode && (!$_ex_object || !$_ex_object->_id)) {
        continue;
      }

      foreach ($fields as $_field) {
        if (!$_field->in_doc_template) {
          continue;
        }

        $_field_name = str_replace(" - ", " ", CAppUI::tr("$_class_name-{$_field->name}"));

        $_template_field_name = "Sejour - $_name - $_field_name";
        $_template_key_name = "CExObject|$_ex_class->_id|$_field->name";

        $_has_value = ($_ex_object && $_ex_object->_id && $_field->name != "");
        $_template_value = ($_has_value ? $_ex_object->getFormattedValue($_field->name) : "");

        $template->addAdvancedData($_template_field_name, $_template_key_name, $_template_value);
      }
    }

    self::$_multiple_load = false;
  }

  /**
   * @param CMbObject $object
   *
   * @return CExObject[][]
   */
  static function getExObjectsOf(CMbObject $object) {

    CExClassField::$_load_lite = true;
    CExObject::$_multiple_load = true;

    $group_id = CGroups::loadCurrent()->_id;
    $where = array(
      "group_id = '$group_id' OR group_id IS NULL",
    );

    if (empty(CExClass::$_list_cache)) {
      $ex_class = new CExClass;
      CExClass::$_list_cache = $ex_class->loadList($where, "name");

      if (!CExObject::$_locales_cache_enabled) {
        foreach (CExClass::$_list_cache as $_ex_class) {
          foreach ($_ex_class->loadRefsGroups() as $_group) {
            $_group->loadRefsFields();

            foreach ($_group->_ref_fields as $_field) {
              $_field->updateTranslation();
            }
          }
        }
      }
    }

    $ex_objects = array();

    $limit = 1;

    $ref_objects_cache = array();

    foreach (CExClass::$_list_cache as $_ex_class_id => $_ex_class) {
      $_ex_object = new CExObject($_ex_class_id);

      $where = array(
        "(reference_class  = '$object->_class' AND reference_id  = '$object->_id') OR
         (reference2_class = '$object->_class' AND reference2_id = '$object->_id') OR
         (object_class     = '$object->_class' AND object_id     = '$object->_id')"
      );

      $ljoin = array();

      /** @var CExObject[] $_ex_objects */
      $_ex_objects = $_ex_object->loadList($where, "{$_ex_object->_spec->key} DESC", $limit, null, $ljoin);

      foreach ($_ex_objects as $_ex) {
        $_ex->_ex_class_id = $_ex_class_id;
        $_ex->load();

        $guid = "$_ex->object_class-$_ex->object_id";

        if (!isset($ref_objects_cache[$guid])) {
          $_ex->loadTargetObject()->loadComplete(); // to get the view
          $ref_objects_cache[$guid] = $_ex->_ref_object;
        }
        else {
          $_ex->_ref_object = $ref_objects_cache[$guid];
        }

        if ($_ex->additional_id) {
          $_ex->loadRefAdditionalObject();
        }

        $ex_objects[$_ex_class_id][$_ex->_id] = $_ex;
      }

      if (isset($ex_objects[$_ex_class_id])) {
        krsort($ex_objects[$_ex_class_id]);
      }
    }

    ksort($ex_objects);

    return $ex_objects;
  }

  /**
   * Custom delete, will delete any link
   *
   * @see parent::delete()
   */
  function delete(){
    $ex_object_id = $this->_id;
    $ex_class_id = $this->_ex_class_id;

    if ($msg = parent::delete()) {
      return $msg;
    }

    // Remove CExLinks
    $where = array(
      "ex_class_id"  => " = '$ex_class_id'",
      "ex_object_id" => " = '$ex_object_id'",
    );

    $ex_link = new CExLink();
    $ex_links = $ex_link->loadList($where);

    foreach ($ex_links as $_ex_link) {
      $_ex_link->delete();
    }

    return null;
  }

  /**
   * Get owner : the person who created $this
   *
   * @return CMediusers
   */
  function loadRefOwner() {
    $this->getOwnerId();
    return $this->_ref_owner = $this->loadFwdRef("owner_id");
  }

  /**
   * Get owner ID, save it if it's not present
   *
   * @return int
   */
  function getOwnerId(){
    if (!$this->owner_id) {
      $this->updateCreationFields();
    }

    return $this->owner_id;
  }

  /**
   * Get creation date, save it if it's not present
   *
   * @return string
   */
  function getCreateDate(){
    if (!$this->datetime_create) {
      $this->updateCreationFields();
    }

    return $this->datetime_create;
  }

  /**
   * Get owner ID, save it if it's not present
   *
   * @return string
   */
  function getEditDate(){
    if (!$this->datetime_edit) {
      $this->updateEditFields();
    }

    return $this->datetime_edit;
  }

  /**
   * Update creation fields : datetime_create and owner_id
   *
   * @return void
   */
  function updateCreationFields(){
    if (!$this->_id || ($this->datetime_create && $this->owner_id)) {
      return;
    }

    $log = $this->loadFirstLog();

    // Don't use store here because we don't want to log this action ...
    $ds = $this->getDS();
    $table_name = $this->getTableName();
    $query = $ds->prepare(
      "UPDATE $table_name SET datetime_create = ?1, owner_id = ?2 WHERE ex_object_id = ?3;",
      $log->date,
      $log->user_id,
      $this->_id
    );
    $ds->exec($query);

    $this->datetime_create = $log->date;
    $this->owner_id = $log->user_id;
  }

  /**
   * Update creation fields : datetime_create and owner_id
   *
   * @return void
   */
  function updateEditFields(){
    if (!$this->_id || $this->datetime_edit) {
      return;
    }

    $log = $this->loadLastLog();

    // Don't use store here because we don't want to log this action ...
    $ds = $this->getDS();
    $table_name = $this->getTableName();
    $query = $ds->prepare(
      "UPDATE $table_name SET datetime_edit = ?1 WHERE ex_object_id = ?2;",
      $log->date,
      $this->_id
    );
    $ds->exec($query);

    $this->datetime_edit = $log->date;
  }
}
