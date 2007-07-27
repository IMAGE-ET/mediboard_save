<?php /* $Id: view_planning.php 1738 2007-03-19 16:33:47Z maskas $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Yohann Poiron
*/

global $AppUI, $can, $m;

$can->needsRead();

$consultation_id = mbGetValueFromGet("consultation_id");

$consultation = new CConsultation;
$consultation->load($consultation_id);
$consultation->loadRefsFwd();

$prat_id = $consultation->_ref_plageconsult->_ref_chir;
$prat_id->loadRefs();

$patient = $consultation->_ref_patient;
$patient->loadRefs();

$today = mbDate();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("consultation", $consultation	);
$smarty->assign("patient"			, $patient			);
$smarty->assign("prat_id"			, $prat_id			);
$smarty->assign("today"    		, $today    		);

$smarty->display("view_consultation.tpl");

?>