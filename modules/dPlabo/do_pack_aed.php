<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPlabo
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CPackExamensLabo", "pack_examens_labo_id");
$do->createMsg = "Pack cr��";
$do->modifyMsg = "Pack modifi�";
$do->deleteMsg = "Pack supprim�";
$do->doIt();

?>