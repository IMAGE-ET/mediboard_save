<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI;

class CDoCopyAddiction extends CDoObjectAddEdit {
  function CDoCopyAddiction() {
    $this->CDoObjectAddEdit("CAddiction", "addiction_id");
    
    $this->createMsg = "Addiction cr��e";
    $this->modifyMsg = "Addiction modifi�e";
    $this->deleteMsg = "Addiction supprim�e";
  }  
  
  function doBind() {
    parent::doBind();
    
    $object_class  = mbGetValueFromPost("object_class"  , null); 
    $object_id     = mbGetValueFromPost("object_id"     , null);
    
    unset($_POST["addiction_id"]);
    $this->_obj = $this->_objBefore;
    $this->_obj->_id = null;
    $this->_obj->antecedent_id = null;
    $this->_obj->object_class  = $object_class;
    $this->_obj->object_id     = $object_id;
  }
}
$do = new CDoCopyAddiction;
$do->doIt();
?>