<?php /* $Id$ */

/**
* @package Mediboard
* @version $Revision$
* @author Thomas Despoix
*/

include_once("./includes/magic_quotes_gpc.php");

$dPconfig = array();
global $performance;
$performance = array();

if (!is_file("./includes/config.php")) {
  header("location: install/");
  die("Redirection vers l'assistant d'installation");
}

// PHP Configuration
ini_set("memory_limit", "64M");
if(function_exists("date_default_timezone_set")) {
  date_default_timezone_set("Europe/Paris");
}

require_once("./includes/config_dist.php");
require_once("./includes/config.php");
require_once("./includes/version.php");
require_once("./classes/sharedmemory.class.php");

if($dPconfig["offline"]) {
  header("location: offline.php");
  die("Le syst�me est actuellement en cours de maintenance");
}

// Check that the user has correctly set the root directory
is_file($dPconfig["root_dir"]."/includes/config.php") 
  or die("ERREUR FATALE: le repertoire racine est probablement mal configur�");

require_once("./includes/mb_functions.php");
require_once("./includes/compat.php");
require_once("./includes/errors.php");

// Start chrono
require_once("./classes/chrono.class.php");
$phpChrono = new Chronometer;
$phpChrono->start();

// Load AppUI from session
require_once("./classes/ui.class.php");
require_once("./includes/session.php");

// Write the HTML headers
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");  // always modified
header("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header("Pragma: no-cache");  // HTTP/1.0

 
//require_once("./includes/db_connect.php");

require_once("./classes/sqlDataSource.class.php");
require_once("./classes/mysqlDataSource.class.php");  	



require_once("./includes/autoload.php");

// Load default preferences if not logged in
if (!$AppUI->user_id) {
  $AppUI->loadPrefs(0);
}

// Don't output anything. Usefull for fileviewers, popup dialogs, ajax requests, etc.
$suppressHeaders = dPgetParam($_REQUEST, "suppressHeaders");

// Output the charset header in case of an ajax request
$ajax = dPgetParam($_REQUEST, "ajax", false);

// Check if we are in the dialog mode
$dialog = dPgetParam($_REQUEST, "dialog");

// check if the user is trying to log in
if (isset($_POST["login"])) {
  $redirect = dPgetParam($_REQUEST, "redirect", "");
  $ok = $AppUI->login();
  if(!$ok) {
    @include_once("./locales/core.php");
    $AppUI->setMsg("Login Failed", UI_MSG_ERROR, true);
  }
  if ($ok && $dialog) {
    $redirect = "m=system&a=login_ok&dialog=1";
  }
  $AppUI->redirect($redirect);
}

// Get the user preference
$uistyle = $AppUI->user_prefs["UISTYLE"];

require_once( $AppUI->getSystemClass("smartydp"));

// clear out main url parameters
$m = "";
$a = "";
$u = "";
$g = "";

// load locale settings
$AppUI->setUserLocale();
require_once("./locales/core.php");
//setlocale(LC_TIME, $AppUI->user_locale);
setlocale(LC_TIME, "fr_FR");

if (!$suppressHeaders || $ajax) {
  // output the character set header
  if (isset($locale_char_set)) {
    header("Content-type: text/html;charset=$locale_char_set");
  }
}

// check if we are logged in
if (!$AppUI->user_id) {
  
  $redirect = mbGetValueFromGet("logout") ?  "" : @$_SERVER["QUERY_STRING"]; 
  
  // Ajax login alert
  if ($ajax) {
    // Creation du Template
    $tplAjax = new CSmartyDP("modules/system");
    $tplAjax->assign("performance", $performance);
    $tplAjax->display("ajax_errors.tpl");

  } else {
    $smartyLogin = new CSmartyDP("style/$uistyle");
    $smartyLogin->assign("localeCharSet"        , $locale_char_set);
    $smartyLogin->assign("mediboardShortIcon"   , mbLinkShortcutIcon("style/$uistyle/images/icons/favicon.ico",1));
    $smartyLogin->assign("mediboardCommonStyle" , mbLinkStyleSheet("style/mediboard/main.css", "all",1));
    $smartyLogin->assign("mediboardStyle"       , mbLinkStyleSheet("style/$uistyle/main.css", "all",1));
    $smartyLogin->assign("mediboardScript"      , mbLoadScripts(1));
    $smartyLogin->assign("demoVersion"          , $dPconfig["demo_version"]);
    $smartyLogin->assign("errorMessage"         , $AppUI->getMsg());
    $smartyLogin->assign("time"                 , time());
    $smartyLogin->assign("redirect"             , $redirect);
    $smartyLogin->assign("uistyle"              , $uistyle);
    $smartyLogin->assign("offline"              , false);
    $smartyLogin->display("login.tpl");
  }
  
  // Destroy the current session and output login page
  session_unset();
  session_destroy();
  exit;
}

// Set the module and action from the url
if (null == $m = $AppUI->checkFileName(mbGetValueFromGet("m", 0))) {
  $m = CPermModule::getFirstVisibleModule();
  if ($pref_module = $AppUI->user_prefs["DEFMODULE"]) {
    if (CPermModule::getViewModule(CModule::getInstalled($pref_module)->mod_id, PERM_READ)) {
      $m = $pref_module;
    }
  }
}

// Still no target module
if (null == $m) {
  $AppUI->redirect("m=system&a=access_denied");
}

if (null == $currentModule = CModule::getInstalled($m)) {
  $AppUI->redirect("m=system&a=access_denied");
}

// Get current module permissions
// these can be further modified by the included action files
$can = $currentModule->canDo();

$a     = $AppUI->checkFileName(mbGetValueFromGet("a"     , "index"));
$u     = $AppUI->checkFileName(mbGetValueFromGet("u"     , ""));
$dosql = $AppUI->checkFileName(mbGetValueFromPost("dosql", ""));

// set the group in use, put the user group if not allowed
$g = mbGetAbsValueFromGetOrSession("g", $AppUI->user_group);
$indexGroup = new CGroups;
$indexGroup->load($g);
if (!$indexGroup->canRead()) {
  mbSetAbsValueToSession("g", $AppUI->user_group);
  $g = $AppUI->user_group;
}

// do some db work if dosql is set
if ($dosql) {
  $mDo = mbGetValueFromPost("m", $m);
  require("./modules/$mDo/$dosql.php");
}

ob_start();

// Feed modules with tabs
foreach (CModule::getActive() as $module) {
  require_once "./modules/$module->mod_name/index.php";
}

if (!$suppressHeaders) {
  
  // -- Code pour le HEADER --
  
  // D�finition du CharSet
  if (!isset($locale_char_set)){
    $locale_char_set = "UTF-8";
  }
  //Liste des Etablissements
  $etablissements = CMediusers::loadEtablissements(PERM_EDIT);
  // Liste des Modules
  if (!$dialog) {
    //top navigation menu
    $iKey = 0;
    $affModule = array();
    foreach (CPermModule::getVisibleModules() as $module) {
      $affModule[$iKey]["modName"]      = "$module->mod_name";
      $affModule[$iKey]["modNameCourt"] = $AppUI->_("module-$module->mod_name-court");
      $affModule[$iKey]["modNameLong"]  = $AppUI->_("module-$module->mod_name-long");
      $iKey++;
    }  
  }
  // Message
  $messages = new CMessage();
  $messages = $messages->loadPublications("present");

  foreach($messages as $message_id => $curr_message) {
  	if ($curr_message->module_id) {
  		$curr_message->loadRefsFwd();

  		if ($curr_message->_ref_module->mod_name != $m) {
  			unset($messages[$message_id]);
  		}
  	}
  }

  
  
  // Creation du Template
  $smartyHeader = new CSmartyDP();
  $smartyHeader->template_dir = "style/$uistyle/templates/";
  $smartyHeader->compile_dir  = "style/$uistyle/templates_c/";
  $smartyHeader->config_dir   = "style/$uistyle/configs/";
  $smartyHeader->cache_dir    = "style/$uistyle/cache/";
  if (!$dialog) {
    $smartyHeader->assign("affModule" , $affModule);
  }
  $smartyHeader->assign("offline"              , false);
  $smartyHeader->assign("configOffline"        , null);
  $smartyHeader->assign("localeCharSet"        , $locale_char_set);
  $smartyHeader->assign("mediboardShortIcon"   , mbLinkShortcutIcon("style/$uistyle/images/icons/favicon.ico",1));
  $smartyHeader->assign("mediboardCommonStyle" , mbLinkStyleSheet("style/mediboard/main.css", "all",1));
  $smartyHeader->assign("mediboardStyle"       , mbLinkStyleSheet("style/$uistyle/main.css", "all",1));
  $smartyHeader->assign("mediboardScript"      , mbLoadScripts(1));
  $smartyHeader->assign("dialog"               , $dialog);
  $smartyHeader->assign("messages"             , $messages);
  $smartyHeader->assign("uistyle"              , $uistyle);
  $smartyHeader->assign("errorMessage"         , $AppUI->getMsg());
  $smartyHeader->assign("Etablissements"       , $etablissements);
  $smartyHeader->assign("portal"               , array (
    "help" => mbPortalURL($m),
    "tracker" => mbPortalURL("tracker"),
  ));
  $smartyHeader->assign("on_load_events"        , $AppUI->on_load_events);
  $smartyHeader->display("header.tpl");
}

// -- Code pour les tabBox et Inclusion du fichier demand� --

$tab = $a == "index"  ? 
  mbGetValueFromGetOrSession("tab", 1) : 
  mbGetValueFromGet("tab");

if ($tab !== null) {
  $currentModule->showTabs();
} else {
  $currentModule->showAction();
}

$phpChrono->stop();

// Calcul des performances
if( !function_exists("memory_get_usage") ) {
  function memory_get_usage() {
    return "-";
  }
}

$performance["genere"]  = number_format($phpChrono->total, 3);
$performance["memoire"] = mbConvertDecaBinary(memory_get_usage());
$performance["objets"]  = CMbObject::$objectCount;
$performance["cache"]   = CMbObject::$cacheCount;
$performance["size"]    = mbConvertDecaBinary(ob_get_length());
  
// Inclusion du footer
if (!$suppressHeaders) {
  
  // Creation du Template
  $smartyFooter = new CSmartyDP("style/$uistyle");
  $smartyFooter->assign("offline"       , false);
  $smartyFooter->assign("debugMode"     , @$AppUI->user_prefs["INFOSYSTEM"]);
  $smartyFooter->assign("performance"   , $performance);
  $smartyFooter->assign("userIP"        , $_SERVER["REMOTE_ADDR"]);
  $smartyFooter->assign("errorMessage"  , $AppUI->getMsg());
  $smartyFooter->assign("demoVersion"   , $dPconfig["demo_version"]);
  $smartyFooter->display("footer.tpl");
}

// Ajax performance
if ($ajax) {
  // Creation du Template
  $tplAjax = new CSmartyDP("modules/system");
  $tplAjax->assign("performance", $performance);
  $tplAjax->display("ajax_errors.tpl");
}

ob_end_flush();
require "./includes/access_log.php";

?>
