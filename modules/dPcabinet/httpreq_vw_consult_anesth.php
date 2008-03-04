<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;
  
$can->needsEdit();

// Utilisateur s�lectionn� ou utilisateur courant
$prat_id = mbGetValueFromGetOrSession("chirSel", 0);

$userSel = new CMediusers;
$userSel->load($prat_id ? $prat_id : $AppUI->user_id);
$userSel->loadRefs();
$canUserSel = $userSel->canDo();

// V�rification des droits sur les praticiens
$listChir = $userSel->loadPraticiens(PERM_EDIT);

if (!$userSel->isPraticien()) {
  $AppUI->setMsg("Vous devez selectionner un praticien", UI_MSG_ALERT);
  $AppUI->redirect("m=dPcabinet&tab=0");
}

$canUserSel->needsEdit();

$selConsult = mbGetValueFromGetOrSession("selConsult", 0);

// Consultation courante
$consult = new CConsultation();
$consult->_ref_chir = $userSel;

if ($selConsult) {
  $consult->load($selConsult);
  
  $can->needsObject($consult);
  $canConsult = $consult->canDo();
  $canConsult->needsEdit();
  
  $consult->loadRefsFwd();
  $consult->loadRefConsultAnesth();

  if($consult->_ref_consult_anesth->_id) {
    $consult->_ref_consult_anesth->loadRefs();
    $consult->_ref_consult_anesth->_ref_operation->loadRefSejour();
    $consult->_ref_consult_anesth->_ref_operation->_ref_sejour->loadRefDossierMedical();
  }

 
  $patient =& $consult->_ref_patient;
  $patient->loadRefsSejours();
  foreach ($patient->_ref_sejours as $key => $sejour) {
    $patient->_ref_sejours[$key]->loadRefsOperations();
    foreach($patient->_ref_sejours[$key]->_ref_operations as $keyOp => $op) {
      $patient->_ref_sejours[$key]->_ref_operations[$keyOp]->loadRefsFwd();
    }
  }
} else {
  $consult->_ref_consult_anesth = new CConsultAnesth();
}

$consult_anesth =& $consult->_ref_consult_anesth;

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("consult"       , $consult);
$smarty->assign("consult_anesth", $consult_anesth);
$smarty->assign("patient"       , $patient);

$smarty->display("inc_consult_anesth/interventions.tpl");

?>