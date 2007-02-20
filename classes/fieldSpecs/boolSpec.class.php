<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author S�bastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");

class CBoolSpec extends CMbFieldSpec {
  
  function getValue($object, $smarty, $params = null) {
    global $AppUI;
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    return $AppUI->_("bool.".$propValue);
  }
  
  function getSpecType() {
    return("bool");
  }
  
  function checkProperty(&$object){
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
  
  function checkFieldType(){
    return "radio";
  }
  
  function getDBSpec(){
    return "enum('0','1')";
  }
}

?>