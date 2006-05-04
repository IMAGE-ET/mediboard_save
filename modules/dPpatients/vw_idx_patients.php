<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once( $AppUI->getModuleClass('dPpatients', 'patients') );
require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );
require_once( $AppUI->getModuleClass('dPcabinet', 'consultation') );

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

// L'utilisateur est-il un chirurgien
$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);
if ($mediuser->isFromType(array("Chirurgien"))) {
  $chir = $mediuser;
} else
  $chir = null;

// L'utilisateur est-il un anesth�siste
$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);
if ($mediuser->isFromType(array("Anesth�siste"))) {
  $anesth = $mediuser;
} else
  $anesth = null;

$patient_id = mbGetValueFromGetOrSession("id", 0);

// R�cuperation du patient s�lectionn�
$patient = new CPatient;
if(dPgetParam($_GET, "new", 0)) {
  $patient->load(NULL);
  mbSetValueToSession("id", null);
} else {
  $patient->load($patient_id);
}

if($patient->patient_id) {
  $patient->loadRefs();
  if($patient->_ref_curr_affectation->affectation_id) {
    $patient->_ref_curr_affectation->loadRefsFwd();
    $patient->_ref_curr_affectation->_ref_lit->loadRefsFwd();
    $patient->_ref_curr_affectation->_ref_lit->_ref_chambre->loadRefsFwd();
  } elseif($patient->_ref_next_affectation->affectation_id) {
    $patient->_ref_next_affectation->loadRefsFwd();
    $patient->_ref_next_affectation->_ref_lit->loadRefsFwd();
    $patient->_ref_next_affectation->_ref_lit->_ref_chambre->loadRefsFwd();
  }
  foreach ($patient->_ref_operations as $key1 => $op) {
    $patient->_ref_operations[$key1]->loadRefs();
  }
  foreach ($patient->_ref_hospitalisations as $key1 => $op) {
    $patient->_ref_hospitalisations[$key1]->loadRefsFwd();
  }
  foreach ($patient->_ref_consultations as $key2 => $consult) {
    $patient->_ref_consultations[$key2]->loadRefs();
    $patient->_ref_consultations[$key2]->_ref_plageconsult->loadRefsFwd();
  }
}

// R�cuperation des patients recherch�s
$patient_nom       = mbGetValueFromGetOrSession("nom"   , '');
$patient_prenom    = mbGetValueFromGetOrSession("prenom", '');
$patient_naissance = mbGetValueFromGetOrSession("naissance", 'off');
$patient_day       = mbGetValueFromGetOrSession("Date_Day", date("d"));
$patient_month     = mbGetValueFromGetOrSession("Date_Month", date("m"));
$patient_year      = mbGetValueFromGetOrSession("Date_Year", date("Y"));

$where = null;
if ($patient_nom   ) $where[] = "nom LIKE '".addslashes($patient_nom)."%'";
if ($patient_prenom) $where[] = "prenom LIKE '".addslashes($patient_prenom)."%'";
if ($patient_naissance == "on")
  $where["naissance"] = "= '$patient_year/$patient_month/$patient_day'";

$patients = null;
if ($where) {
  $patients = new CPatient();
  $patients = $patients->loadList($where, "nom, prenom, naissance", "0, 100");
}

$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_EDIT);

$canEditCabinet = !getDenyEdit("dPcabinet");

// Cr�ation du template
require_once( $AppUI->getSystemClass ('smartydp' ) );
$smarty = new CSmartyDP;

$smarty->assign('nom', $patient_nom);
$smarty->assign('prenom', $patient_prenom);
$smarty->assign('naissance', $patient_naissance);
$smarty->assign('date', "$patient_year-$patient_month-$patient_day");
$smarty->assign('patients', $patients);
$smarty->assign('patient', $patient);
$smarty->assign('chir', $chir);
$smarty->assign('anesth', $anesth);
$smarty->assign('listPrat', $listPrat);
$smarty->assign('canEditCabinet', $canEditCabinet);

$smarty->display('vw_idx_patients.tpl');
?>