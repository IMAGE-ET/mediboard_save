<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $m, $exam_audio;

$exam_audio = new CExamAudio;
$exam_audio->load(mbGetValueFromGetOrSession("examaudio_id"));

CAppUI::requireModuleFile($m, "inc_graph_audio_tympan");

$side = dPgetParam($_GET, "side");

AudiogrammeTympano::${$side}->Stroke();
