<?php

/**
* @package Mediboard
* @subpackage dPetablissement
* @version $Revision:  $
* @author Alexis Granger
*/

global $AppUI;

$do = new CDoObjectAddEdit("CEtabExterne", "etab_id");
$do->createMsg = "Etablissement externe cr��";
$do->modifyMsg = "Etablissement externe modifi�";
$do->deleteMsg = "Etablissement externe supprim�";
$do->doIt();

?>