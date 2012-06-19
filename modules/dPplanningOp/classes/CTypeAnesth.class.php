<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPPlanningOp
 *	@version $Revision$
 *  @author S�bastien Fillonneau
 */


/**
 * The CTypeAnesth class
 */
class CTypeAnesth extends CMbObject {
  // DB Table key
  var $type_anesth_id = null;

  // DB Fields
  var $name = null;
  var $ext_doc = null;
  var $actif = null;
  
  // References
  var $_count_operations = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'type_anesth';
    $spec->key   = 'type_anesth_id';
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["operations"] = "COperation type_anesth";
    return $backProps;
  }
	
	function updateFormFields() {
		parent::updateFormFields();
		$this->_view = $this->name;
	}
  
  function countOperations() {
    $this->_count_operations = $this->countBackRefs("operations");
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["name"]    = "str notNull";
    $specs["ext_doc"] = "enum list|1|2|3|4|5|6"; 
    $specs["actif"]   = "bool notNull default|1" ;
    return $specs;
  }
}
?>