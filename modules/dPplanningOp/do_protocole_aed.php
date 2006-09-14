<?php /* $Id: do_planning_aed.php 110 2006-06-11 20:19:38Z Rhum1 $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: 110 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if ($chir_id = mbGetValueFromPost("chir_id")) {
  mbSetValueToSession("chir_id", $chir_id);
}

$do = new CDoObjectAddEdit("CProtocole", "protocole_id");
$do->createMsg = "Protocole cr��";
$do->modifyMsg = "Protocole modifi�";
$do->deleteMsg = "Protocole supprim�";
$do->redirectDelete = "m=$m&tab=vw_edit_protocole&protocole_id=0";
$do->doIt();

?>