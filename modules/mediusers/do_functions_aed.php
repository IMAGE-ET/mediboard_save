<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CFunctions", "function_id");
$do->createMsg = "Fonction cr��e";
$do->modifyMsg = "Fonction modifi�e";
$do->deleteMsg = "Fonction supprim�e";
$do->doIt();

?>