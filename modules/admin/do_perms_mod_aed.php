<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage admin
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CPermModule", "perm_module_id");
$do->createMsg = "Permission cr��e";
$do->modifyMsg = "Permission modifi�e";
$do->deleteMsg = "Permission supprim�e";
$do->doIt();

?>