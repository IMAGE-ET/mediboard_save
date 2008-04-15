<?php /* $Id:  $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Fabien M�nager
*/


global $AppUI, $can, $m;

$can->needsEdit();

$prat_id = mbGetValueFromGetOrSession("prat_id");

// On charge le praticien
$userSel = new CMediusers;
$userSel->load($prat_id);
$userSel->loadRefs();
$canUserSel = $userSel->canDo();

// V�rification des droits sur les praticiens
$listChir = $userSel->loadPraticiens(PERM_EDIT);

if (!$userSel->isPraticien()) {
  $AppUI->setMsg("Vous devez s�l�ctionner un praticien", UI_MSG_ALERT);
  $AppUI->redirect("m=dPcabinet&tab=0");
}

$canUserSel->needsEdit(array("chirSel"=>0));

// Chargement des aides � la saisie
$addiction = new CAddiction();
$addiction->loadAides($userSel->user_id);

$antecedent = new CAntecedent();
$antecedent->loadAides($userSel->user_id);

$traitement = new CTraitement();
$traitement->loadAides($userSel->user_id);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("addiction" , $addiction);
$smarty->assign("antecedent", $antecedent);
$smarty->assign("traitement", $traitement);

$smarty->display("vw_ant_easymode.tpl");
?>