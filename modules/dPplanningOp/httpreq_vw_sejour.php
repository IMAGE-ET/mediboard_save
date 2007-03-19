<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $tab, $dPconfig;

$mode_operation = mbGetValueFromGet("mode_operation", 0);
$sejour_id      = mbGetValueFromGet("sejour_id"     , 0);
$patient_id     = mbGetValueFromGet("patient_id"    , 0);

// Droit de lecture dPsante400
$moduleSante400 = CModule::getInstalled("dPsante400");
$canSante400    = $moduleSante400 ? $moduleSante400->canDo() : new CCanDo;

// Liste des Etablissements selon Permissions
$etablissements = new CMediusers();
$etablissements = $etablissements->loadEtablissements(PERM_READ);

$sejour = new CSejour;
$praticien = new CMediusers;
if($sejour_id) {
  $sejour->load($sejour_id);
  $sejour->loadRefsFwd();
  $praticien =& $sejour->_ref_praticien;
  $patient =& $sejour->_ref_patient;
  $patient->loadRefsSejours();
  $sejours =& $patient->_ref_sejours;
} else {
  $patient = new CPatient;
  $patient->load($patient_id);
  $patient->loadRefsSejours();
  $sejours =& $patient->_ref_sejours;
}

// L'utilisateur est-il un praticien
$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);

// V�rification des droits sur les praticiens
$listPraticiens = $mediuser->loadPraticiens(PERM_EDIT);

$sejourConfig =& $dPconfig["dPplanningOp"]["sejour"];
for ($i = $sejourConfig["heure_deb"]; $i <= $sejourConfig["heure_fin"]; $i++) {
    $hours[] = $i;
}

for ($i = 0; $i < 60; $i += $sejourConfig["min_intervalle"]) {
    $mins[] = $i;
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("canSante400", $canSante400);

$smarty->assign("sejour"   , $sejour);
$smarty->assign("praticien", $praticien);
$smarty->assign("patient"  , $patient);
$smarty->assign("sejours"  , $sejours);

$smarty->assign("listPraticiens", $listPraticiens);
$smarty->assign("hours"      , $hours);
$smarty->assign("mins"       , $mins);
$smarty->assign("mode_operation", $mode_operation);
$smarty->assign("etablissements", $etablissements);

$smarty->display("inc_form_sejour.tpl");

?>