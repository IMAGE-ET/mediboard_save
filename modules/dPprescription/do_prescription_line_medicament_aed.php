<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CPrescriptionLineMedicament", "prescription_line_id");
$do->createMsg = "Traitement ajout�";
$do->modifyMsg = "Traitement modifi�";
$do->deleteMsg = "Traitement supprim�";
$do->doIt();

?>