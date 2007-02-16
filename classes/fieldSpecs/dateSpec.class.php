<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author S�bastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");

class CDateSpec extends CMbFieldSpec {
  
  function checkProperty(&$object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    if (!preg_match ("/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/", $propValue)) {
      return "format de date invalide";
    }
    return null;
  }
  
  function getConfidential(&$object){
    $fieldName = $this->fieldName;
    $propValue =& $object->$fieldName;
    
    $propValue = "19".$this->randomString($this->_nums, 2)."-".$this->randomString($this->_monthes, 1)."-".$this->randomString($this->_days, 1);
  }
  
  function checkFieldType(){
    return "text";
  }
  
  function getDBSpec(){
    return "date";
  }
}

?>