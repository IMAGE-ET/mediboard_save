<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author S�bastien Fillonneau
*/

global $AppUI, $can, $m;


$can->needsRead();

// D�finition des variables
$consultation_id = mbGetValueFromGet("consultation_id", 0);

$consult = new CConsultation;
$consult->load($consultation_id);
$consult->loadRefConsultAnesth();

$consult->loadLogs();

if($consult->_ref_consult_anesth->consultation_anesth_id){
  $consult->_ref_consult_anesth->loadLogs();
}


// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("consult" , $consult );

$smarty->display("vw_history.tpl");
?>