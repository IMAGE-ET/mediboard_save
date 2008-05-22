<?php /* $Id: $*/

/**
* @package Mediboard
* @subpackage dPboard
* @version $Revision: $
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m, $g, $dPconfig;

$can->needsRead();

$date = mbGetValueFromGetOrSession("date", mbDate());
$praticien_id = mbGetValueFromPostOrSession("praticien_id");
$prat = false;

// Chargement de l'utilisateur courant
$userCourant = new CMediusers;
$userCourant->load($AppUI->user_id);

// Test du type de l'utilisateur courant
$secretaire = $userCourant->isFromType(array("Secr�taire"));
$admin = $userCourant->isFromType(array("Administrator"));

// Redirect
if(!$userCourant->isPraticien() && !$secretaire && !$admin) {
  $AppUI->redirect("m=system&a=access_denied");
}

$listPraticiens = array();
// Si le user est secretaire ou admin
if($secretaire || $admin){
  // Chargement de la liste de praticien
  $mediuser = new CMediusers();
  $listPraticiens = $userCourant->loadPraticiens(PERM_EDIT);
  
  // Chargement du praticien selectionne
  $pratSel = $userCourant->load($praticien_id);
}

// Si le user courant est un praticien
if($userCourant->isPraticien()){
  $pratSel = $userCourant;
  $prat = true;
}


$prec = mbDate("-1 day", $date);
$suiv = mbDate("+1 day", $date);

// Consultations
$vue2_default = isset($AppUI->user_prefs["AFFCONSULT"]) ? $AppUI->user_prefs["AFFCONSULT"] : 0 ;
$vue          = mbGetValueFromGetOrSession("vue2", $vue2_default);

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("prat", $prat);
$smarty->assign("pratSel"        , $pratSel);
$smarty->assign("praticien_id"   , $praticien_id);
$smarty->assign("listPraticiens" , $listPraticiens);
$smarty->assign("secretaire"     , $secretaire);
$smarty->assign("admin"          , $admin);
$smarty->assign("prec"           , $prec);
$smarty->assign("suiv"           , $suiv);
$smarty->assign("date"           , $date);
$smarty->assign("vue"            , $vue);

$smarty->display("vw_day.tpl");

?>