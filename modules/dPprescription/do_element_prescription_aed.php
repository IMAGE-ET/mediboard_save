<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI;

$do = new CDoObjectAddEdit("CElementPrescription", "element_prescription_id");
$do->createMsg = "El�ment cr��";
$do->modifyMsg = "El�ment modifi�";
$do->deleteMsg = "El�ment supprim�";
$do->doIt();

?>