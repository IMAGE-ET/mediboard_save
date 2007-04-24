<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: $
 *  @author S�bastien Fillonneau
*/

require_once("./classes/mbFieldSpec.class.php");
require_once("./classes/fieldSpecs/numSpec.class.php");

class CNumcharSpec extends CNumSpec {
  
  function getSpecType() {
    return("numchar");
  }

  function getDBSpec(){
    $type_sql = "BUGINT ZEROFILL";
    
    if($this->maxLength || $this->length){
      $length = $this->maxLength ? $this->maxLength : $this->length;
      $valeur_max = pow(10,$length);
      $type_sql = "TINYINT";
      if ($valeur_max > pow(2,8)) {
        $type_sql = "MEDIUMINT";
      }
      if ($valeur_max > pow(2,16)) {
        $type_sql = "INT";
      }
      if ($valeur_max > pow(2,32)) {
        $type_sql = "BIGINT";
      }
      $type_sql .= "($length) UNSIGNED ZEROFILL";
    }
    return $type_sql;
  }
}

?>