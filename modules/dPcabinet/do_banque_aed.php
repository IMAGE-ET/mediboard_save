<?php

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI, $can, $m;

$do = new CDoObjectAddEdit("CBanque", "banque_id");
$do->createMsg = "Banque cr��e";
$do->modifyMsg = "Banque modifi�e";
$do->deleteMsg = "Banque supprim�e";
$do->doIt();

?>