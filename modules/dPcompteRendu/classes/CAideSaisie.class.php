<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage dPcompteRendu
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$ 
 */

/**
 * Remplacement d'un mot-cl� par une plus longue cha�ne de caract�res
 * S'associe sur toute propri�t� d'une classe dont la spec contient helped
 */
class CAideSaisie extends CMbObject {
  // DB Table key
  public $aide_id;

  // DB References
  public $user_id;
  public $function_id;
  public $group_id;

  // DB fields
  public $class;
  public $field;
  public $name;
  public $text;
  public $depend_value_1;
  public $depend_value_2;

  // Form Fields
  public $_depend_field_1;
  public $_depend_field_2;
  public $_owner;
  public $_vw_depend_field_1;
  public $_vw_depend_field_2;
  public $_is_ref_dp_1;
  public $_is_ref_dp_2;
  public $_class_dp_1;
  public $_class_dp_2;
  public $_applied;

  /** @var CMediusers */
  public $_ref_user;

  /** @var CFunctions */
  public $_ref_function;

  /** @var CGroups */
  public $_ref_group;

  /** @var CMediusers|CFunctions|CGroups */
  public $_ref_owner;

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'aide_saisie';
    $spec->key   = 'aide_id';
    $spec->xor["owner"] = array("user_id", "function_id", "group_id");
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["user_id"]      = "ref class|CMediusers";
    $props["function_id"]  = "ref class|CFunctions";
    $props["group_id"]     = "ref class|CGroups";

    $props["class"]        = "str notNull";
    $props["field"]        = "str notNull";
    $props["name"]         = "str notNull seekable";
    $props["text"]         = "text notNull seekable";
    $props["depend_value_1"] = "str";
    $props["depend_value_2"] = "str";

    $props["_depend_field_1"] = "str";
    $props["_depend_field_2"] = "str";
    $props["_vw_depend_field_1"] = "str";
    $props["_vw_depend_field_2"] = "str";
    $props["_owner"]          = "enum list|user|func|etab";

    return $props;
  }

  /**
   * V�rifie l'unicit� d'une aide � la saisie
   * 
   * @return string
   */
  function check() {
    $msg = "";

    $ds = $this->_spec->ds;

    $where = array();
    if ($this->user_id) {
      $where["user_id"] = $ds->prepare("= %", $this->user_id);
    }
    else if ($this->function_id) {
      $where["function_id"] = $ds->prepare("= %", $this->function_id);
    }
    else {
      $where["group_id"] = $ds->prepare("= %", $this->group_id);
    }

    $where["class"]          = $ds->prepare("= %",  $this->class);
    $where["field"]          = $ds->prepare("= %",  $this->field);
    $where["depend_value_1"] = $ds->prepare("= %",  $this->depend_value_1);
    $where["depend_value_2"] = $ds->prepare("= %",  $this->depend_value_2);
    $where["text"]           = $ds->prepare("= %",  $this->text);
    $where["aide_id"]        = $ds->prepare("!= %", $this->aide_id);

    $sql = new CRequest();
    $sql->addSelect("count(aide_id)");
    $sql->addTable("aide_saisie");
    $sql->addWhere($where);

    $nb_result = $ds->loadResult($sql->makeSelect());

    if ($nb_result) {
      $msg .= "Cette aide existe d�j�<br />";
    }

    return $msg . parent::check();
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields() {
    parent::updateFormFields();

    $this->_view = $this->name;

    // Owner
    if ($this->user_id ) {
      $this->_owner = "user";
    }
    if ($this->function_id) {
      $this->_owner = "func";
    }
    if ($this->group_id) {
      $this->_owner = "etab"; 
    }

    // Depend fields
    if ($this->class) {
      $object = new $this->class;
      $helped = $object->_specs[$this->field]->helped;
      $this->_depend_field_1 = isset($helped[0]) ? $helped[0] : null; 
      $this->_depend_field_2 = isset($helped[1]) ? $helped[1] : null;

      switch ($this->class) {
        case "CTransmissionMedicale":
          $this->_class_dp_2 = "CCategoryPrescription";
          break;
        case "CObservationResult":
          $this->_class_dp_1 = "CObservationValueType";
          $this->_class_dp_2 = "CObservationValueUnit";
          break;
        case "CPrescriptionLineElement":
          $this->_class_dp_1 = "CElementPrescription";
      }
      $this->loadViewDependValues($object);
    }
    $this->searchRefsObject();
  }

  /**
   * Charge l'utilisateur associ� � l'aide
   * 
   * @param boolean $cached Charge l'utilisateur depuis le cache
   * 
   * @return CMediusers
   */
  function loadRefUser($cached = true){
    return $this->_ref_user = $this->loadFwdRef("user_id", $cached);
  }

  /**
   * Charge la fonction associ�e � l'aide
   * 
   * @param boolean $cached Charge la fonction depuis le cache
   * 
   * @return CFunctions
   */
  function loadRefFunction($cached = true){
    return $this->_ref_function = $this->loadFwdRef("function_id", $cached);
  }

  /**
   * Charge l'�tablissement associ� � l'aide
   * 
   * @param boolean $cached Charge l'�tablissement depuis le cache
   * 
   * @return CGroups
   */
  function loadRefGroup($cached = true){
    return $this->_ref_group = $this->loadFwdRef("group_id", $cached);
  }

  /**
   * @see parent::loadRefsFwd()
   */
  function loadRefsFwd() {
    $this->loadRefUser();
    $this->loadRefFunction();
    $this->loadRefGroup();
  }

  /**
   * Charge le propri�taire de l'aide
   * 
   * @return CMediusers|CFunctions|CGroups|null
   */
  function loadRefOwner(){
    $this->loadRefsFwd();
    if ($this->user_id) {
      return $this->_ref_owner = $this->_ref_user;
    }
    if ($this->function_id) {
      return $this->_ref_owner = $this->_ref_function;
    }
    if ($this->group_id) {
      return $this->_ref_owner = $this->_ref_group;
    }

    return null;
  }

  /**
   * Permission generic check
   * 
   * @param int $permType Type of permission : PERM_READ|PERM_EDIT|PERM_DENY
   * 
   * @return boolean
   */
  function getPerm($permType) {
    if (!$this->_ref_user) {
      $this->loadRefsFwd();
    }

    return $this->_ref_user->getPerm($permType);
  }

  /**
   * Traduit les depend fields
   * 
   * @param CMbObject $object L'objet sur lequel sont appliqu�es les valeurs de d�pendances 
   * 
   * @return void
   */
  function loadViewDependValues($object) {
    $locale = "$object->_class.$this->_depend_field_1.$this->depend_value_1";
    $this->_vw_depend_field_1 = (CAppUI::isTranslated($locale) ? CAppUI::tr($locale) : $this->depend_value_1);

    $locale = "$object->_class.$this->_depend_field_2.$this->depend_value_2";
    $this->_vw_depend_field_2 = (CAppUI::isTranslated($locale) ? CAppUI::tr($locale) : $this->depend_value_2);
  }

  /**
   * Charge les objets r�f�renc�s par l'aide
   * 
   * @return void
   */
  function searchRefsObject() {
    $this->_is_ref_dp_1 = false;
    $this->_is_ref_dp_2 = false;

    $object = new $this->class;
    $field = $this->field;
    $helped = array();
    if ($object->_specs[$field]->helped && !is_bool($object->_specs[$field]->helped)) {
      if (!is_array($object->_specs[$field]->helped)) {
        $helped = array($object->_specs[$field]->helped);
      }
      else {
        $helped = $object->_specs[$field]->helped;
      }
    }
    foreach ($helped as $i => $depend_field) {
      $spec = $object->_specs[$depend_field];
      if ($spec instanceof CRefSpec) {
        $key = "_is_ref_dp_".($i+1);
        $this->$key = true;
        $key = "depend_value_".($i+1);
        if (is_numeric($this->$key)) {
          $key_class = "_class_dp_".($i+1);
          $object_helped = new $this->$key_class;
          $object_helped->load($this->$key);
          $key_field = "_vw_depend_field_".($i+1);
          $this->$key_field = $object_helped->_view;
        }
      }
    }
  }
}
