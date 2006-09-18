<?php /* $Id: vw_idx_patients.php 783 2006-09-14 12:44:01Z rhum1 $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 783 $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect("m=system&a=access_denied");
}

$user_id = mbGetValueFromGetOrSession("user_id", $AppUI->user_id);

if(!$user_id) {
  $AppUI->setMsg("Vous devez selectionner un utilisateur");
  $AppUI->redirect("m=$m&tab=vw_edit_users");
}

// R�cuperation de l'utilisateur s�lectionn�
$user = new CUser;
$user->load($user_id);


$where = array();
$where["user_id"] = "= '$user->user_id'";

$order = "mod_id";
$permModule = new CPermModule;
$listPermsModules = $permModule->loadList($where, $order);
foreach($listPermsModules as $keyMod => $mod) {
  $listPermsModules[$keyMod]->loadRefDBModule();
}

$order = "object_class";
$permObject = new CPermObject;
$listPermsObjects = $permObject->loadList($where, $order);
foreach($listPermsObjects as $keyObj => $obj) {
  $listPermsObjects[$keyObj]->loadRefDBObject();
}

$permission = array(PERM_DENY => "interdit",
                     PERM_READ => "lecture",
                     PERM_EDIT => "ecriture");

$visibility = array(PERM_DENY => "cach�",
                    PERM_READ => "vue",
                    PERM_EDIT => "administration");

$modulesInstalled = CModule::getInstalled();

$listClasses = getChildClasses("CMbObject", null);

// Cr�ation du template
$smarty = new CSmartyDP(1);

$smarty->assign("user"            , $user            );
$smarty->assign("modulesInstalled", $modulesInstalled);
$smarty->assign("listClasses"     , $listClasses     );
$smarty->assign("listPermsModules", $listPermsModules);
$smarty->assign("listPermsObjects", $listPermsObjects);
$smarty->assign("permission"      , $permission      );
$smarty->assign("visibility"      , $visibility      );

$smarty->display("edit_perms.tpl");

?>