<?php 

/**
 * $Id$
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

CApp::setMemoryLimit("512M");

$ds = CSQLDataSource::get("std");

$function_id = CValue::get("function_id");
$chir_ids    = CValue::get("chir_ids");
$date        = CValue::get("date", CMbDT::date());


// Praticiens s�lectionn�s
$user = new CMediusers;
$praticiens = array();
if ($function_id) {
  $praticiens = CConsultation::loadPraticiens(PERM_EDIT, $function_id);
}
if ($chir_ids) {
  $praticiens = $user->loadAll(explode("-", $chir_ids));
}

//plages de consultation
$where = array();
$where["chir_id"] = CSQLDataSource::prepareIn(array_keys($praticiens));
$where["date"] = " = '$date'";
$Pconsultation = new CPlageconsult();
$Pconsultations = $Pconsultation->loadList($where, array("debut"));

$nbConsultations = 0;
$consultations = array();
$resumes_patient = array();

/** @var $Pconsultations CPlageConsult[] */
foreach ($Pconsultations as $_plage_consult) {
  $_plage_consult->loadRefsConsultations(false, false);
  $_plage_consult->loadRefChir();
  $_plage_consult->countPatients();
  $_plage_consult->_ref_chir->loadRefFunction();
  $_plage_consult->updateFormFields();
  $_plage_consult->loadFillRate();

  /** @var $consultations CConsultation[] */
  foreach ($_plage_consult->_ref_consultations as $_consult) {
    if (isset($resumes_patient[$_consult->patient_id])) {
      continue;
    }
    $patient = $_consult->loadRefPatient();
    $patient->loadDossierComplet();
    $patient->loadRefDossierMedical();
    $smarty = new CSmartyDP();
    $smarty->assign("offline", 1);
    $smarty->assign("patient", $_consult->_ref_patient);
    $resumes_patient[$patient->_id] = $smarty->fetch("vw_resume.tpl");  //dynamic assignment

    if ($_consult->patient_id) {
      $nbConsultations++;
    }
  }
}


//smarty global
$smarty = new CSmartyDP();
$smarty->assign("consultations", $Pconsultations);
$smarty->assign("nbConsultations", $nbConsultations);
$smarty->assign("date_generated", CMbDT::dateTime());
$smarty->assign("praticiens", $praticiens);
$smarty->assign("resumes_patient", $resumes_patient);
$smarty->assign("date", $date);
$smarty->display("vw_offline/consult_patients.tpl");