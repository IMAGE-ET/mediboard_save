<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision: $
* @author Romain Ollivier
*/

$do = new CDoObjectAddEdit("CNote", "note_id");
$do->createMsg = "Note cr��e";
$do->modifyMsg = "Note modifi�e";
$do->deleteMsg = "Note supprim�e";
$do->doIt();
?>