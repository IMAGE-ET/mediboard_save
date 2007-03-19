<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $can, $m;


$can->needsRead();

// D�finition des variables
$patient_id = mbGetValueFromGet("patient_id", 0);

//R�cup�ration du dossier complet patient
$patient = new CPatient;
$patient->load($patient_id);
$patient->loadDossierComplet();

$patient->loadLogs();

// log pour les s�jours
foreach($patient->_ref_sejours as $sejour) {
  $sejour->loadLogs();
  
  // log pour les op�rations de ce s�jour
  $sejour->loadRefsOperations();
  foreach($sejour->_ref_operations as $operation) {
  	$operation->loadRefsFwd();
    $operation->loadLogs();
  }
  
  // log pour les affectations de ce s�jour
  $sejour->loadRefsAffectations();  
  foreach($sejour->_ref_affectations as $affectation) {
    $affectation->loadLogs();
    $affectation->loadRefsFwd();
  }
}

// log pour les consultations
foreach($patient->_ref_consultations as $consultation) {
  $consultation->loadLogs();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("patient" , $patient );

$smarty->display("vw_history.tpl");
?>