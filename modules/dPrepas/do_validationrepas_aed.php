<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m, $g;

class CDoValidationRepasAddEdit extends CDoObjectAddEdit {
  function CDoValidationRepasAddEdit() {
    $this->CDoObjectAddEdit("CValidationRepas", "validationrepas_id");
    $this->createMsg = "Validation des repas cr��e";
    $this->modifyMsg = "Validation des repas modifi�e";
    $this->deleteMsg = "Validation des repas supprim�e";
  }
  
  function doStore() {
    
    $this->_obj->resetModifications();
    parent::doStore();
  }
  
}
$do = new CDoValidationRepasAddEdit;
$do->doIt();
?>