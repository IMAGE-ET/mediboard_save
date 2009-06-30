<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("mbFieldSpec");

class CPasswordSpec extends CMbFieldSpec {
  
  var $minLength     = null;
  
  function getSpecType() {
    return('password');
  }
  
  function getDBSpec(){
    return "VARCHAR(50)";
  }
  
  function getOptions(){
    return parent::getOptions() + array(
      'minLength' => 'num',
    );
  }
  
  // TODO: Factoriser les check
  function checkProperty($object) {
    $propValue = $object->{$this->fieldName};

    // minLength
    if($this->minLength){
      if(!$length = $this->checkLengthValue($this->minLength)){
        trigger_error("Sp�cification de longueur minimale invalide (longueur = $this->minLength)", E_USER_WARNING);
        return "Erreur syst�me";
      }     
      if (strlen($propValue) < $length) {
        return "Le mot de passe n'a pas la bonne longueur '$propValue' (longueur minimale souhait�e : $length)'";
      }
    }
    
    // notContaining
    if($field = $this->notContaining){
      if($msg = $this->checkTargetPropValue($object, $field)){
        return $msg;
      }
      $targetPropValue = $object->$field;  
      if (stristr($propValue, $targetPropValue)) {
        return "Le mot de passe ne doit pas contenir '$field->fieldName'";
      }
    }
    
    // notNear
    if($field = $this->notNear){
      if($msg = $this->checkTargetPropValue($object, $field)){
        return $msg;
      }
      $targetPropValue = $object->$field;  
      if (levenshtein($propValue, $targetPropValue) < 3) {
        return "Le mot de passe ressemble trop � '$field->fieldName'";
      }
    }
    
    // alphaAndNum
    if($this->alphaAndNum){
      if (!preg_match("/[A-z]/", $propValue) || !preg_match("/\d+/", $propValue)) {
        return 'Le mot de passe doit contenir au moins un chiffre ET une lettre';
      }
    }
    return null;
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    $field        = htmlspecialchars($this->fieldName);
    $extra        = CMbArray::makeXmlAttributes($params);
    $sHtml        = '<input type="password" name="'.$field.'"';    
    $sHtml       .= ' class="'.htmlspecialchars(trim($className.' '.$this->prop)).'" '.$extra.' />';
    $sHtml       .= '<span id="'.$field.'_message"></span>';
    return $sHtml;
  }
  
  function sample(&$object, $consistent = true) {
    parent::sample($object, $consistent);
    $object->{$this->fieldName} = self::randomString(array_merge(range('0','9'), range('a','z'), range('A','Z')), 8);
  }
}

?>