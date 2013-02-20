<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Alexis Granger
*/

CCanDo::checkRead();

$consultation_id   = CValue::getOrSession("consultation_id");
$dossier_anesth_id = CValue::getOrSession("dossier_anesth_id");

// Chargement de la consultation
$consult = new CConsultation();
$consult->load($consultation_id);
$consult->loadRefPlageConsult();
$consult->loadRefsFichesExamen();

$consult->_is_anesth = $consult->_ref_chir->isAnesth();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("consult"   , $consult);
$smarty->assign("dossier_anesth_id", $dossier_anesth_id);

$smarty->display("inc_examens_comp.tpl");
