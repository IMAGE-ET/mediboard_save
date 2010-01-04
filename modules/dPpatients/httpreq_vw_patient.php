<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author S�bastien Fillonneau
*/

global $AppUI, $can, $m;

$can->needsRead();

$patient_id = CValue::getOrSession("patient_id", 0);

// R�cuperation du patient s�lectionn�
$patient = new CPatient();
if (CValue::get("new", 0)) {
  $patient->load(NULL);
  CValue::setSession("id", null);
} else {
  $patient->load($patient_id);
}

if ($patient->_id) {
  $patient->loadDossierComplet();
	$patient->loadIPP();
  $patient->loadIdVitale();
}

$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_EDIT);

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("patient"         , $patient         );
$smarty->assign("listPrat"        , $listPrat        );
$smarty->assign("canPatients"     , CModule::getCanDo("dPpatients"));
$smarty->assign("canAdmissions"   , CModule::getCanDo("dPadmissions"));
$smarty->assign("canPlanningOp"   , CModule::getCanDo("dPplanningOp"));
$smarty->assign("canCabinet"      , CModule::getCanDo("dPcabinet"));
$smarty->display("inc_vw_patient.tpl");
?>