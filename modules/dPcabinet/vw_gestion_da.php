<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkRead();
$conusultation_id  = CValue::getOrSession("conusultation_id");
$consult_anesth  = CValue::getOrSession("consult_anesth_id");

$consult = new CConsultation();
$consult->load($conusultation_id);
$patient = $consult->loadRefPatient();
$patient->loadRefsSejours();
foreach ($patient->_ref_sejours as $_sejour) {
  $_sejour->loadRefsOperations();
  foreach ($_sejour->_ref_operations as $op) {
    $op->loadRefPlageOp();
    $op->loadRefChir();
  }
}
$patient->getNextSejourAndOperation();
// Chargement du patient
$patient = $consult->_ref_patient;
$patient->countBackRefs("consultations");
$patient->countBackRefs("sejours");
$patient->loadRefs();
$patient->loadRefsNotes();
$patient->loadRefPhotoIdentite();

$ops_sans_dossier_anesth = array();
// Chargement de ses s�jours
foreach ($patient->_ref_sejours as $_key => $_sejour) {
  $_sejour->loadRefsOperations();
  foreach ($_sejour->_ref_operations as $_key_op => $_operation) {
    $_operation->loadRefsFwd();
    $_operation->_ref_chir->loadRefFunction()->loadRefGroup();
    if (!$_operation->_ref_consult_anesth->_id) {
      $ops_sans_dossier_anesth[] = $_operation;
    }
  }
  $_sejour->loadRefsFwd();
}


$consult->loadRefPraticien();
$consult->loadRefsDossiersAnesth();
$consult->loadRefConsultAnesth();

$tab_op = array();
foreach ($consult->_refs_dossiers_anesth as $consultation_anesth) {
  $consultation_anesth->loadRelPatient();
  $consultation = $consultation_anesth->_ref_consultation;
  $consultation->_ref_patient->loadRefConstantesMedicales(null, array("poids"), $consultation);

  $consultation_anesth->loadRefOperation()->loadRefSejour();
  $consultation_anesth->_ref_operation->_ref_sejour->loadRefDossierMedical();
}

$dossier_medical_patient = $patient->loadRefDossierMedical();
$dossier_medical_patient->loadRefsAntecedents();
$dossier_medical_patient->loadRefsTraitements();
$dossier_medical_patient->loadRefPrescription();

$user = new CMediusers();
$user->load($consult->_ref_praticien->_id);
$listChirs   = $user->loadPraticiens(PERM_READ);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("consult" , $consult);
$smarty->assign("patient" , $patient);
$smarty->assign("dm_patient"              , $dossier_medical_patient);
$smarty->assign("ops_sans_dossier_anesth" , $ops_sans_dossier_anesth);
$smarty->assign("first_operation"         , reset($ops_sans_dossier_anesth));
$smarty->assign("consult_anesth"          , $consult_anesth);
$smarty->assign("listChirs"               , $listChirs);

$smarty->display("inc_consult_anesth/vw_gestion_da.tpl");