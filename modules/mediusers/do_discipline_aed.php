<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CDiscipline", "discipline_id");
$do->createMsg = "Discipline cr��e";
$do->modifyMsg = "Discipline modifi�e";
$do->deleteMsg = "Discipline supprim�e";
$do->doIt();

?>