<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI;

$do = new CDoObjectAddEdit("CPrisePosologie", "prise_posologie_id");
$do->createMsg = "Posologie ajout�";
$do->modifyMsg = "Posologie modifi�";
$do->deleteMsg = "Posologie supprim�";
$do->doIt();

?>