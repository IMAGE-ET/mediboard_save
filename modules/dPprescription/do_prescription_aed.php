<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CPrescription", "prescription_id");
$do->createMsg = "Prescription cr��e";
$do->modifyMsg = "Prescription modifi�e";
$do->deleteMsg = "Prescription supprim�e";
$do->doIt();

?>