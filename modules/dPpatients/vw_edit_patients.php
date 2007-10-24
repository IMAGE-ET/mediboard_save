<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author Romain Ollivier
 */

global $can;

$can->needsRead();

$patient_id = mbGetValueFromGetOrSession("patient_id");
$name       = mbGetValueFromGet("name");
$firstName  = mbGetValueFromGet("firstName");

$date = mbDate();
$dateCMU = mbDate("+1 year", $date);

$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefsFwd();

// Chargement de l'ipp
$patient->loadIPP();

if (!$patient_id) {
  $patient->nom    = $name;
  $patient->prenom = $firstName;
}

if (mbGetValueFromGet("useVitale")) {
  $patient->getValuesFromVitaleEx();
  $patient->updateFormFields();
}


// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("dateCMU", $dateCMU);
$smarty->assign("patient", $patient);
$smarty->display("vw_edit_patients.tpl");
?>