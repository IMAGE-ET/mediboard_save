<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;
  
if (!$canEdit) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$selConsult  = mbGetValueFromGetOrSession("selConsult", 0);

$consult = new CConsultation();
$consult->load($selConsult);
$consult->loadRefConsultAnesth();
$consult->_ref_consult_anesth->loadRefsBack();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("consult_anesth", $consult->_ref_consult_anesth);

$smarty->display("inc_consult_anesth/techniques_comp.tpl");
?>