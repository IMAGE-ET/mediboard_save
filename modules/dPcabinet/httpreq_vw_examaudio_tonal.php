<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $m;
global $frequences, $pressions, $exam_audio, $reloadGraph;

$examaudio_id = mbGetValueFromGetOrSession("examaudio_id");
$side         = mbGetValueFromGetOrSession("side");
$reloadGraph  = $side;

$exam_audio = new CExamAudio;
$exam_audio->load($examaudio_id);

CAppUI::requireModuleFile($m, "inc_graph_audio_tonal");

${"graph_tonal_".$side}->Stroke("tmp/graphtmp.png");
$map_tonal = ${"graph_tonal_".$side}->GetHTMLImageMap("graph_tonal_".$side);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("map_tonal" , $map_tonal);
$smarty->assign("side"      , $side);
$smarty->assign("fctOnClick", ucwords($side));
$smarty->assign("exam_audio", $exam_audio);
$smarty->assign("time"    , time());

$smarty->display("inc_exam_audio/inc_examaudio_graph_tonal.tpl");
?>