<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $g, $dPconfig;

$can->needsEdit();

$pat_id = mbGetValueFromGetOrSession("pat_id");


// Chargement du dossier patient
$patient = new CPatient;
$patient->load($pat_id);

if ($patient->patient_id) {
  $patient->loadIPP();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("patient"         , $patient );
$smarty->assign("hprim21installed", CModule::getActive("hprim21"));

$smarty->display("inc_ipp_form.tpl");