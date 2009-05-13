<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("mbFieldSpec");

class CBoolSpec extends CMbFieldSpec {
  
  var $_list = null;
  var $_locales = null;
    
  function __construct($className, $field, $prop = null, $aProperties = array()) {
    parent::__construct($className, $field, $prop, $aProperties);
    foreach ($this->_list = array(0,1) as $value) {
      $this->_locales[$value] = CAppUI::tr("bool.$value");
    }
  }
  
  function checkValues(){
    parent::checkValues();
    if($this->default === null){
      $this->default = 0;
    }
  }
  
  function getValue($object, $smarty = null, $params = null) {
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    return CAppUI::tr("bool.".$propValue);
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
    return "ENUM('0','1')";
  }
  
  function getFormHtmlElement($object, $params, $value, $className){
    $sHtml        = "";
    $field        = htmlspecialchars($this->fieldName);
    $typeEnum     = CMbArray::extract($params, "typeEnum", "radio");
    $separator    = CMbArray::extract($params, "separator");
    $disabled     = CMbArray::extract($params, "disabled");
    $default      = CMbArray::extract($params, "default", $this->default);
    $defaultOption = CMbArray::extract($params, "defaultOption");
    $extra        = CMbArray::makeXmlAttributes($params);
    
    if ($typeEnum === "radio") {
	    // Attributes for all inputs
	    $attributes = array(
	      "type" => "radio",
	      "name" => $field,
	    );
	    
	    if (null === $value) {
	      $value = "$default";
	    }
	    
	    for ($i = 1; $i >= 0; $i--) {
	      $attributes["value"] = "$i"; 
	      $attributes["checked"] = $value === "$i" ? "checked" : null; 
	      $attributes["disabled"] = $disabled === "$i" ? "disabled" : null; 
	      $attributes["class"] = $i == 1 ? $this->prop : null;
	      $attributes["class"].= $className ? $className : null;
	      
	      $xmlAttributes = CMbArray::makeXmlAttributes($attributes);
	      
	      $sHtml .= "\n<input $xmlAttributes $extra />";
	      
	      $sTr = CAppUI::tr("bool.$i");
	      $sHtml .= "\n<label for=\"{$field}_$i\">$sTr</label> ";
	      
	      if ($i != 0 && $separator){
	        $sHtml .= "\n$separator";
	      }
	    }
	    return $sHtml;
    } 
    
    if ($typeEnum === "checkbox"){
    	if(($value !== null && $value === "1")){
        $checked = " checked=\"checked\""; 
      }else{
        $checked = "";
        $value = "0";
      }
    	$sHtml = '<input type="checkbox" name="__'.$field.'" 
        onclick="$V(this.form.'.$field.', $V(this)?1:0);" '.$checked.' />';
    	$sHtml .= '<input type="hidden" name="'.$field.'" '.$extra.' value="'.$value.'" />';
    
    	return $sHtml;
    } 

    if ($typeEnum === "select") {
      $sHtml       = "<select name=\"$field\"";
      $sHtml      .= " class=\"".htmlspecialchars(trim($className." ".$this->prop))."\" $extra>";
      
      if ($defaultOption){
        if($value === null) {
          $sHtml    .= "\n<option value=\"\" selected=\"selected\">&mdash; $defaultOption</option>";
        } else {
          $sHtml    .= "\n<option value=\"\">&mdash; $defaultOption</option>";
        }
      }
      
      foreach ($this->_locales as $key => $item){
        if(($value !== null && $value === "$key") || ($value === null && "$key" === "$this->default" && !$defaultOption)){
          $selected = " selected=\"selected\""; 
        }else{
          $selected = "";
        }
        $sHtml    .= "\n<option value=\"$key\"$selected>$item</option>";
      }
      $sHtml      .= "\n</select>";
      return $sHtml;
    }    
  }
  
  function getLabelForElement($object, &$params){
  	$typeEnum  = CMbArray::extract($params, "typeEnum", "radio");
    
  	if($typeEnum === "radio"){
      return $this->fieldName."_1";
  	}
  	if($typeEnum === "checkbox"){
  		return "__".$this->fieldName;
  	}
  }
  
  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $fieldName = $this->fieldName;
    $propValue =& $object->$fieldName;
    
    $propValue = rand(0,1);

  }
  
}

?>