<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

global $can;
$can->needsRead();

$sejour = new CSejour();
$sejour->load(CValue::get("sejour_id"));
$sejour->loadRefsOperations();
$consult_anesth = $sejour->loadRefsConsultAnesth();
$consult_anesth->loadRefsFwd();

foreach ($sejour->_ref_operations as $_operation) {
	$_operation->loadRefPlageOp();
	$consult_anesth = $_operation->loadRefsConsultAnesth();
	$consult_anesth->loadRefsFwd();
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("sejour", $sejour);

$smarty->display("inc_documents_sejour.tpl");
?>