<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author S�bastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");

class CBoolSpec extends CMbFieldSpec {
  
  function checkValues(){
    parent::checkValues();
    if($this->default === null){
      $this->default = 0;
    }
  }
  
  function getValue($object, $smarty, $params = null) {
    global $AppUI;
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    return $AppUI->_("bool.".$propValue);
  }
  
  function getSpecType() {
    return("bool");
  }
  
  function checkProperty($object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    $propValue = $this->checkNumeric($propValue, false);
    if($propValue === null){
      return "N'est pas une cha�ne num�rique";
    }
    if($propValue!=0 && $propValue!=1){
      return "Ne peut �tre diff�rent de 0 ou 1";
    }
    return null;
  }

  function getDBSpec(){
    return "enum('0','1')";
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    global $AppUI;
    $sHtml        = "";
    $field        = htmlspecialchars($this->fieldName);
    $separator    = CMbArray::extract($params, "separator");
    $extra        = CMbArray::makeXmlAttributes($params);
    
    for($i=1; $i>=0; $i--){
      $selected = ""; 
      if(($value !== null && $value === "$i") || ($value === null && "$i" === "$this->default")){
        $selected = "checked=\"checked\"";
      }
      $sHtml .= "<input type=\"radio\" name=\"$field\" value=\"$i\" $selected";
      if($i == 1) {
        $sHtml .= " class=\"".htmlspecialchars(trim($className." ".$this->prop))."\"";
      }elseif($className){
        $sHtml .= " class=\"".htmlspecialchars(trim($className))."\"";
      }
      $sHtml .= " $extra/><label for=\"".$field."_$i\">".$AppUI->_("bool.$i")."</label> ";
      if($i==1 && $separator){
        $sHtml .= $separator;
      }
    }
    return $sHtml;
  }
  
  function getLabelForElement($object, $params){
    return $this->fieldName."_1";
  }
}

?>