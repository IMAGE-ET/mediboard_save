<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Thomas Despoix
*/

require_once($AppUI->getModuleClass("dPsalleOp", "acteccam"));
require_once($AppUI->getSystemClass('doobjectaddedit'));

class CDoActeCCAMAddEdit extends CDoObjectAddEdit {
  function CDoActeCCAMAddEdit() {
    $this->CDoObjectAddEdit("CActeCCAM", "acte_id");
    
    $this->createMsg = "Acte CCAM cr��";
    $this->modifyMsg = "Acte CCAM modifi�";
    $this->deleteMsg = "Acte supprim�";
	  
  }
  
  function doBind() {
    parent::doBind();
    
    $this->_obj->modificateurs = "";
    foreach ($_POST as $propName => $propValue) {
      if (preg_match("/modificateur_(.)/", $propName, $matches)) {
        $modificateur = $matches[1];
        $this->_obj->modificateurs .= $modificateur;
      }
    }
  }
}

$do = new CDoActeCCAMAddEdit();
$do->doIt();
?>