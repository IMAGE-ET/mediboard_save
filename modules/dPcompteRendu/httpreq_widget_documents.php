<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $can, $AppUI;  
$can->needsEdit();

$object_class = CValue::getOrSession("object_class");
$object_id    = CValue::getOrSession("object_id");
$user_id      = CValue::getOrSession("praticien_id");

// Chargement de l'objet cible
$object = new $object_class;
if (!$object instanceof CMbObject) {
	trigger_error("object_class should be an CMbObject", E_USER_WARNING);
	return;
}

$object->load($object_id);
if (!$object->_id) {
	trigger_error("object of class '$object_class' could not be loaded with id '$object_id'", E_USER_WARNING);
	return;
}

// Praticien concern�
if ($AppUI->_ref_user->isPraticien()) {
  $user = $AppUI->_ref_user;
}
else {
	$user = new CMediusers();
	$user->load($user_id);
}

$user->loadRefFunction();
$user->_ref_function->loadRefGroup();
$user->canDo();

$object->loadRefsDocs();

// Mod�les du praticien
$modelesByOwner = array();
$packsByOwner = array();
if ($user->_can->edit) {
  $modelesByOwner = CCompteRendu::loadAllModelesFor($user->_id, 'prat', $object_class, "body");
  $packsByOwner = CPack::loadAllPacksFor($user->_id, 'user', $object_class);
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("modelesByOwner", $modelesByOwner);
$smarty->assign("packsByOwner"  , $packsByOwner);
$smarty->assign("praticien"     , $user);
$smarty->assign("object"        , $object);
$smarty->assign("mode"          , CValue::get("mode"));
$smarty->assign("notext"        , "notext");
$smarty->display("inc_widget_documents.tpl");

?>