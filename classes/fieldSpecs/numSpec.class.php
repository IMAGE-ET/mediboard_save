<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author S�bastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");

class CNumSpec extends CMbFieldSpec {
  
  function getSpecType() {
    return("num");
  }
  
  var $min       = null;
  var $max       = null;
  var $pos       = null;
  var $length    = null;
  var $minLength = null;
  var $maxLength = null;
  var $minMax    = null;

  function checkProperty($object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    $propValue = $this->checkNumeric($propValue, false);
    
    if($propValue === null){
      return "N'est pas une cha�ne num�rique";
    }

    // pos
    if($this->pos){
      if ($propValue <= 0) {
        return "Doit avoir une valeur positive";
      }
    }  

    // min
    if($this->min){
      if(!$min = $this->checkNumeric($this->min)){
        trigger_error("Sp�cification de minimum num�rique invalide (min = $this->min)", E_USER_WARNING);
        return "Erreur syst�me";
      }
      if ($propValue < $min) {
        return "Soit avoir une valeur minimale de $min";
      }
    }
    
    // max  
    if($this->max){
      $max = $this->checkNumeric($this->max);
      if($max === null){
        trigger_error("Sp�cification de maximum num�rique invalide (max = $this->max)", E_USER_WARNING);
        return "Erreur syst�me";
      }      
      if ($propValue > $max) {
        return "Soit avoir une valeur maximale de $max";
      }
    }
    
    // length  
    if($this->length){
      if(!$length = $this->checkLengthValue($this->length)){
        trigger_error("Sp�cification de longueur invalide (longueur = $this->length)", E_USER_WARNING);
        return "Erreur syst�me";
      }
      if (strlen($propValue) != $length) {
        return "N'a pas la bonne longueur (longueur souhait� : $length)'";
      }
    }
    
    // minLength
    if($this->minLength){
      if(!$length = $this->checkLengthValue($this->minLength)){
        trigger_error("Sp�cification de longueur minimale invalide (longueur = $this->minLength)", E_USER_WARNING);
        return "Erreur syst�me";
      }
      if (strlen($propValue) < $length) {
        return "N'a pas la bonne longueur (longueur minimale souhait�e : $length)'";
      }
    }
    
    // maxLength
    if($this->maxLength){
      if(!$length = $this->checkLengthValue($this->maxLength)){
        trigger_error("Sp�cification de longueur maximale invalide (longueur = $this->maxLength)", E_USER_WARNING);
        return "Erreur syst�me";
      }
      if (strlen($propValue) > $length) {
        return "N'a pas la bonne longueur (longueur maximale souhait�e : $length)'";
      }
    }
    
    // minMax
    if($this->minMax){
      $specFragments = explode("|", $this->minMax);
      $min= $this->checkNumeric(@$specFragments[0]);
      $max= $this->checkNumeric(@$specFragments[1]);
      if(count($specFragments) != 2 || $min === null || $max === null){
        trigger_error("Sp�cification de minimum maximum num�rique invalide (minMax = $this->minMax)", E_USER_WARNING);
        return "Erreur syst�me";
      }
      if($propValue>$max || $propValue<$min){
        return "N'est pas compris entre $min et $max";
      }
    }
    return null;
  }

  function sample(&$object, $consistent = true){
    parent::sample($object, $consistent);
    $fieldName = $this->fieldName;
    $propValue =& $object->$fieldName;
    
    if($this->length){
      $propValue = $this->randomString(CMbFieldSpec::$nums, $this->length);
      
    }elseif($this->minLength){
      if($this->_defaultLength < $this->minLength){
        $propValue = $this->randomString(CMbFieldSpec::$nums, $this->minLength);
      }else{
        $propValue = $this->randomString(CMbFieldSpec::$nums, $this->_defaultLength);
      }
      
    }elseif($this->maxLength){
      if($this->_defaultLength > $this->maxLength){
        $propValue = $this->randomString(CMbFieldSpec::$nums, $this->maxLength);
      }else{
        $propValue = $this->randomString(CMbFieldSpec::$nums, $this->_defaultLength);
      }
    }elseif($this->minMax || $this->max || $this->min){
      if($this->minMax){
        $specFragments = explode("|", $this->minMax);
        $min= $this->checkNumeric($specFragments[0]);
        $max= $this->checkNumeric($specFragments[1]);
      }else{
        $min = $this->min ? $this->min : 0;
        $max = $this->max ? $this->max : 999999;
      }
      $propValue = rand($min, $max);
    }else{
      $propValue = $this->randomString(CMbFieldSpec::$nums, $this->_defaultLength);
    }

  }
  
  function getDBSpec(){
    $type_sql   = "INT(11)";
    $valeur_max = null;
    
    if($this->minMax || $this->max){
      if($this->minMax){
        $specFragments = explode("|", $this->minMax);
        $valeur_max = $specFragments[1];
      }else{
        $valeur_max = $this->max;
      }
      $type_sql = "TINYINT(4)";
      if ($valeur_max > pow(2,8)) {
        $type_sql = "MEDIUMINT(9)";
      }
      if ($valeur_max > pow(2,16)) {
        $type_sql = "INT(11)";
      }
      if ($valeur_max > pow(2,32)) {
        $type_sql = "BIGINT(20)";
      }
    }elseif($this->pos){
      $type_sql = "INT(10) UNSIGNED";
    }
    
    return $type_sql;
  }

  function getFormHtmlElement($object, $params, $value, $className) {
  	$form      = CMbArray::extract($params, "form");
  	$increment = CMbArray::extract($params, "increment");
  	$field     = htmlspecialchars($this->fieldName);
    $maxLength = mbGetValue($this->length, $this->maxLength, 11);
    $fieldId = str_replace('-', '_', $form.'_'.$field);
    
    if (!$min = CMbArray::extract($params, "min")) {
      $min = $this->checkNumeric($this->min);
    }
    if (!$max = CMbArray::extract($params, "max")) {
      $max = $this->checkNumeric($this->max);
    }
    $step = $this->checkNumeric(CMbArray::extract($params, "step"));
    
    CMbArray::defaultValue($params, "size", min($maxLength, 20));
    CMbArray::defaultValue($params, "maxlength", $maxLength);
    if ($form && $increment) {
	    $sHtml  = '<div class="numericField">';
	    $sHtml .= $this->getFormElementText($object, $params, $value, $className);
	    $sHtml .= '
	  <script type="text/javascript">
			'.$fieldId.'_object = new NumericField("'.$form.'", "'.$field.'", '.($step?$step:'null').', '.($this->pos?'0':($min?$min:'null')).', '.($max?$max:'null').');
		</script>
    <img alt="updown" src="./images/icons/numeric_updown.gif" usemap="#arrow_'.$fieldId.'" />
	  <map name="arrow_'.$fieldId.'" >
	    <area coords="0,0,10,8"   href="#1" tabIndex="10000" onclick="'.$fieldId.'_object.inc()" title="+" />
	    <area coords="0,10,10,18" href="#1" tabIndex="10000" onclick="'.$fieldId.'_object.dec()" title="-" />
	  </map>
	  </div>';
    } else {
    	$sHtml = $this->getFormElementText($object, $params, $value, $className);
    }
    return $sHtml;
  }
}

?>