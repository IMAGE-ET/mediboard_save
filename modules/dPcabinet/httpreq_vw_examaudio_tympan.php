<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $m;
global $frequences, $pressions, $exam_audio, $reloadGraph;

$examaudio_id = mbGetValueFromGetOrSession("examaudio_id");
$side         = mbGetValueFromGetOrSession("side");
$reloadGraph  = $side;

$exam_audio = new CExamAudio;
$exam_audio->load($examaudio_id);

require_once($AppUI->getModuleFile("$m", "inc_graph_audio_tympan"));

${"graph_tympan_".$side}->Stroke("tmp/graphtmp.png");
$map_tympan = ${"graph_tympan_".$side}->GetHTMLImageMap("graph_tympan_".$side);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("map_tympan", $map_tympan);
$smarty->assign("side"      , $side);
$smarty->assign("fctOnClick", ucwords($side));
$smarty->assign("exam_audio", $exam_audio);
$smarty->assign("time"      , time());

$smarty->display("inc_exam_audio/inc_examaudio_graph_tympan.tpl");
?>