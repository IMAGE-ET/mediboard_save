<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

$today = date("d/m/Y");

$patient_id = CValue::get("patient_id");

// Cr�ation du patient
$patient = new CPatient();
$patient->load($patient_id);
$patient->updateNomPaysInsee();

$patient->loadRefsSejours();
foreach ($patient->_ref_sejours as $sejour) {
  $sejour->loadRefPraticien();
	$sejour->loadRefsOperations();
  $sejour->loadNumDossier();
  foreach($sejour->_ref_operations as $operation) {
    $operation->loadRefPlageOp();
		$operation->loadRefChir();
  }
}

$patient->loadRefsConsultations();
foreach($patient->_ref_consultations as $consultation) {
  $consultation->loadRefPlageConsult();
}

$patient->loadRefsCorrespondants();
$patient->loadIPP();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("patient", $patient);
$smarty->assign("today"  , $today  );

if (CAppUI::conf('dPpatients CPatient extended_print'))
  $smarty->display("print_patient_extended.tpl");
else
  $smarty->display("print_patient.tpl");

?>