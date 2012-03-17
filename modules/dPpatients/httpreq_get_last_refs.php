<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

$patient_id = CValue::get("patient_id", 0);
$consultation_id = CValue::get("consultation_id", 0);
$is_anesth = CValue::get("is_anesth", 1);

$patient = new CPatient;
$patient->load($patient_id);
$where = array("group_id" => "= '".CGroups::loadCurrent()->_id."'");

foreach ($patient->loadRefsSejours($where) as $_sejour) {
  foreach ($_sejour->loadRefsConsultations() as $_consult) {
    $_consult->getType();
    $_consult->loadRefPlageConsult();
  }
  
  foreach ($_sejour->loadRefsOperations() as $_operation) {
    $_operation->loadRefsFwd();
  }
}

foreach ($patient->loadRefsConsultations() as $_consult) {
  if ($_consult->sejour_id) {
    unset($patient->_ref_consultations[$_consult->_id]);
    continue;
  }
  
  $_consult->loadRefsFwd();
  $_consult->getType();
  $_consult->loadRefPraticien();
  $_consult->loadRefPlageConsult();
}

$consultation = new CConsultation();
$consultation->load($consultation_id);
$consultation->loadRefConsultAnesth();

if (CCanDo::read()) {
  // Cr�ation du template
  $smarty = new CSmartyDP();
  
  $smarty->assign("is_anesth", $is_anesth);
  $smarty->assign("consultation", $consultation);
  $smarty->assign("patient", $patient);

  $smarty->display("httpreq_get_last_refs.tpl");
}

?>