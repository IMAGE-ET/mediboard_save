<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPetablissement
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CGroups", "group_id");
$do->createMsg = "Etablissement cr��";
$do->modifyMsg = "Etablissement modifi�";
$do->deleteMsg = "Etablissement supprim�";
$do->doIt();

?>