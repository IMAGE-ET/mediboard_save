<?php /* $Id: httpreq_vw_consult_anesth.php 23 2006-05-04 15:05:35Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: 23 $
* @author Romain Ollivier
*/

global $AppUI, $can, $m;
  
$can->needsEdit();

// Utilisateur s�lectionn� ou utilisateur courant
$prat_id = mbGetValueFromGetOrSession("chirSel", 0);

$userSel = new CMediusers;
$userSel->load($prat_id ? $prat_id : $AppUI->user_id);
$userSel->loadRefs();
$canUserSel = $userSel->canDo();

// V�rification des droits sur les praticiens
$listChir = $userSel->loadPraticiens(PERM_EDIT);

if (!$userSel->isPraticien()) {
  $AppUI->setMsg("Vous devez selectionner un praticien", UI_MSG_ALERT);
  $AppUI->redirect("m=dPcabinet&tab=0");
}

$canUserSel->needsEdit();

$selConsult = mbGetValueFromGetOrSession("selConsult", 0);
if (isset($_GET["date"])) {
  $selConsult = null;
  mbSetValueToSession("selConsult", 0);
}

$anesth = new CTypeAnesth;
$orderanesth = "name";
$anesth = $anesth->loadList(null,$orderanesth);


// Consultation courante
$consult = new CConsultation();
$consult->_ref_chir = $userSel;

if ($selConsult) {
  $consult->load($selConsult);
  
  $can->needsObject($consult);
  $canConsult = $consult->canDo();
  $canConsult->needsEdit();
  
  $consult->loadAides($userSel->user_id);
  $consult->loadRefConsultAnesth();
  $consult->_ref_consult_anesth->loadAides($userSel->user_id);
  $consult->loadRefPlageConsult();
  
  if($consult->_ref_consult_anesth->consultation_anesth_id) {
    $consult->_ref_consult_anesth->loadRefs();
  if($consult->_ref_consult_anesth->_ref_operation->operation_id)
    $consult->_ref_consult_anesth->_ref_operation->loadAides($userSel->user_id);
  }

  $consult_anesth =& $consult->_ref_consult_anesth;
  
} else {
  $consult->_ref_consult_anesth = new CConsultAnesth();
}

$consult_anesth =& $consult->_ref_consult_anesth;

$techniquesComp = new CTechniqueComp();
$techniquesComp->loadAides($userSel->user_id);


// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("consult"       , $consult       );
$smarty->assign("consult_anesth", $consult_anesth);
$smarty->assign("anesth"        , $anesth        );
$smarty->assign("techniquesComp", $techniquesComp);
$smarty->display("inc_consult_anesth/acc_infos_anesth.tpl");

?>