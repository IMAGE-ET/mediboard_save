<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/


global $AppUI;

$do = new CDoObjectAddEdit("CPrescriptionLineElement", "prescription_line_element_id");
$do->createMsg = "El�ment ajout�";
$do->modifyMsg = "El�ment modifi�";
$do->deleteMsg = "El�ment supprim�";
$do->doIt();

?>