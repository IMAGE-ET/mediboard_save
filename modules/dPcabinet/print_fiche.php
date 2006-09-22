<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canEdit) {
  $AppUI->redirect("m=system&a=access_denied");
}

$date = mbGetValueFromGetOrSession("date", mbDate());
$today = mbDate();

$consultation_id = mbGetValueFromGet("consultation_id");

// Consultation courante
$consult = new CConsultation();
if ($consultation_id) {
  $consult->load($consultation_id);
  $consult->loadRefs();
  if($consult->_ref_consult_anesth->consultation_anesth_id) {
    $consult->_ref_consult_anesth->loadRefs();
  }
  $praticien =& $consult->_ref_chir;
  $praticien->loadRefs();
  $patient =& $consult->_ref_patient;
  $patient->loadRefs();
  foreach ($patient->_ref_consultations as $key => $value) {
    $patient->_ref_consultations[$key]->loadRefs();
    $patient->_ref_consultations[$key]->_ref_plageconsult->loadRefs();
  }
  foreach ($patient->_ref_sejours as $key => $sejour) {
    $patient->_ref_sejours[$key]->loadRefsFwd();
    $patient->_ref_sejours[$key]->loadRefsOperations();
    foreach($patient->_ref_sejours[$key]->_ref_operations as $keyOp => $op) {
      $patient->_ref_sejours[$key]->_ref_operations[$keyOp]->loadRefsFwd();
    }
  }
}

// Classement des antecedents
$antecedent = new CAntecedent();
$listAnt = array();
foreach($antecedent->_enums["type"] as $nameantecedent){
  $listAnt[$nameantecedent] = array();
}
foreach($patient->_ref_antecedents as $keyAnt=>$currAnt){
  $listAnt[$currAnt->type][$keyAnt] = $currAnt;
}
// Cr�ation du template
$smarty = new CSmartyDP(1);

$smarty->assign("consult", $consult);
$smarty->assign("listAnt", $listAnt);

$smarty->display("print_fiche.tpl");
?>