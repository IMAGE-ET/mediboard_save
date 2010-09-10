<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Alexis Granger
* @abstract Permet de choisir des mod�les pour constituer des packs
*/

CCanDo::checkRead();

// Chargement du user
$user = new CMediusers;
$user->load(CValue::get("user_id", CAppUI::$user->_id));
$user->loadRefs();

// Chargement du pack
$pack = new CPack();
if ($pack->load(CValue::getOrSession("pack_id"))) {
  $pack->loadRefsFwd();
} else {
  $pack->chir_id = $user->user_id;
}
// Mod�les de l'utilisateur
$filter_class = CValue::get("filter_class", '');

$modeles = CCompteRendu::loadAllModelesFor($user->_id, 'prat', $filter_class);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("modeles", $modeles);
$smarty->assign("pack"   , $pack   );
$smarty->assign("user_id", $user->_id);
$smarty->assign("filter_class", $filter_class);

$smarty->display("inc_list_modeles.tpl");
