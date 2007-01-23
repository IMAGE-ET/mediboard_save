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

// Utilisateur s�lectionn� ou utilisateur courant
$prat_id      = mbGetValueFromGetOrSession("chirSel", 0);
$noReglement  = mbGetValueFromGet("noReglement" , 0);

$userSel = new CMediusers;
$userSel->load($prat_id ? $prat_id : $AppUI->user_id);
$userSel->loadRefs();

// V�rification des droits sur les praticiens
$listChir = $userSel->loadPraticiens(PERM_EDIT);

if (!$userSel->isPraticien()) {
  $AppUI->setMsg("Vous devez selectionner un praticien", UI_MSG_ALERT);
  $AppUI->redirect("m=dPcabinet&tab=0");
}

if (!$userSel->canEdit()) {
  $AppUI->setMsg("Vous n'avez pas les droits suffisants", UI_MSG_ALERT);
  $AppUI->redirect("m=dPcabinet&tab=0");
}

$selConsult = mbGetValueFromGetOrSession("selConsult");

// Consultation courante
$consult = new CConsultation();
$consult->_ref_chir = $userSel;
if ($selConsult) {
  $consult->load($selConsult);
  $consult->loadRefsFwd();
  $consult->loadRefsDocs();
  $consult->loadRefsFiles();
  
  // On v�rifie que l'utilisateur a les droits sur la consultation
  $right = false;
  foreach($listChir as $key => $value) {
    if($value->user_id == $consult->_ref_plageconsult->chir_id)
      $right = true;
  }
  if(!$right) {
    $AppUI->setMsg("Vous n'avez pas acc�s � cette consultation", UI_MSG_ALERT);
    $AppUI->redirect("m=dPpatients&tab=0&id=$consult->patient_id");
  }
}

// R�cup�ration des mod�les
$whereCommon = array();
$whereCommon["object_id"] = "IS NULL";
if($consult->_ref_consult_anesth->consultation_anesth_id){
  $whereCommon[] = "`object_class` = 'CConsultAnesth'";
}else{
  $whereCommon[] = "`object_class` = 'CConsultation'";
}

$order = "nom";

// Mod�les de l'utilisateur
$listModelePrat = array();
if ($userSel->user_id) { 
  $where = $whereCommon;
  $where["chir_id"] = db_prepare("= %", $userSel->user_id);
  $listModelePrat = new CCompteRendu;
  $listModelePrat = $listModelePrat->loadlist($where, $order);
}

// Mod�les de la fonction
$listModeleFunc = array();
if ($userSel->user_id) {
  $where = $whereCommon;
  $where["function_id"] = db_prepare("= %", $userSel->function_id);
  $listModeleFunc = new CCompteRendu;
  $listModeleFunc = $listModeleFunc->loadlist($where, $order);
}

// R�cup�ration des tarifs
$order = "description";
$where = array();
$where["chir_id"] = "= '$userSel->user_id'";
$tarifsChir = new CTarif;
$tarifsChir = $tarifsChir->loadList($where, $order);
$where = array();
$where["function_id"] = "= '$userSel->function_id'";
$tarifsCab = new CTarif;
$tarifsCab = $tarifsCab->loadList($where, $order);

// Cr�ation du template
$smarty = new CSmartyDP();

if($consult->_ref_chir->isFromType(array("Anesth�siste")) || $consult->_ref_consult_anesth->consultation_anesth_id) {
  $_is_anesth=true;	
} else {
  $_is_anesth=false;
}
$smarty->assign("_is_anesth", $_is_anesth);  

$smarty->assign("listModelePrat", $listModelePrat);
$smarty->assign("listModeleFunc", $listModeleFunc);
$smarty->assign("tarifsChir"    , $tarifsChir);
$smarty->assign("tarifsCab"     , $tarifsCab);
$smarty->assign("consult"       , $consult);
$smarty->assign("noReglement"   , $noReglement);

$smarty->display("inc_fdr_consult.tpl");

?>