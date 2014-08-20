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

class CExClassFieldPredicate extends CMbObject {
  public $ex_class_field_predicate_id;
  
  public $ex_class_field_id;
  public $operator;
  public $value;
  public $_value;
  public $_compute_view;

  /** @var CExClassField */
  public $_ref_ex_class_field;

  /** @var CExClassFieldProperty */
  public $_ref_properties;

  static $_load_lite = false;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "ex_class_field_predicate";
    $spec->key   = "ex_class_field_predicate_id";
    $spec->uniques["value"] = array("ex_class_field_id", "operator", "value");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["ex_class_field_id"] = "ref notNull class|CExClassField cascade seekable";
    $props["operator"]          = "enum notNull list|=|!=|>|>=|<|<=|startsWith|endsWith|contains|hasValue|hasNoValue default|=";
    $props["value"]             = "str notNull seekable";
    $props["_value"]            = "str";
    $props["_compute_view"]     = "bool";
    return $props;
  }

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["display_fields"]    = "CExClassField predicate_id";
    $backProps["display_messages"]  = "CExClassMessage predicate_id";
    $backProps["display_subgroups"] = "CExClassFieldSubgroup predicate_id";
    $backProps["properties"]        = "CExClassFieldProperty predicate_id";
    return $backProps;
  }

  /**
   * @see parent::loadView()
   */
  function loadView(){
    parent::loadView();

    if (!$this->_id) {
      return;
    }

    $field = $this->loadRefExClassField();

    $this->_value = "";
    if ($this->operator != "hasValue" && $this->operator != "hasNoValue") {
      $_ex_class_id = $field->loadRefExGroup()->ex_class_id;

      $_spec = $field->getSpecObject();
      $_obj = (object)array(
        "_class"     => "CExObject_$_ex_class_id",
        $field->name => $this->value,
      );

      $this->_value = $_spec->getValue($_obj);
    }

    $this->_view = $field->_view." ".$this->_specs["operator"]->_locales[$this->operator]." ".$this->_value;
  }
  
  /**
   * @param bool $cache [optional]
   *
   * @return CExClassField
   */
  function loadRefExClassField($cache = true){
    return $this->_ref_ex_class_field = $this->loadFwdRef("ex_class_field_id", $cache);
  }
  
  function getAutocompleteList($keywords, $where = null, $limit = null, $ljoin = null, $order = null) {
    $list = $this->loadList($where, null, null, null, $ljoin);

    /** @var self[] $real_list */
    $real_list = array();
    $re = preg_quote($keywords);
    $re = CMbString::allowDiacriticsInRegexp($re);
    $re = str_replace("/", "\\/", $re);
    $re = "/($re)/i";
    
    foreach ($list as $_match) {
      if ($keywords == "%" || $keywords == "" || preg_match($re, $_match->_view)) {
        $_match->loadView();
        $real_list[$_match->_id] = $_match;
      }
    }
    
    $views = CMbArray::pluck($real_list, "_view");
    array_multisort($views, $real_list);
    
    $empty = new self;
    $empty->_id = null;
    $empty->_guid = "$this->_class-$this->_id"; // FIXME
    $empty->_view = " -- ";
    array_unshift($real_list, $empty);
    
    return $real_list;
  }

  function checkValue($value){
    return CExClass::compareValues($value, $this->operator, $this->value);
  }

  /**
   * @see parent::store()
   */
  function store(){
    CExObject::$_locales_cache_enabled = false;

    if ($msg = parent::store()) {
      return $msg;
    }
    
    if ($this->_compute_view) {
      $this->loadView();
    }
    
    return null;
  }

  /**
   * @return CExClassFieldProperty[]
   */
  function loadRefProperties(){
    return $this->_ref_properties = $this->loadBackRefs("properties");
  }
}
