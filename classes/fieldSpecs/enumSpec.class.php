<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author S�bastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");

class CEnumSpec extends CMbFieldSpec {
  
  var $list = null;
  
  function getValue($object, $smarty, $params = null) {
    global $AppUI;
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    return htmlspecialchars($AppUI->_("$object->_class_name.$fieldName.$propValue"));
  }
  
  function getSpecType() {
    return("enum");
  }
  
  function checkValues(){
    parent::checkValues();
    if(!$this->list){
      trigger_error("Sp�cification 'list' manquante pour le champ '".$this->fieldName."' de la classe '".$this->className."'", E_USER_WARNING);
    }
  }
  
  function checkProperty($object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    $specFragments = explode("|", $this->list);
    if (!in_array($propValue, $specFragments)) {
      return "N'a pas une valeur possible";
    }
    return null;
  }
  
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $fieldName = $this->fieldName;
    $propValue =& $object->$fieldName;
    $specFragments = explode("|", $this->list);
    
    $propValue = $this->randomString($specFragments, 1);
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    $sHtml         = null;
    $field         = htmlspecialchars($this->fieldName);
    $typeEnum      = CMbArray::extract($params, "typeEnum", "select");
    $separator     = CMbArray::extract($params, "separator");
    $cycle         = CMbArray::extract($params, "cycle", 1);
    $defaultOption = CMbArray::extract($params, "defaultOption");
    $extra         = CMbArray::makeXmlAttributes($params);
    $enumsTrans    = $object->_enumsTrans[$field];
    
    if ($typeEnum == "select") {
      $sHtml       = "<select name=\"$field\"";
      $sHtml      .= " class=\"".htmlspecialchars(trim($className." ".$this->prop))."\" $extra>";
      
      if($defaultOption){
        if($value === null) {
          $sHtml    .= "<option value=\"\" selected=\"selected\">$defaultOption</option>";
        } else {
          $sHtml    .= "<option value=\"\">$defaultOption</option>";
        }
      }
      foreach($enumsTrans as $key => $item){
        if(($value !== null && $value === "$key") || ($value === null && "$key" === "$this->default" && !$defaultOption)){
          $selected = " selected=\"selected\""; 
        }else{
          $selected = "";
        }
        $sHtml    .= "<option value=\"$key\"$selected>$item</option>";
      }
      $sHtml      .= "</select>";
      return $sHtml;
    }

    if ($typeEnum == "radio"){
      $compteur    = 0;
      $sHtml       = "";
      
      foreach($enumsTrans as $key => $item){
        if(($value !== null && $value === "$key") || ($value === null && "$key" === "$this->default")){
          $selected = " checked=\"checked\""; 
        }else{
          $selected = "";
        }
        $sHtml    .= "<input type=\"radio\" name=\"$field\" value=\"$key\"$selected";
        if($compteur == 0) {
          $sHtml  .= " class=\"".htmlspecialchars(trim($className." ".$this->prop))."\"";
        }elseif($className != ""){
          $sHtml  .= " class=\"".htmlspecialchars(trim($className))."\"";
        }
        $sHtml    .= " $extra/><label for=\"".$field."_$key\">$item</label> ";
        $compteur++;
        if($compteur % $cycle == 0){
          $sHtml  .= $separator;
        }
      }
      
      return $sHtml;
    }
    
    trigger_error("mb_field: Type d'enumeration '$typeEnum' non pris en charge", E_USER_WARNING);
  }
  
  function getLabelForElement($object, &$params){
    $typeEnum  = CMbArray::extract($params, "typeEnum", "select");
    
    if($typeEnum == "select"){
      return $this->fieldName;
    }
    if($typeEnum == "radio"){
      $enumsTrans = array_flip($object->_enumsTrans[$this->fieldName]);
      return $this->fieldName."_".current($enumsTrans);
    }
    trigger_error("mb_field: Type d'enumeration '$typeEnum' non pris en charge", E_USER_WARNING);
    return null;
  }
  
  function getDBSpec() {
    $aSpecFragments = explode("|", $this->list);
    $type_sql = "ENUM('".implode("','", $aSpecFragments)."')";
    return $type_sql;
  }
}

?>