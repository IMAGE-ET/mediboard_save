<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $m;

require_once($AppUI->getModuleClass("dPgestionCab", "fichePaie"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CFichePaie", "fiche_paie_id");
$do->createMsg = "Fiche cr��e";
$do->modifyMsg = "Fiche modifi�e";
$do->deleteMsg = "Fiche supprim�e";
$do->doIt();
?>