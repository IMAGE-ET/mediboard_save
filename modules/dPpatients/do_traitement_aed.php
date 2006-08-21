<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

require_once($AppUI->getModuleClass("dPpatients", "traitement"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CTraitement", "traitement_id");
$do->createMsg = "Traitement cr��";
$do->modifyMsg = "Traitement modifi�";
$do->deleteMsg = "Traitement supprim�";
$do->doIt();

?>