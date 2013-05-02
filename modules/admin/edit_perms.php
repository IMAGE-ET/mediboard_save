<?php

/**
 * $Id$
 *
 * @category Admin
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkEdit();

$user = CUser::get(CValue::getOrSession("user_id"));

$user_id = CValue::getOrSession("user_id", $user->_id);

if (!$user_id) {
  CAppUI::setMsg("Vous devez s�lectionner un utilisateur");
  CAppUI::redirect("m=admin&tab=vw_edit_users");
}

$modulesInstalled = CModule::getInstalled();
$isAdminPermSet   = false;

$profile = new CUser();
if ($user->profile_id) {
  $where["user_id"] = "= '$user->profile_id'";
  $profile->loadObject($where);
}

$order = "mod_id";

//Droit de l'utilisateur sur les modules
$whereUser = array();
$whereUser["user_id"] = "= '$user->user_id'";

$whereProfil = array();
$whereProfil["user_id"] = "= '$user->profile_id'";

// DROITS SUR LES MODULES
$permModule = new CPermModule;
$permsModule = array();
$permsModuleCount = 0;

// Droit du profil sur les modules
foreach ($permModule->loadList($whereProfil, $order) as $_perm) {
  $permsModuleCount++;
  $_perm->_owner = "template";
  $_perm->loadRefDBModule();
  $permsModule[$_perm->mod_id]["profil"] = $_perm;
}

foreach ($permModule->loadList($whereUser, $order) as $_perm) {
  $permsModuleCount++;
  $_perm->_owner = "user";
  $module = $_perm->loadRefDBModule();
  if (!$module->_id) {
    $isAdminPermSet = true;
  }
  $permsModule[$module->_id]["user"] = $_perm;
  unset($modulesInstalled[$module->mod_name]);
}

// DROITS SUR LES OBJETS
$permObject = new CPermObject;
$permsObject = array();
$permsObjectCount = 0;
$order = "object_class, object_id";

// Droit sur le profil
foreach ($permObject->loadList($whereProfil, $order) as $_perm) {
  $permsObjectCount++;
  $_perm->_owner = "template";
  $object = $_perm->loadRefDBObject();
  $permsObject[$object->_class][$object->_id]["profil"] = $_perm;
}

// Droit sur l'utilisateur
foreach ($permObject->loadList($whereUser, $order) as $_perm) {
  $permsObjectCount++;
  $_perm->_owner = "user";
  $object = $_perm->loadRefDBObject();
  $permsObject[$object->_class][$object->_id]["user"] = $_perm;
}

// Chargement des utilisateurs du profil courant ou de celui de l'utilisateur
$profileUser = new CUser();
$profilesList = array();
if ($user->profile_id) {
  $profileUser->profile_id = $user->profile_id;
  $profilesList = $profileUser->loadMatchingList('user_last_name');
} 
if ($user->template) {
  $profileUser->profile_id = $user->_id;
  $profilesList = $profileUser->loadMatchingList('user_last_name');
}

$classes        = CApp::getInstalledClasses();
$module_classes = CApp::groupClassesByModule($classes);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("user"            , $user            );
$smarty->assign("modulesInstalled", $modulesInstalled);
$smarty->assign("isAdminPermSet"  , $isAdminPermSet  );
$smarty->assign("classes"         , $classes         );
$smarty->assign("module_classes"  , $module_classes  );
$smarty->assign("permsModule"     , $permsModule     );
$smarty->assign("permsObject"     , $permsObject     );
$smarty->assign("permModule"      , $permModule      );
$smarty->assign("permObject"      , $permObject      );
$smarty->assign("profile"         , $profile         );
$smarty->assign("profilesList"    , $profilesList    );

$smarty->display("edit_perms.tpl");
