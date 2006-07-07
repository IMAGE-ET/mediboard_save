<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

require_once($AppUI->getModuleClass("dPmateriel", "materiel"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CMateriel", "materiel_id");
$do->createMsg = "Mat�riel cr��";
$do->modifyMsg = "Mat�riel modifi�";
$do->deleteMsg = "Mat�riel supprim�";
$do->doIt();

?>