<?php /** $Id$ **/

/**
 * @package Mediboard
 * @subpackage admin
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$user_id = CCanDo::edit() ? CValue::getOrSession("user_id", "default") : null;
$user =  CUser::get($user_id);
$prof = $user->profile_id ? CUser::get($user->profile_id) : new CUser;

if ($user_id == "default") {
  $user->_id = null;
}

$prefvalues = array(
  "default"  => CPreferences::get(),
  "template" => $user->profile_id ? CPreferences::get($user->profile_id) : array(),
  "user"     => $user->_id !== "" ? CPreferences::get($user->_id       ) : array(),
);

// common sera toujours au debut
$prefs = array(
  "common" => array()
);

// Classement par module et par pr�f�rences
CPreferences::loadModules();
foreach (CPreferences::$modules as $modname => $prefnames) {
  $module  = CModule::getActive($modname);
  $canRead = $module ? CPermModule::getPermModule($module->_id, PERM_READ, $user_id) : false;
  
  if ($modname == "common" || $user_id == "default" || $canRead) {
    $prefs[$modname] = array();
    foreach ($prefnames as $prefname) {
      $prefs[$modname][$prefname] = array(
        "default"  => CMbArray::extract($prefvalues["default" ], $prefname),
        "template" => CMbArray::extract($prefvalues["template"], $prefname),
        "user"     => CMbArray::extract($prefvalues["user"    ], $prefname),
      );
    }
  }
}

// Warning: user clone necessary!
// Some module index change $user global
$user_clone = $user;
// Chargement des modules
$modules = CPermModule::getVisibleModules();
foreach ($modules as $module) {
  // Module might not be present
  @include "./modules/$module->mod_name/index.php";
}
$user = $user_clone;

// Locales and styles
$locales = CAppUI::readDirs("locales");
$styles  = CAppUI::readDirs("style");

// Get session lifetime in php.ini
$gc_maxlifetime = ini_get("session.gc_maxlifetime");
$session_lifetime = false;
if (!empty($gc_maxlifetime)) {
  $session_lifetime = (int) ($gc_maxlifetime / 60);
}

$session_lifetime_values = array("", 5, 10, 15, 20, 25, 30, 45, 60, 120, 180, 240, 300);
$session_lifetime_enum = implode("|", $session_lifetime_values);
if ($session_lifetime) {
  $session_lifetime_enum = array();

  foreach ($session_lifetime_values as $_enum) {
    if ($_enum <= $session_lifetime) {
      $session_lifetime_enum[] = $_enum;
    }
  }

  if (!empty($session_lifetime_enum)) {
    $session_lifetime_enum = implode("|", $session_lifetime_enum);
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("user"                 , $user);
$smarty->assign("prof"                 , $prof);
$smarty->assign("user_id"              , $user_id);
$smarty->assign("locales"              , $locales);
$smarty->assign("styles"               , $styles);
$smarty->assign("modules"              , $modules);
$smarty->assign("prefs"                , $prefs);
$smarty->assign("session_lifetime_enum", $session_lifetime_enum);

$smarty->display("edit_prefs.tpl");