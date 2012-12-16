<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkRead();
$user = CMediusers::get();

$patient_id = CValue::getOrSession("patient_id", 0);

// recuperation des id dans le cas d'une recherche de dossiers cliniques 
$consultation_id = CValue::get("consultation_id", 0);
$sejour_id       = CValue::get("sejour_id", 0);
$operation_id    = CValue::get("operation_id", 0);

// Liste des Praticiens
$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_EDIT);

// R�cuperation du patient s�lectionn�
$patient = new CPatient;
$patient->load($patient_id);
if (!$patient->_id || $patient->_vip) {
  CAppUI::setMsg("Vous devez selectionner un patient", UI_MSG_ALERT);
  CAppUI::redirect("m=patients&tab=vw_idx_patients");
}
$patient->loadDossierComplet(PERM_READ);

// Chargement de l'IPP
$patient->loadIPP();

// Chargement du dossier medical du patient
$dossier_medical = $patient->loadRefDossierMedical();
$dossier_medical->loadRefsTraitements();
$dossier_medical->loadRefsAntecedents();
$prescription = $dossier_medical->loadRefPrescription();

if ($prescription && is_array($prescription->_ref_prescription_lines)) {
  foreach ($prescription->_ref_prescription_lines as $_line) {
    $_line->loadRefsPrises();
  }
}

// Suppression des consultations d'urgences
foreach ($patient->_ref_consultations as $consult) {
  if ($consult->motif == "Passage aux urgences") {
    unset($patient->_ref_consultations[$consult->_id]);
  }
}

$patient->_ref_dossier_medical->canRead();

$can_view_dossier_medical = 
  CModule::getCanDo('dPcabinet')->edit ||
  CModule::getCanDo('dPbloc')->edit ||
  CModule::getCanDo('dPplanningOp')->edit || 
  $user->isFromType(array("Infirmi�re"));
  
// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("canCabinet", CModule::getCanDo("dPcabinet"));

$smarty->assign("consultation_id"         , $consultation_id);
$smarty->assign("sejour_id"               , $sejour_id);
$smarty->assign("can_view_dossier_medical", $can_view_dossier_medical);
$smarty->assign("operation_id"            , $operation_id);
$smarty->assign("patient"                 , $patient);
$smarty->assign("listPrat"                , $listPrat);
$smarty->assign("object"                  , $patient);
$smarty->assign("isImedsInstalled"        , (CModule::getActive("dPImeds") && CImeds::getTagCIDC(CGroups::loadCurrent())));

$smarty->display("vw_full_patients.tpl");

?>