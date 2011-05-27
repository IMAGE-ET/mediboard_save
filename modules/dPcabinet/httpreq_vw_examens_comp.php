<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Alexis Granger
*/

CCanDo::checkRead();

$consultation_id = CValue::getOrSession("consultation_id");

// Chargement de la consultation
$consult = new CConsultation();
$consult->load($consultation_id);
$consult->loadRefPlageConsult();
$consult->loadRefsFichesExamen();

$consult->_is_anesth = $consult->_ref_chir->isFromType(array("Anesth�siste"));

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("consult"   , $consult);
$smarty->assign("_is_anesth", $consult->_is_anesth);

$smarty->display("inc_examens_comp.tpl");

?>