<?php 

/**
* @package Mediboard
* @subpackage dPurgences
* @version $Revision:  $
* @author Alexis Granger
*/

function viewMsg($msg, $action, $txt = ""){
  global $AppUI, $m, $tab;
  $action = CAppUI::tr($action);
  if($msg){
    $AppUI->setMsg("$action: $msg", UI_MSG_ERROR );
    $AppUI->redirect("m=$m&tab=$tab");
    return;
  }
  $AppUI->setMsg("$action $txt", UI_MSG_OK );
}


global $AppUI;

// R�cup�ration du rpu
$rpu_id = mbGetValueFromPost("rpu_id");
$rpu = new CRPU();
$rpu->load($rpu_id);
$rpu->loadRefSejour();

$sejour_rpu =& $rpu->_ref_sejour;

// Creation du nouveau sejour et pre-remplissage des champs
$sejour = new CSejour();
$sejour->patient_id   = $sejour->rpu->patient_id;
$sejour->praticien_id = $sejour->rpu->praticien_id;
$sejour->group_id     = $sejour->rpu->group_id;
$sejour->entree_prevue = mbDateTime();
$sejour->sortie_prevue = mbDateTime("+ 1 day");
$sejour->entree_reelle = mbDateTime();
$sejour->type = "comp";
$sejour->DP = $sejour->rpu->DP;
$sejour->DR = $sejour->rpu->DR;
if($rpu->diag_infirmier){ 
  $sejour->rques = "Diagnostic infirmier: $rpu->diag_infirmier\n";
}
if($rpu->motif){
  $sejour->rques .= "Motif de recours aux urgences: $rpu->motif";
}
$msg = $sejour->store();
viewMsg($msg, "msg-CSejour-title-create");

// Chargement des actes de la prise en charge aux urgences
$consult_atu =& $rpu->_ref_consult;
$consult_atu->loadRefsActes();
foreach ($consult_atu->_ref_actes as $acte) {
  $acte->setObject($sejour);
  $acte->_adapt_object = 1;
  $msg = $acte->store();
  viewMsg($msg, "msg-$acte->_class_name-title-modify");
}


$consult_atu->codes_ccam = "";
$consult_atu->tarif = "Transfert S�jour";
$consult_atu->valide = "0";
$consult_atu->du_patient = 0;
$consult_atu->du_tiers = 0;
$consult_atu->secteur1 = 0;
$consult_atu->secteur2 = 0;
$msg = $consult_atu->store();
viewMsg($msg, "msg-CConsultation-title-modify");

// Sauvegarde du RPU
$rpu->orientation = "HO";
$rpu->mutation_sejour_id = $sejour->_id;
$rpu->GEMSA = "4";
$msg = $rpu->store();
viewMsg($msg, "msg-CRPU-title-close");

// Sauvegarde du sejour
$sejour_rpu->sortie_reelle = mbDateTime();
$sejour_rpu->mode_sortie = "transfert";
$sejour_rpu->annule = "1";
$sejour_rpu->etablissement_transfert_id = "";
$msg = $sejour_rpu->store();
viewMsg($msg, "msg-CSejour-title-close", "(Urgences)");

$AppUI->redirect("m=dPplanningOp&tab=vw_edit_sejour&sejour_id=$sejour->_id");

?>