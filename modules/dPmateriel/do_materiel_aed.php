<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPmateriel
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CMateriel", "materiel_id");
$do->createMsg = "Mat�riel cr��";
$do->modifyMsg = "Mat�riel modifi�";
$do->deleteMsg = "Mat�riel supprim�";
$do->doIt();

?>