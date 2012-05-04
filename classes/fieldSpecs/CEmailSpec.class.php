<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CEmailSpec extends CMbFieldSpec {
  function getSpecType() {
    return "email";
  }
  
  function getDBSpec(){
    return "VARCHAR(50)";
  }
  
  function getValue($object, $smarty = null, $params = array()) {
    $propValue = $object->{$this->fieldName};
    
    return ($propValue !== null && $propValue !== "") ? 
      "<a class=\"email\" href=\"mailto:$propValue\">$propValue</a>" : 
      "";
  }
  
  function checkProperty($object){
    if (!preg_match("/^[-a-z0-9\._]+@[-a-z0-9\.]+\.[a-z]{2,4}$/i", $object->{$this->fieldName})) {
      return "Le format de l'email n'est pas valide";
    }
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    $field = htmlspecialchars($this->fieldName);
    $value = htmlspecialchars($value);
    $class = htmlspecialchars("$className $this->prop");
    
    $form  = CMbArray::extract($params, "form");
    $extra = CMbArray::makeXmlAttributes($params);
    
    return "<input type=\"email\" name=\"$field\" value=\"$value\" class=\"$class\" $extra />";
  }
  
  function sample(&$object, $consistent = true) {
    parent::sample($object, $consistent);
    $object->{$this->fieldName} = "noone@nowhere.com";
  }
}

?>