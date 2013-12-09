<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Numeric reference to a CStoredObject
 */
class CRefSpec extends CMbFieldSpec {
  public $class;
  public $cascade;
  public $unlink;
  public $nullify;
  public $meta;
  public $purgeable;

  /**
   * @see parent::getSpecType()
   */
  function getSpecType() {
    return "ref";
  }

  /**
   * @see parent::getDBSpec()
   */
  function getDBSpec(){
    return "INT(11) UNSIGNED";
  }

  /**
   * @see parent::getOptions()
   */
  function getOptions(){
    return array(
      'class'     => 'class',
      'cascade'   => 'bool',
      'unlink'    => 'bool',
      'nullify'   => 'bool',
      'meta'      => 'field',
      'purgeable' => 'bool',
    ) + parent::getOptions();
  }

  /**
   * @see parent::getValue()
   */
  function getValue($object, $smarty = null, $params = array()) {
    $tooltip = CMbArray::extract($params, "tooltip");
    $ref = $object->loadFwdRef($this->fieldName, true);
    
    if ($ref->_id && $this->fieldName != $object->_spec->key) {
      return $tooltip ?
        "<span onmouseover=\"ObjectTooltip.createEx(this, '$ref->_guid')\">$ref->_view</span>" :
        $ref->_view;
    }
 
    return $object->{$this->fieldName};
  }

  /**
   * @see parent::checkProperty()
   */
  function checkProperty($object){
    if ($this->notNull && $this->nullify) {
      return "Sp�cifications de propri�t� incoh�rentes entre 'notNull' et 'nullify'";
    }
    
    $fieldName = $this->fieldName;
    $propValue = CMbFieldSpec::checkNumeric($object->$fieldName, true);
    
    if ($propValue === null || $object->$fieldName === "") {
      return "N'est pas une r�f�rence (format non num�rique)";
    }
    
    if ($propValue == 0) {
      return "ne peut pas �tre une r�f�rence nulle";
    }
    
    if ($propValue < 0) {
      return "N'est pas une r�f�rence (entier n�gatif)";
    }
    
    if (!$this->class and !$this->meta) {
      return "Type d'objet cible on d�fini";
    }
    
    $class = $this->class;
    if ($meta = $this->meta) {
      $class = $object->$meta;
    }
    
    // Gestion des objets �tendus ayant une pseudo-classe
    $ex_object = CExObject::getValidObject($class);
    if ($ex_object) {
      if (!$this->unlink && !$ex_object->load($propValue)) {
        return "Objet r�f�renc� de type '$class' introuvable";      
      }
    }
    else {
      if (!is_subclass_of($class, "CStoredObject")) {
        return "La classe '$class' n'est pas une classe d'objet enregistr�e";
      }

      /** @var CStoredObject $ref */
      $ref = new $class;
      if (!$this->unlink && !$ref->idExists($propValue)) {
        return "Objet r�f�renc� de type '$class' introuvable";      
      }
    }

    return null;
  }
  
  /**
   * @param array $params Template params:
   *   - options : array of objects with IDs
   *   - choose  : string alternative for Choose default option
   *   - size    : interger for size of text input 
   * @see classes/CMbFieldSpec#getFormHtmlElement($object, $params, $value, $className)
   *
   * @return string
   */
  function getFormHtmlElement($object, $params, $value, $className) {
    if ($options = CMbArray::extract($params, "options")) {
      $field         = CMbString::htmlSpecialChars($this->fieldName);
      $className     = CMbString::htmlSpecialChars(trim("$className $this->prop"));
      $extra         = CMbArray::makeXmlAttributes($params);
      $choose        = CMbArray::extract($params, "choose", "Choose");
      $choose = CAppUI::tr($choose);
      
      $html = "\n<select name=\"$field\" class=\"$className\" $extra>";
      $html.= "\n<option value=\"\">&mdash; $choose</option>";
      foreach ($options as $_option) {
        $selected = $value == $_option->_id ? "selected=\"selected\"" : "";
        $html.= "\n<option value=\"$_option->_id\" $selected>$_option->_view</option>";
      }
      $html.= "\n</select>";
      
      return $html;
    }
    
    CMbArray::defaultValue($params, "size", 25);
    return $this->getFormElementText($object, $params, $value, $className);
  }

  /**
   * @see parent::getLitteralDescription()
   */
  function getLitteralDescription() {
    return "R�f�rence de classe, identifiant. ".
    parent::getLitteralDescription();
  }
}
