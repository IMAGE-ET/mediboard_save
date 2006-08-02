<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPpatients"  , "patients"));
require_once($AppUI->getModuleClass("dPplanningOp", "planning"));
require_once($AppUI->getModuleClass("dPcabinet"   , "consultation"));
require_once($AppUI->getModuleClass("system", "user_log"));


if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

// D�finition des variables
$patient_id = mbGetValueFromGet("patient_id", 0);

//R�cup�ration du dossier complet patient
$patient = new CPatient;
$patient->load($patient_id);
$patient->loadDossierComplet();


$patient->loadLogs();

// log pour les s�jours
foreach($patient->_ref_sejours as $key => $value) {
  $patient->_ref_sejours[$key]->loadLogs();
  
  $patient->_ref_sejours[$key]->loadRefsOperations();
  // log pour les op�rations de ce s�jour
  foreach($patient->_ref_sejours[$key]->_ref_operations as $keyOp => $valueOp) {
  	$patient->_ref_sejours[$key]->_ref_operations[$keyOp]->loadRefsFwd();
    $patient->_ref_sejours[$key]->_ref_operations[$keyOp]->loadLogs();
  }
  
  $patient->_ref_sejours[$key]->loadRefsAffectations();  
  // log pour les Affectations de ce s�jour
  foreach($patient->_ref_sejours[$key]->_ref_affectations as $keyAf => $valueAf) {
    $patient->_ref_sejours[$key]->_ref_affectations[$keyAf]->loadLogs();
    $patient->_ref_sejours[$key]->_ref_affectations[$keyAf]->loadRefsFwd();
  }
}

// log pour les consultations
foreach($patient->_ref_consultations as $key => $value) {
  $patient->_ref_consultations[$key]->loadLogs();
}

// Cr�ation du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("patient" , $patient );

$smarty->display("vw_history.tpl");
?>