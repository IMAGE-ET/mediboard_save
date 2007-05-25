<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author S�bastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");

class CRefSpec extends CMbFieldSpec {
  
  var $class   = null;
  var $cascade = null;
  var $unlink  = null;
  var $meta    = null;
  
  function getSpecType() {
    return("ref");
  }

  function checkProperty($object){
    $fieldName = $this->fieldName;
    $propValue = $object->$fieldName;
    
    $propValue = $this->checkNumeric($propValue, false);
      
    if ($propValue === null || $object->$fieldName === ""){
      return "N'est pas une r�f�rence (format non num�rique)";
    }
    
    if ($propValue == 0) {
      return "ne peut pas �tre une r�f�rence nulle";
    }
    
    if ($propValue < 0) {
      return "N'est pas une r�f�rence (entier n�gatif)";
    }
    
    if (!$this->class and !$this->meta) {
      return "Type d'objet cible on d�fini";
    }
    
    $class = $this->class;
    if ($meta = $this->meta) {
      $class = $object->$meta;
    }
    
    if (!class_inherits_from($class, "CMbObject")) {
      return "La type '$class' n'est pas un type d'objet m�tier";
    }
    
    $ref = new $class;
    if (!$ref->load($propValue)) {
      return "Objet r�f�renc� de type '$class' introuvable";      
    }

    return null;
  }
  
  function getDBSpec(){
    return "INT(11) UNSIGNED";
  }
}

?>