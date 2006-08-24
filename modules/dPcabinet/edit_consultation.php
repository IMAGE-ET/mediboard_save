<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("mediusers"));
require_once($AppUI->getModuleClass("dPcabinet"    , "plageconsult"));
require_once($AppUI->getModuleClass("dPcabinet"    , "consultation"));
require_once($AppUI->getModuleClass("dPcabinet"    , "tarif"));
require_once($AppUI->getModuleClass("dPcompteRendu", "compteRendu"));

if (!$canEdit) {
	$AppUI->redirect("m=system&a=access_denied");
}


$date  = mbGetValueFromGetOrSession("date", mbDate());
$vue   = mbGetValueFromGetOrSession("vue2", 0);
$today = mbDate();
$hour  = mbTime(null);
$_is_anesth = false;

$prat_id    = mbGetValueFromGetOrSession("chirSel", 0);
$selConsult = mbGetValueFromGetOrSession("selConsult", 0);

// On r�cup�re la consultation demand�e
if($selConsult){
  $consult = new CConsultation();
  $consult->load($selConsult);
  $consult->loadRefs();
  // On m�morise le praticien demand�
  $prat_id = $consult->_ref_plageconsult->chir_id;
  mbSetValueToSession("chirSel", $prat_id);
}else{
  // Si un chirurgien est pass� en parametre
  if(!$prat_id) $prat_id = $AppUI->user_id;
}

// V�rification des droits pour le praticien demand� (via chirSel ou par la consultation)
$userSel = new CMediusers;
$userSel->load($prat_id);
$userSel->loadRefs();

// V�rification des droits sur les praticiens
$listChir = $userSel->loadPraticiens(PERM_EDIT);

if (!$userSel->isPraticien()) {
  $AppUI->setMsg("Vous devez selectionner un praticien", UI_MSG_ALERT);
  $AppUI->redirect("m=dPcabinet&tab=0");
}

if (!$userSel->isAllowed(PERM_EDIT)) {
  mbSetValueToSession("chirSel", 0);
  $AppUI->setMsg("Vous n'avez pas les droits suffisants", UI_MSG_ALERT);
  $AppUI->redirect("m=dPcabinet&tab=0");
}

if (isset($_GET["date"])) {
  $selConsult = null;
  mbSetValueToSession("selConsult", 0);
}


//Liste des types d'anesth�sie
$anesth = dPgetSysVal("AnesthType");


// Consultation courante
$consult = new CConsultation();
$consult->_ref_chir = $userSel;
$consult->_ref_consult_anesth->consultation_anesth_id = 0;
if ($selConsult) {
  $consult->load($selConsult);
  $consult->loadRefs();
  $userSel->load($consult->_ref_plageconsult->chir_id);
  $userSel->loadRefs();
  $consult->loadAides($userSel->user_id);

  if($consult->_ref_consult_anesth->consultation_anesth_id) {
    $consult->_ref_consult_anesth->loadRefs();
  }
  
  $patient =& $consult->_ref_patient;
  $patient->loadRefs();
  $patient->loadStaticCIM10($userSel->user_id);
  foreach($patient->_ref_consultations as $key => $consul) {
    $patient->_ref_consultations[$key]->loadRefsFwd();
  }
  foreach($patient->_ref_sejours as $key => $sejour) {
    $patient->_ref_sejours[$key]->loadRefsFwd();
    $patient->_ref_sejours[$key]->loadRefsOperations();
    foreach($patient->_ref_sejours[$key]->_ref_operations as $keyOp => $op) {
      $patient->_ref_sejours[$key]->_ref_operations[$keyOp]->loadRefsFwd();
    }
  }
  
  // Affecter la date de la consultation
  $date = $consult->_ref_plageconsult->date;
  
}

// R�cup�ration des mod�les
$whereCommon = array();
if($consult->_ref_consult_anesth->consultation_anesth_id){
  $whereCommon[] = "`type` = 'consultation' OR `type` = 'consultAnesth'";
}else{
  $whereCommon[] = "`type` = 'consultation'";
}

$order = "nom";

// Mod�les de l'utilisateur
$listModelePrat = array();
if ($userSel->user_id) {
  $where = $whereCommon;
  $where["chir_id"] = "= '$userSel->user_id'";
  $listModelePrat = new CCompteRendu;
  $listModelePrat = $listModelePrat->loadlist($where, $order);
}

// Mod�les de la fonction
$listModeleFunc = array();
if ($userSel->user_id) {
  $where = $whereCommon;
  $where["function_id"] = "= '$userSel->function_id'";
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
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("date"          , $date);
$smarty->assign("hour"          , $hour);
$smarty->assign("vue"           , $vue);
$smarty->assign("today"         , $today);
$smarty->assign("userSel"       , $userSel);
$smarty->assign("listModelePrat", $listModelePrat);
$smarty->assign("listModeleFunc", $listModeleFunc);
$smarty->assign("tarifsChir"    , $tarifsChir);
$smarty->assign("tarifsCab"     , $tarifsCab);
$smarty->assign("anesth"        , $anesth);
$smarty->assign("consult"       , $consult);

$antecedent = new CAntecedent();
$antecedent->loadAides($userSel->user_id);
$smarty->assign("antecedent", $antecedent);

$traitement = new CTraitement();
$traitement->loadAides($userSel->user_id);
$smarty->assign("traitement", $traitement);

if($consult->_ref_chir->isFromType(array("Anesth�siste")) || $consult->_ref_consult_anesth->consultation_anesth_id) {
  $_is_anesth=true;	
} else {
  $_is_anesth=false;
}
$smarty->assign("_is_anesth", $_is_anesth);  

if($_is_anesth) {
  $smarty->assign("consult_anesth", $consult->_ref_consult_anesth);
  $smarty->display("edit_consultation_anesth.tpl");
} else {
  $smarty->display("edit_consultation_old.tpl");
}
?>