<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI;

class CDoCopyAntecedent extends CDoObjectAddEdit {
  function CDoCopyAntecedent() {
    $this->CDoObjectAddEdit("CAntecedent", "antecedent_id");
    
    $this->createMsg = "Antecedent cr��";
    $this->modifyMsg = "Antecedent modifi�";
    $this->deleteMsg = "Antecedent supprim�";
  }  
  
  function doBind() {
    parent::doBind();
    
    $object_class  = mbGetValueFromPost("object_class"  , null); 
    $object_id     = mbGetValueFromPost("object_id"     , null);
    
    unset($_POST["antecedent_id"]);
    $this->_obj = $this->_objBefore;
    $this->_obj->_id = null;
    $this->_obj->antecedent_id = null;
    $this->_obj->object_class  = $object_class;
    $this->_obj->object_id     = $object_id;
  }
}
$do = new CDoCopyAntecedent;
$do->doIt();
?>