<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CPrescriptionLaboExamen", "prescription_labo_examen_id");
$do->createMsg = "Examen ajout�";
$do->modifyMsg = "Examen modifi�";
$do->deleteMsg = "Examen supprim�";
$do->doIt();

?>