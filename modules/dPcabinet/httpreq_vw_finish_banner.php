<?php /* $Id$*/

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;
  
$can->needsEdit();

// Utilisateur s�lectionn� ou utilisateur courant
$prat_id = CValue::getOrSession("chirSel", 0);

$userSel = new CMediusers;
$userSel->load($prat_id ? $prat_id : $AppUI->user_id);
$userSel->loadRefs();
$canUserSel = $userSel->canDo();

// V�rification des droits sur les praticiens
if(CAppUI::pref("pratOnlyForConsult", 1)) {
  $listChir = $userSel->loadPraticiens(PERM_EDIT);
} else {
  $listChir = $userSel->loadProfessionnelDeSante(PERM_EDIT);
}

if (!$userSel->isMedical()) {
  CAppUI::setMsg("Vous devez selectionner un professionnel de sant�", UI_MSG_ALERT);
  CAppUI::redirect("m=dPcabinet&tab=0");
}

$canUserSel->needsEdit();

$selConsult = CValue::getOrSession("selConsult");
$_is_anesth = CValue::get("_is_anesth");

// Consultation courante
$consult = new CConsultation();
$consult->_ref_chir = $userSel;
if ($selConsult) {
  $consult->load($selConsult);
  
  $can->needsObject($consult);
  $canConsult = $consult->canDo();
  $canConsult->needsEdit();
  
  $consult->loadRefsFwd();
  $consult->_ref_patient->loadRefPhotoIdentite();
  $userSel->load($consult->_ref_plageconsult->chir_id);
  $consult->loadAides($userSel->user_id);
  
  $consult->loadRefSejour();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("_is_anesth", $_is_anesth);
$smarty->assign("consult"   , $consult);

$smarty->display("inc_finish_banner.tpl");

?>