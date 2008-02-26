<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $can, $m;

//$can->needsEdit();

$consultation_id = mbGetValueFromGetOrSession("consultation_id");

$where = array("consultation_id" => "= '$consultation_id'");
$exam_nyha = new CExamNyha;
$exam_nyha->loadObject($where);

if (!$exam_nyha->_id) {
  $exam_nyha->consultation_id = $consultation_id;
}
$exam_nyha->loadRefsFwd();

$consultation =& $exam_nyha->_ref_consult;
$consultation->loadRefsFwd();
$consultation->loadRefConsultAnesth();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("exam_nyha" , $exam_nyha);

$smarty->display("exam_nyha.tpl");
?>