<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPPlanningOp
 *	@version $Revision: $
 *  @author S�bastien Fillonneau
 */


/**
 * The CTypeAnesth class
 */
class CTypeAnesth extends CMbObject {
  // DB Table key
  var $type_anesth_id = null;

  // DB Fields
  var $name           = null;

  function CTypeAnesth() {
    $this->CMbObject("type_anesth", "type_anesth_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getBackRefs() {
      $backRefs = parent::getBackRefs();
      $backRefs["operations_anesth"] = "COperation type_anesth";
     return $backRefs;
  }
  
  function getSpecs() {
    return array (
      "name" => "notNull str"
    );
  }
  
}
?>