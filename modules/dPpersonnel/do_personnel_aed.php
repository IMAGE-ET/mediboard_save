<?php

/**
 *	@package Mediboard
 *	@subpackage dPpersonnel
 *	@version $Revision: 
 *  @author Alexis Granger
 */

global $AppUI;

$do = new CDoObjectAddEdit("CPersonnel", "personnel_id");
$do->createMsg = "Personnel cr��";
$do->modifyMsg = "Personnel modifi�";
$do->deleteMsg = "Personnel supprim�";
$do->doIt();

?>