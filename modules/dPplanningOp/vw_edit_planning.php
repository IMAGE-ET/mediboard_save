<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m, $tab, $dPconfig;

require_once( $AppUI->getModuleClass('dPplanningOp', 'planning') );
require_once( $AppUI->getModuleClass('dPplanningOp', 'sejour') );
require_once( $AppUI->getModuleClass('mediusers') );
require_once( $AppUI->getModuleClass('dPpatients', 'patients') );
require_once( $AppUI->getModuleClass('dPcompteRendu', 'pack') );

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

$operation_id = mbGetValueFromGetOrSession("operation_id", null);
$sejour_id = mbGetValueFromGetOrSession("sejour_id", null);
$chir_id = mbGetValueFromGet("chir_id", null);
$patient_id = mbGetValueFromGet("pat_id", null);

// L'utilisateur est-il un praticien
$chir = new CMediusers;
$chir->load($AppUI->user_id);
if ($chir->isPraticien() and !$chir_id) {
  $chir_id = $chir->user_id;
}

// Chargement du praticien
$chir = new CMediusers;
if ($chir_id) {
  $chir->load($chir_id);
}

// Chargement du patient
$patient = new CPatient;
if ($patient_id && !$operation_id && !$sejour_id) {
  $patient->load($patient_id);
  $patient->loadRefsSejours();
}

// V�rification des droits sur les praticiens
$listPraticiens = $chir->loadPraticiens(PERM_EDIT);

// On r�cup�re le s�jour
$sejour = new CSejour;
if($sejour_id && !$operation_id) {
  $sejour->load($sejour_id);
  $sejour->loadRefsFwd();
  if(!$chir_id) {
    $chir =& $sejour->_ref_praticien;
  }
  $patient =& $sejour->_ref_patient;
}

// On r�cup�re l'op�ration
$op = new COperation;
if ($operation_id) {
  $op->load($operation_id);

  // On v�rifie que l'utilisateur a les droits sur l'operation
  if (!array_key_exists($op->chir_id, $listPraticiens)) {
    $AppUI->setMsg("Vous n'avez pas acc�s � cette op�ration", UI_MSG_WARNING);
    $AppUI->redirect("m=$m&tab=$tab&operation_id=0");
  }

  $op->loadRefs();
  $sejour =& $op->_ref_sejour;
  $sejour->loadRefsFwd();
  $chir =& $sejour->_ref_praticien;
  $patient =& $sejour->_ref_patient;
}

// R�cup�ration des mod�les
$whereCommon = array();
$whereCommon["type"] = "= 'hospitalisation'";
$order = "nom";

// Mod�les de l'utilisateur
$listModelePrat = array();
if ($op->chir_id) {
  $where = $whereCommon;
  $where["chir_id"] = "= '".$op->_ref_chir->user_id."'";
  $listModelePrat = new CCompteRendu;
  $listModelePrat = $listModelePrat->loadlist($where, $order);
}

// Mod�les de la fonction
$listModeleFunc = array();
if ($op->chir_id) {
  $where = $whereCommon;
  $where["function_id"] = "= '".$op->_ref_chir->function_id."'";
  $listModeleFunc = new CCompteRendu;
  $listModeleFunc = $listModeleFunc->loadlist($where, $order);
}

// Packs d'hospitalisation
$listPack = array();
if($op->chir_id) {
  $where = array();
  $where["chir_id"] = "= '".$op->_ref_chir->user_id."'";
  $listPack = new CPack;
  $listPack = $listPack->loadlist($where, $order);
}

$sejourConfig =& $dPconfig["dPplanningOp"]["sejour"];
for ($i = $sejourConfig["heure_deb"]; $i <= $sejourConfig["heure_fin"]; $i++) {
    $hours[] = $i;
}

for ($i = 0; $i < 60; $i += $sejourConfig["min_intervalle"]) {
    $mins[] = $i;
}

$operationConfig =& $dPconfig["dPplanningOp"]["operation"];
for ($i = $operationConfig["duree_deb"]; $i <= $operationConfig["duree_fin"]; $i++) {
    $hours_duree[] = $i;
}

for ($i = 0; $i < 60; $i += $operationConfig["min_intervalle"]) {
    $mins_duree[] = $i;
}

// Cr�ation du template
require_once( $AppUI->getSystemClass ("smartydp" ) );
$smarty = new CSmartyDP(1);

$smarty->assign("op"         , $op);
$smarty->assign("sejour"     , $sejour);
$smarty->assign("chir"       , $chir);
$smarty->assign("praticien"  , $chir);
$smarty->assign("patient"    , $patient );
$smarty->assign("plage"      , $op->plageop_id ? $op->_ref_plageop : new CPlageop );

$smarty->assign("listPraticiens", $listPraticiens);
$smarty->assign("listModelePrat", $listModelePrat);
$smarty->assign("listModeleFunc", $listModeleFunc);
$smarty->assign("listPack"      , $listPack      );

$smarty->assign("hours"      , $hours);
$smarty->assign("mins"       , $mins);
$smarty->assign("hours_duree", $hours_duree);
$smarty->assign("mins_duree" , $mins_duree);

$smarty->display("vw_edit_planning.tpl");

?>