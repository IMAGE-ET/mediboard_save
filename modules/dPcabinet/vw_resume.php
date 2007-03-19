<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

$patient_id = mbGetValueFromGet("patient_id");

$patient = new CPatient;
$patient->load($patient_id);

$user = new CMediusers;
$user->load($AppUI->user_id);
$listPrat = $user->loadPraticiens(PERM_EDIT);

$patient->loadRefsFiles();
$patient->loadRefsDocs();
$where = array();
$where["plageconsult.chir_id"] = db_prepare_in(array_keys($listPrat));
$patient->loadRefsConsultations($where);
$patient->loadRefsSejours();
$patient->loadRefsAntecedents();
$patient->loadRefsTraitements();

$consultations =& $patient->_ref_consultations;
$sejours =& $patient->_ref_sejours;

$antecedent = new CAntecedent();
$listAnt = array();
foreach($antecedent->_enumsTrans["type"] as $keyAnt => $currAnt){
  $listAnt[$keyAnt] = array();
}
foreach($patient->_ref_antecedents as $keyAnt => $currAnt){
  $listAnt[$currAnt->type][$keyAnt] = $currAnt;
}

$docsCons = array();
$docsOp = array();
$filesCons = array();
$filesOp = array();

// Consultations
foreach($consultations as $key => $value) {
  $patient->_ref_consultations[$key]->loadRefsBack();
  $patient->_ref_consultations[$key]->loadRefPlageConsult();
}

// Sejours
$where = array();
$where["chir_id"] = db_prepare_in(array_keys($listPrat));
foreach($sejours as $key => $sejour) {
  $sejours[$key]->loadRefsOperations($where);
  foreach($sejours[$key]->_ref_operations as $keyOp => $op) {
    $sejours[$key]->_ref_operations[$keyOp]->loadRefPlageOp();
    $sejours[$key]->_ref_operations[$keyOp]->loadRefChir();
    $sejours[$key]->_ref_operations[$keyOp]->loadRefsFiles();
    $sejours[$key]->_ref_operations[$keyOp]->loadRefsDocs();
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("listAnt"       , $listAnt);
$smarty->assign("patient"       , $patient);
$smarty->assign("consultations" , $consultations );
$smarty->assign("sejours"       , $sejours);

$smarty->display("vw_resume.tpl");

?>