<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$group = CGroups::loadCurrent();
$user = CAppUI::$user;
$listResponsables = CAppUI::conf("dPurgences only_prat_responsable") ?
  $user->loadPraticiens(PERM_READ, $group->service_urgences_id) :
  $user->loadUsers(PERM_READ, $group->service_urgences_id);

$listPrats = $user->loadPraticiens(PERM_READ, $group->service_urgences_id);

$rpu    = new CRPU;
$rpu_id = CValue::getOrSession("rpu_id");
if ($rpu_id && !$rpu->load($rpu_id)) {
  global $m, $tab;
  CAppUI::setMsg("Ce RPU n'est pas ou plus disponible", UI_MSG_WARNING);
  CAppUI::redirect("m=$m&tab=$tab&rpu_id=0");
}

// Cr�ation d'un RPU pour un s�jour existant
if ($sejour_id = CValue::get("sejour_id")) {
  $rpu = new CRPU;
  $rpu->sejour_id = $sejour_id;
  $rpu->loadMatchingObject();
  $rpu->updateFormFields();
}

if ($rpu->_id || $rpu->sejour_id) {
  // Mise en session de l'id de la consultation, si elle existe.
  $rpu->loadRefConsult();
  if ($rpu->_ref_consult->_id) {
    CValue::setSession("selConsult", $rpu->_ref_consult->_id);
  }
  
  $sejour  = $rpu->_ref_sejour;
  $patient = $sejour->_ref_patient;
  $patient->loadStaticCIM10($user->_id);
  
  // Chargement de l'IPP ($_IPP)
  $patient->loadIPP();
	
  // Chargement du numero de dossier ($_NDA)
  $sejour->loadNDA();
  $sejour->loadRefPraticien(1);
  $sejour->loadRefsNotes();
	$praticien = $sejour->_ref_praticien;
  $listResponsables[$praticien->_id] = $praticien;
} 
else {
  $rpu->_responsable_id = $user->_id;
  $rpu->_entree         = mbDateTime();
  $sejour               = new CSejour;
  $patient              = new CPatient;
	$praticien            = new CMediusers;
}

// Gestion des traitements, antecedents, diagnostics
$traitement = new CTraitement();
$traitement->loadAides($user->_id);

$antecedent = new CAntecedent();
$antecedent->loadAides($user->_id);

// Contraintes sur le mode d'entree / provenance
$contrainteProvenance[6] = array("", 1, 2, 3, 4);
$contrainteProvenance[7] = array("", 1, 2, 3, 4);
$contrainteProvenance[8] = array("", 5, 8);

// Chargement des boxes d'urgences
$listServicesUrgence = CService::loadServicesUrgence();


$module_orumip = CModule::getActive("orumip");
$orumip_active = $module_orumip && $module_orumip->mod_active;

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("group"               , $group);
$smarty->assign("line"                , new CPrescriptionLineMedicament());
$smarty->assign("listServicesUrgence" , $listServicesUrgence);
$smarty->assign("contrainteProvenance", $contrainteProvenance);
$smarty->assign("userSel"             , $user);
$smarty->assign("today"               , mbDate());
$smarty->assign("traitement"          , $traitement);
$smarty->assign("antecedent"          , $antecedent);
$smarty->assign("rpu"                 , $rpu);
$smarty->assign("sejour"              , $sejour);
$smarty->assign("patient"             , $patient);
$smarty->assign("listResponsables"    , $listResponsables);
$smarty->assign("listPrats"           , $listPrats);
$smarty->assign("praticien"           , $praticien);
$smarty->assign("orumip_active"       , $orumip_active);
$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));
$smarty->assign("isImedsInstalled"    , (CModule::getActive("dPImeds") && CImeds::getTagCIDC(CGroups::loadCurrent())));
$smarty->display("vw_aed_rpu.tpl");
?>