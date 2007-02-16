<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author S�bastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");
require_once("./classes/fieldSpecs/refSpec.class.php");

class CRefMandatorySpec extends CRefSpec {
  
  function checkProperty(&$object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    $propValue = $this->checkNumeric($propValue, false);
    if($propValue === null || $object->$fieldName == ""){
      return "N'est pas une r�f�rence (format non num�rique)";
    }
    
    if ($propValue === 0) {
      return "ne peut pas �tre une r�f�rence nulle";
    }
    return parent::checkProperty(&$object);
  }
}

?>