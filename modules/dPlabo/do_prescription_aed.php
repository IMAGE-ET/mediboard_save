<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CPrescriptionLabo", "prescription_labo_id");
$do->createMsg = "Prescription ajout�e";
$do->modifyMsg = "Prescription modifi�e";
$do->deleteMsg = "Prescription supprim�e";
$do->doIt();

?>