<?php

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI, $can, $m;

$do = new CDoObjectAddEdit("CConsultationCategorie", "categorie_id");
$do->createMsg = "Cat�gorie cr��e";
$do->modifyMsg = "Cat�gorie modifi�e";
$do->deleteMsg = "Cat�gorie supprim�e";
$do->doIt();

?>