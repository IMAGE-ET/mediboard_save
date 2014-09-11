<?php 

/**
 * $Id$
 *
 * @category DPpatients
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();
if (!CAppUI::pref("allowed_identity_status")) {
  //todo voir pour redirect
  CApp::rip();
}

$state          = CValue::get("state");
$page           = (int)CValue::get("page", 0);
$patients       = array();
$patients_state = array();

$patient         = new CPatient();
$patient->status = $state;

$count = (int)$patient->countMatchingList();

if ($count > 0) {
  /** @var CPatient[] $patients */
  $patients = $patient->loadMatchingList(null, "$page, 30");
  CPatient::massLoadIPP($patients);

  /** @var CPatientState $patients_state */
  $patients_state = CPatient::massLoadBackRefs($patients, "patient_state", "datetime");
  $mediusers      = CPatientState::massLoadFwdRef($patients_state, "mediuser_id");

  /** @var CPatientLink[] $link1 */
  $link1         = CPatient::massLoadBackRefs($patients, "patient_link1");
  /** @var CPatientLink[] $link2 */
  $link2         = CPatient::massLoadBackRefs($patients, "patient_link2");
  $patient_link1 = CPatientLink::massLoadFwdRef($link1, "patient_id2");
  $patient_link2 = CPatientLink::massLoadFwdRef($link2, "patient_id1");
  $patient_link  = $patient_link1+$patient_link2;
  CPatient::massLoadIPP($patient_link);

  foreach ($link1 as $_link1) {
    $_link1->_ref_patient_doubloon = $patient_link[$_link1->patient_id2];
  }

  foreach ($link2 as $_link2) {
    $_link2->_ref_patient_doubloon = $patient_link[$_link2->patient_id1];
  }

  foreach ($patients_state as $_patient_state) {
    /** @var CPatient $patient */
    $patient = $patients[$_patient_state->patient_id];

    $_patient_state->_ref_patient  = $patient;
    $_patient_state->_ref_mediuser = $mediusers[$_patient_state->mediuser_id];
  }

  foreach ($patients as $_patient) {
    $_patient->_ref_last_patient_states = current($_patient->_back["patient_state"]);
    if ($state == "dpot") {
      $_patient->_ref_patient_links = array_merge($_patient->_back["patient_link1"], $_patient->_back["patient_link2"]);
    }
  }
}

$smarty = new CSmartyDP();
$smarty->assign("count"   , $count);
$smarty->assign("patients", $patients);
$smarty->assign("state"   , $state);
$smarty->assign("page"    , $page);
$smarty->display("patient_state/inc_list_patient_state.tpl");