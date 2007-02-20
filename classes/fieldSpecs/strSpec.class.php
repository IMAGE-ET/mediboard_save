<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author S�bastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");

class CStrSpec extends CMbFieldSpec {
  
  var $length    = null;
  var $minLength = null;
  var $maxLength = null;
  
  function getSpecType() {
    return("str");
  }
  
  function checkProperty(&$object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    // length
    if($this->length){
      if(!$length = $this->checkLengthValue($this->length)){
        trigger_error("Sp�cification de longueur invalide (longueur = $this->length)", E_USER_WARNING);
        return "Erreur syst�me";
      } 
      if (strlen($propValue) != $length) {
        return "N'a pas la bonne longueur (longueur souhait�e : $length)'";
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
    
    return null;
  }
  
  function getConfidential(&$object){
    $fieldName = $this->fieldName;
    $propValue =& $object->$fieldName;
    
    if($this->length){
      $propValue = $this->randomString($this->_chars, $this->length);
    
    }elseif($this->minLength){
      if($this->_defaultLength < $this->minLength){
        $propValue = $this->randomString($this->_chars, $this->minLength);
      }else{
        $propValue = $this->randomString($this->_chars, $this->_defaultLength);
      }
    
    }elseif($this->maxLength){
      if($this->_defaultLength > $this->maxLength){
        $propValue = $this->randomString($this->_chars, $this->maxLength);
      }else{
        $propValue = $this->randomString($this->_chars, $this->_defaultLength);
      }

    }else{
      $propValue = $this->randomString($this->_chars, $this->_defaultLength);
    }
  }
  
  function checkFieldType(){
    return "text";
  }

  function getDBSpec(){
    $type_sql = "varchar(255)";
    
    if($this->maxLength || $this->length){
      $length = $this->maxLength ? $this->maxLength : $this->length;
      $type_sql = "varchar($length)";
    }
    return $type_sql;
  }
}

?>