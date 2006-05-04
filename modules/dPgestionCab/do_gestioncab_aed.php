<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $m;

require_once($AppUI->getModuleClass("dPgestionCab", "gestionCab"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CGestionCab", "gestioncab_id");
$do->createMsg = "Fiche cr��e";
$do->modifyMsg = "Fiche modifi�e";
$do->deleteMsg = "Fiche supprim�e";
$do->doIt();
?>