<?php

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision:
* @author Alexis Granger
*/

global $AppUI, $m;

$do = new CDoObjectAddEdit("CPrestation", "prestation_id");
$do->createMsg = "Prestation cr��e";
$do->modifyMsg = "Prestation modifi�e";
$do->deleteMsg = "Prestation supprim�e";
$do->doIt();
?>