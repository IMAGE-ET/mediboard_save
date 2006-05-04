<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

require_once( $AppUI->getModuleClass('mediusers', 'functions') );
require_once($AppUI->getSystemClass('doobjectaddedit'));

$do = new CDoObjectAddEdit("CFunctions", "function_id");
$do->createMsg = "Fonction cr��e";
$do->modifyMsg = "Fonction modifi�e";
$do->deleteMsg = "Fonction supprim�e";
$do->doIt();

?>