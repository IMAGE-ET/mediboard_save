<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI;

$do = new CDoObjectAddEdit("CAssociationMoment", "association_moment_id");
$do->createMsg = "Association cr��e";
$do->modifyMsg = "Association modifi�e";
$do->deleteMsg = "Association supprim�e";
$do->doIt();

?>