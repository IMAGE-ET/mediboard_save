<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if ($chir_id = dPgetParam($_POST, "chir_id")) {
  mbSetValueToSession("chir_id", $chir_id);
}

$do = new CDoObjectAddEdit("CConsultAnesth", "consultation_anesth_id");
$do->createMsg = "Consultation cr��e";
$do->modifyMsg = "Consultation modifi�e";
$do->deleteMsg = "Consultation supprim�e";
$do->doIt();

?>
