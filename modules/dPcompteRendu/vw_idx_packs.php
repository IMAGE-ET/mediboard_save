<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

// Utilisateur s�lectionn� ou utilisateur courant
$user_id = mbGetValueFromGetOrSession("filter_user_id", $AppUI->user_id);

$userSel = new CMediusers;
$userSel->load($user_id ? $user_id : $AppUI->user_id);
$userSel->loadRefs();

if (!$userSel->isPraticien()) {
  $userSel->load(null);
}

// Utilisateurs modifiables
$users = new CMediusers;
$users = $users->loadPraticiens(PERM_EDIT);

// Filtres sur la liste des packs
$where["chir_id"] = $userSel->user_id ? 
  "= '$userSel->user_id'" : 
  db_prepare_in(array_keys($users));

$packs = new CPack();
$packs = $packs->loadList($where);
foreach($packs as $key => $value) {
  $packs[$key]->loadRefsFwd();
}

// R�cup�ration des mod�les
$whereCommon = array();
$whereCommon[] = "nom = 'Hospitalisation' OR nom = 'operation'";

// Mod�les de l'utilisateur
$listModelePrat = array();
if ($userSel->user_id) {
  $where = array();
  $where["chir_id"] = db_prepare("= %", $userSel->user_id);
  $listModelePrat = CCompteRendu::loadModeleByCat($whereCommon, $where);
}

// Mod�les de la fonction
$listModeleFunc = array();
if ($userSel->user_id) {
  $where = array();
  $where["function_id"] = db_prepare("= %", $userSel->function_id);
  $listModeleFunc = CCompteRendu::loadModeleByCat($whereCommon, $where);
}

// pack s�lectionn�
$pack_id = mbGetValueFromGetOrSession("pack_id");
$pack = new CPack();
$pack->load($pack_id);
if($pack_id) {
  $pack->loadRefsFwd();
} else {
  $pack->chir_id = $userSel->user_id;
}

// Cr�ation du template
$smarty = new CSmartyDP(1);

$smarty->assign("users"         , $users);
$smarty->assign("user_id"       , $user_id);
$smarty->assign("listModelePrat", $listModelePrat);
$smarty->assign("listModeleFunc", $listModeleFunc);
$smarty->assign("packs"         , $packs);
$smarty->assign("pack"          , $pack);

$smarty->display("vw_idx_packs.tpl");

?>