<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPurgences
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CRPU", "rpu_id");
$do->createMsg = "Urgence cr��e";
$do->modifyMsg = "Urgence modifi�e";
$do->deleteMsg = "Urgence supprim�e";
$do->doIt();

?>