<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $m;

$do = new CDoObjectAddEdit("CEmployeCab", "employecab_id");
$do->createMsg = "Employ� cr��";
$do->modifyMsg = "Employ� modifi�";
$do->deleteMsg = "Employ� supprim�";
$do->doIt();
?>