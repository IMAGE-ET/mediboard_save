<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

CCanDo::checkEdit();

$user_id = CValue::getOrSession("user_id", $AppUI->user_id);

// R�cuperation de l'utilisateur s�lectionn�
$user = new CUser;
$user->load($user_id);
$user->loadRefMediuser();
$user->isLDAPLinked();

// R�cuperation des utilisateurs recherch�s
$user_last_name  = CValue::getOrSession("user_last_name" , "");
$user_first_name = CValue::getOrSession("user_first_name", "");
$user_username   = CValue::getOrSession("user_username"  , "");
$user_type       = CValue::getOrSession("user_type"      , 0);
$template        = CValue::getOrSession("template"       , "");

$where = null;
if ($user_last_name ) $where["user_last_name"]  = "LIKE '".addslashes($user_last_name )."%'";
if ($user_first_name) $where["user_first_name"] = "LIKE '".addslashes($user_first_name)."%'";
if ($user_username  ) $where["user_username"]   = "LIKE '".addslashes($user_username)."%'";
if ($user_type      ) $where["user_type"]       = "= '".addslashes($user_type)."'";
if ($template != null)$where["template"]        = "= '".addslashes($template)."'";

$users = null;
if ($where) {
  $users = $user->loadList($where, "user_type, user_last_name, user_first_name, template", "0, 100");
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("template"       , $template       );
$smarty->assign("user_last_name" , $user_last_name );
$smarty->assign("user_first_name", $user_first_name);
$smarty->assign("user_username"  , $user_username  );
$smarty->assign("user_type"      , $user_type      );
$smarty->assign("utypes"         , CUser::$types   );
$smarty->assign("users"          , $users          );
$smarty->assign("user"           , $user           );
$smarty->assign("specs"          , $user->getProps());

$smarty->display("vw_edit_users.tpl");
?>