<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI;

$do = new CDoObjectAddEdit("CExecutantPrescriptionLine", "executant_prescription_line_id");
$do->createMsg = "Ex�cutant cr��";
$do->modifyMsg = "Ex�cutant modifi�";
$do->deleteMsg = "Ex�cutant supprim�";
$do->doIt();

?>