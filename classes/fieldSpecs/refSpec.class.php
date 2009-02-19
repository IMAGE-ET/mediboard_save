<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author S�bastien Fillonneau
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("mbFieldSpec");

class CRefSpec extends CMbFieldSpec {
  
  var $class   = null;
  var $cascade = null;
  var $unlink  = null;
  var $meta    = null;
  
  function getSpecType() {
    return("ref");
  }
  
  function getValue($object, $smarty = null, $params = null) {
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    $ref = new $this->class;
    if (($ref_object = $ref->load($propValue)) && ($fieldName != $object->_spec->key)) 
      return $ref_object->_view;
    else
      return $propValue;
  }

  function checkProperty($object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    $propValue = $this->checkNumeric($propValue, false);
      
    if ($propValue === null || $object->$fieldName === ""){
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
    
    if (!is_subclass_of($class, "CMbObject")) {
      return "La classe '$class' n'est pas une classe d'objet m�tier";
    }
    
    $ref = new $class;
    if (!$this->unlink && !$ref->load($propValue)) {
      return "Objet r�f�renc� de type '$class' introuvable";      
    }

    return null;
  }
  
  function getDBSpec(){
    return "INT(11) UNSIGNED";
  }
  
  function getFormHtmlElement($object, $params, $value, $className) {
    CMbArray::defaultValue($params, "size", 25);
    return $this->getFormElementText($object, $params, $value, $className);
  }
  
/*  function getFormHtmlElement($object, $params, $value, $className) {
    $formName = CMbArray::extract($params, "form");
    
    $html = mbLoadScript('modules/system/javascript/object_selector.js', true);
    $ref = $object->loadFwdRef($this->fieldName);
    if (!$ref) $ref = new $this->class;
    $id = $this->fieldName.'-'.$ref->_guid;
    $html .= '<input type="text" size="20" readonly="readonly" ondblclick="ObjectSelector[\'init'.$id.'\']()" name="selView'.$id.'" value="'.$ref->_view.'" />
              <button type="button" onclick="ObjectSelector.init[\'init'.$id.'\']()" class="search notext">Rechercher</button>
              <input type="hidden" name="selKey'.$id.'" value="'.$object->_id.'" />
              <input type="hidden" name="selClass'.$id.'" value="'.$this->class.'" />
              <script type="text/javascript">
              ObjectSelector.init[\'init'.$id.'\'] = function(){
                this.sForm     = "'.$formName.'";
                this.sId       = "selKey'.$id.'";
                this.sView     = "selView'.$id.'";
                this.sClass    = "selClass'.$id.'";
                this.onlyclass = true; 
                this.pop();
              }
            </script>';
    return $html;
  }*/
}

?>