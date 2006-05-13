<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $m;

require_once($AppUI->getModuleClass("dPgestionCab", "paramsPaie"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CParamsPaie", "params_paie_id");
$do->createMsg = "Param�tres cr��s";
$do->modifyMsg = "Param�tres modifi�s";
$do->deleteMsg = "Param�tres supprim�s";
$do->doIt();
?>