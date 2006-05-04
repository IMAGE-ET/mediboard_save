<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getSystemClass("doobjectaddedit"));
require_once($AppUI->getModuleClass("dPcabinet", "examaudio"));

mbSetValueToSession("_conduction", $_POST["_conduction"]);
mbSetValueToSession("_oreille", $_POST["_oreille"]);

$do = new CDoObjectAddEdit("CExamAudio", "examaudio_id");
$do->createMsg = "Examen audio cr��";
$do->modifyMsg = "Examen audio modifi�";
$do->deleteMsg = "Examen audio supprim�";
$do->redirect = null;
$do->doIt();
?>
