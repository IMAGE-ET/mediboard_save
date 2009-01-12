<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Fabien M�nager
*/

$patient_id = mbGetValueFromGet('patient_id');
$mode = mbGetValueFromGet('mode', 'read');

$patient = new CPatient();
$patient->load($patient_id);
$patient->updateFormFields();
$patient->loadRefPhotoIdentite();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign('patient', $patient);
$smarty->assign('mode', $mode);
$smarty->display("inc_vw_photo_identite.tpl");

?>
