<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPpatients
 * @version $Revision$
 * @author Romain Ollivier
 */

global $can;

$can->needsEdit();

$patient_id = mbGetValueFromGetOrSession("patient_id");
$name       = mbGetValueFromGet("name");
$firstName  = mbGetValueFromGet("firstName");

$date = mbDate();

$patient = new CPatient;
$patient->load($patient_id);
$patient->loadRefsFwd();

// Chargement de l'ipp
$patient->loadIPP();
$patient->loadIdVitale();

if (!$patient_id) {
  $patient->nom    = $name;
  $patient->prenom = $firstName;
}

if (mbGetValueFromGet("useVitale")) {
  $patVitale = new CPatient();
  $patVitale->getValuesFromVitale();
  $patVitale->nullifyEmptyFields();
  $patient->extendsWith($patVitale);
  $patient->updateFormFields();
  $patient->_bind_vitale = "1";
}


// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("patient", $patient);
$smarty->display("vw_edit_patients.tpl");
?>