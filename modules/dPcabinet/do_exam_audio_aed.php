<?php /* $Id: do_exam_audio_aed.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 23 $
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

mbSetValueToSession("_conduction", $_POST["_conduction"]);
mbSetValueToSession("_oreille", $_POST["_oreille"]);

$do = new CDoObjectAddEdit("CExamAudio", "examaudio_id");
$do->createMsg = "Examen audio cr��";
$do->modifyMsg = "Examen audio modifi�";
$do->deleteMsg = "Examen audio supprim�";
$do->redirect = null;
$do->doIt();
?>
