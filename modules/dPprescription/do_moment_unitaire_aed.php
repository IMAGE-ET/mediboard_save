<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI;

$do = new CDoObjectAddEdit("CMomentUnitaire", "moment_unitaire_id");
$do->createMsg = "Moment unitaire cr��";
$do->modifyMsg = "Moment unitaire modifi�";
$do->deleteMsg = "Moment unitaire supprim�";
$do->doIt();

?>