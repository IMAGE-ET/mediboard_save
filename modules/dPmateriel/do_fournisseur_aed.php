<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI;

require_once($AppUI->getModuleClass("dPmateriel", "fournisseur"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CFournisseur", "fournisseur_id");
$do->createMsg = "Fournisseur cr��";
$do->modifyMsg = "Fournisseur modifi�";
$do->deleteMsg = "Fournisseur supprim�";
$do->doIt();

?>