<?php 

/**
 * $Id$
 *  
 * @category Pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

CCanDo::checkEdit();

$group = CGroups::loadCurrent();

// Chargement du patient
$patient = new CPatient();
$patient->load(CValue::get("patient_id"));
$patient->loadIPP();
$patient->loadRefsCorrespondants();
$patient->loadRefPhotoIdentite();
$patient->loadPatientLinks();
$patient->countINS();
if (CModule::getActive("fse")) {
  $cv = CFseFactory::createCV();
  if ($cv) {
    $cv->loadIdVitale($patient);
  }
}

// Chargement du s�jour
$sejour  = new CSejour();
$sejour_maman  = new CSejour();
$sejour->load(CValue::get("sejour_id"));
if ($sejour->patient_id == $patient->_id) {
  $sejour->canDo();
  $sejour->loadNDA();
  $sejour->loadExtDiagnostics();
  $sejour->loadRefsAffectations();
  $sejour->loadSuiviMedical();
  $sejour->_ref_patient = $patient;
  foreach ($sejour->loadRefsOperations() as $_op) {
    $_op->loadRefPraticien();
    $_op->loadRefPlageOp();
    $_op->loadRefAnesth();
    $_op->loadRefsConsultAnesth();
    $_op->loadBrancardage();
  }
  $sejour->loadRefsConsultAnesth();

  /**
   * Gestion des s�jours obst�triques
   **/

  // Dans le cadre o� le dossier pmsi est celui de l'enfant
  $naissance_enf = $sejour->loadUniqueBackRef("naissance");
  if ($naissance_enf && $naissance_enf->_id) {
    /** @var CNaissance $naissance_enf */
    $naissance_enf->canDo();
    $naissance_enf->loadRefGrossesse();
    $sejour_enf = $naissance_enf->loadRefSejourEnfant();
    $sejour_enf->loadRelPatient();
    $sejour_enf->loadRefUFHebergement();
    $sejour_enf->loadRefUFMedicale();
    $sejour_enf->loadRefUFSoins();
    $sejour_enf->loadRefService();
    $sejour_enf->loadRefsNotes();

    // Chargement du s�jour de la maman
    $sejour_maman = $naissance_enf->loadRefSejourMaman();
    if ($sejour_maman && $sejour_maman->_id) {
      $sejour_maman->loadRefGrossesse();
      $sejour_maman->_ref_grossesse->canDo();
      $grossesse = $sejour_maman->_ref_grossesse;

      $grossesse->loadLastAllaitement();
      $grossesse->loadFwdRef("group_id");

      $sejour_maman->canDo();
      $sejour_maman->_ref_patient = $grossesse->loadRefParturiente();
      $sejour_maman->loadRefUFHebergement();
      $sejour_maman->loadRefUFMedicale();
      $sejour_maman->loadRefUFSoins();
      $sejour_maman->loadRefService();
      $sejour_maman->loadRefsNotes();

      foreach ( $grossesse->loadRefsNaissances() as $_naissance) {
        $_naissance->loadRefSejourEnfant();
        $_naissance->_ref_sejour_enfant->loadRelPatient();
      }
    }
  }

  // Dans le cadre o� le dossier pmsi est celui de la maman
  if ($sejour->grossesse_id) {
    $sejour->canDo();
    $sejour->loadRefUFHebergement();
    $sejour->loadRefUFMedicale();
    $sejour->loadRefUFSoins();
    $sejour->loadRefService();
    $sejour->loadRefsNotes();
    $sejour->loadRefGrossesse();
    $sejour->_ref_grossesse->canDo();

    $grossesse = $sejour->_ref_grossesse;
    $grossesse->loadLastAllaitement();
    $grossesse->loadFwdRef("group_id");

    foreach ($grossesse->loadRefsNaissances() as $_naissance) {
      $_naissance->loadRefSejourEnfant();
      $_naissance->_ref_sejour_enfant->loadRelPatient();
    }
  }
}
else {
  $sejour = new CSejour();
}

// Cr�ation du template
$smarty = new CSmartyDP("modules/dPpmsi");

$smarty->assign("canPatients"     , CModule::getCanDo("dPpatients"));
$smarty->assign("hprim21installed", CModule::getActive("hprim21"));
$smarty->assign("isImedsInstalled", (CModule::getActive("dPImeds") && CImeds::getTagCIDC($group)));
$smarty->assign("patient"         , $patient);
$smarty->assign("sejour"          , $sejour);
$smarty->assign("sejour_maman"    , $sejour_maman);
$smarty->assign("naissance"       , $naissance_enf);


$smarty->display("inc_vw_dossier_sejour.tpl");