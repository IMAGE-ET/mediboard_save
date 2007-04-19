<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision: $
 *  @author Romain Ollivier
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$user = new CMediusers;
$user->load($AppUI->user_id);

$patient_id = mbGetValueFromGetOrSession("patient_id");

$prescription_labo_id = mbGetValueFromGetOrSession("prescription_labo_id");
$prescription_labo_examen_id = mbGetValueFromGetOrSession("prescription_labo_examen_id");

if (!$patient_id) {
  return;
}

// Chargement de la prescription demand�e
$prescription = new CPrescriptionLabo();
$prescription->load($prescription_labo_id);
$prescription->loadRefs();

$patient = new CPatient();
$patient->load($patient_id);

$patient->loadRefsPrescriptions();

foreach($patient->_ref_prescriptions as &$curr_prescription) {
  if(!$curr_prescription->canEdit()) {
    unset($curr_prescription);
  } else {
    $curr_prescription->loadRefs();
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("patient"     , $patient     );
$smarty->assign("prescription_labo_examen_id", $prescription_labo_examen_id);
$smarty->assign("prescription", $prescription);

$smarty->display("inc_vw_prescriptions.tpl");

?>
