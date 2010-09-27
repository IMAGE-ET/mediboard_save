<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

global $AppUI, $can, $m, $tab;

$can->needsRead();

$group = CGroups::loadCurrent();
$user = new CMediusers();
if (CAppUI::conf("dPurgences only_prat_responsable")) {
  $listResponsables = $user->loadPraticiens(PERM_READ, $group->service_urgences_id);
} else {
  $listResponsables = $user->loadUsers(PERM_READ, $group->service_urgences_id);
}
$listPrats = $user->loadPraticiens(PERM_READ, $group->service_urgences_id);

$rpu    = new CRPU;
$rpu_id = CValue::getOrSession("rpu_id");
if ($rpu_id && !$rpu->load($rpu_id)) {
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

// Chargement des aides a la saisie
$rpu->loadAides($AppUI->user_id);

if ($rpu->_id || $rpu->sejour_id) {
  $sejour  = $rpu->_ref_sejour;
  $patient = $sejour->_ref_patient;
  $patient->loadStaticCIM10($AppUI->user_id);
  
  // Chargement de l'IPP ($_IPP)
  $patient->loadIPP();
  // Chargement du numero de dossier ($_num_dossier)
  $sejour->loadNumDossier();
	$sejour->loadRefPraticien(1);
	$praticien = $sejour->_ref_praticien;
  $listResponsables[$praticien->_id] = $praticien;
} 
else {
  $rpu->_responsable_id = $AppUI->user_id;
  $rpu->_entree         = mbDateTime();
  $sejour               = new CSejour;
  $patient              = new CPatient;
}

// Gestion des traitements, antecedents, diagnostics
$traitement = new CTraitement();
$traitement->loadAides($AppUI->user_id);

$antecedent = new CAntecedent();
$antecedent->loadAides($AppUI->user_id);

// Chargement du praticien courant
$userSel = new CMediusers();
$userSel->load($AppUI->user_id);

// Contraintes sur le mode d'entree / provenance
$contrainteProvenance[6] = array("", 1, 2, 3, 4);
$contrainteProvenance[7] = array("", 1, 2, 3, 4);
$contrainteProvenance[8] = array("", 5, 8);

// Chargement des boxes d'urgences
$listServicesUrgence = CService::loadServicesUrgence();

// Chargement des etablissements externes
$order = "nom";
$etab = new CEtabExterne();
$listEtab = $etab->loadList(null, $order);

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("group"               , $group);
$smarty->assign("line"                , new CPrescriptionLineMedicament());
$smarty->assign("listServicesUrgence" , $listServicesUrgence);
$smarty->assign("contrainteProvenance", $contrainteProvenance);
$smarty->assign("userSel"             , $userSel);
$smarty->assign("today"               , mbDate());
$smarty->assign("traitement"          , $traitement);
$smarty->assign("antecedent"          , $antecedent);
$smarty->assign("rpu"                 , $rpu);
$smarty->assign("sejour"              , $sejour);
$smarty->assign("patient"             , $patient);
$smarty->assign("listResponsables"    , $listResponsables);
$smarty->assign("listPrats"           , $listPrats);
$smarty->assign("listEtab"            , $listEtab);
$smarty->assign("praticien"           , $praticien);
$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));
$smarty->assign("isImedsInstalled"    , CModule::getActive("dPImeds"));
$smarty->display("vw_aed_rpu.tpl");
?>