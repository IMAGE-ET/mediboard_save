<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI;

class CDoCopyTraitement extends CDoObjectAddEdit {
  function CDoCopyTraitement() {
    $this->CDoObjectAddEdit("CTraitement", "traitement_id");
    
    $this->createMsg = "Traitement cr��";
    $this->modifyMsg = "Traitement modifi�";
    $this->deleteMsg = "Traitement supprim�";
  }  
  
  function doBind() {
    parent::doBind();
    
    $object_class  = mbGetValueFromPost("object_class"  , null); 
    $object_id     = mbGetValueFromPost("object_id"     , null);
    
    unset($_POST["traitement_id"]);
    $this->_obj = $this->_objBefore;
    $this->_obj->_id = null;
    $this->_obj->traitement_id = null;
    $this->_obj->object_class  = $object_class;
    $this->_obj->object_id     = $object_id;
  }
}
$do = new CDoCopyTraitement;
$do->doIt();
?>