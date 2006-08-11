<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPetablissement
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

require_once($AppUI->getModuleClass("dPetablissement", "groups"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CGroups", "group_id");
$do->createMsg = "Groupe cr��";
$do->modifyMsg = "Groupe modifi�";
$do->deleteMsg = "Groupe supprim�";
$do->doIt();

?>