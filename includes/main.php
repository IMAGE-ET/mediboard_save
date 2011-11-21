<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage mediboard
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$debug = CAppUI::pref("INFOSYSTEM");

// Http Redirections
if (CAppUI::conf("http_redirections")) {
  if (!CAppUI::$instance->user_id || CValue::get("login")) {
    $redirection = new CHttpRedirection();
    $redirections = $redirection->loadList(null, "priority DESC");
    $passThrough = false;
    foreach($redirections as $_redirect) {
      if (!$passThrough) {
        $passThrough = $_redirect->applyRedirection();
      }
    }
  }  
}

// Get the user's style
$uistyle = CAppUI::pref("UISTYLE");
if (!file_exists("style/$uistyle/templates/header.tpl")) {
  $uistyle = "mediboard";
}

CJSLoader::$files = array(
  CJSLoader::getLocaleFile(),
  "includes/javascript/printf.js",
  "includes/javascript/stacktrace.js",
  //"lib/dshistory/dshistory.js",
  
  "lib/scriptaculous/lib/prototype.js",
  "lib/scriptaculous/src/scriptaculous.js",
  
  //"lib/nwmatcher/nwmatcher.js",
  //"lib/nwmatcher/adapter.js",
 
  "includes/javascript/console.js",

  // We force the download of the dependencies 
  "lib/scriptaculous/src/builder.js",
  "lib/scriptaculous/src/effects.js",
  "lib/scriptaculous/src/dragdrop.js",
  "lib/scriptaculous/src/controls.js",
  "lib/scriptaculous/src/slider.js",
  "lib/scriptaculous/src/sound.js",

  "includes/javascript/prototypex.js",

  // Datepicker
  "includes/javascript/date.js",
  "lib/datepicker/datepicker.js",
  "lib/datepicker/datepicker-locale-fr_FR.js",

  // Livepipe UI
  "lib/livepipe/livepipe.js",
  "lib/livepipe/tabs.js",
  "lib/livepipe/window.js",
  
  // Growler
  //"lib/growler/build/Growler-compressed.js",
	
	// TreeView
  "includes/javascript/treeview.js",

  // Flotr
  "lib/flotr/flotr.js",
  "lib/flotr/lib/excanvas.js",
  "lib/flotr/lib/base64.js",
  "lib/flotr/lib/canvas2image.js",
  "lib/flotr/lib/canvastext.js",
  
  // JS Expression eval
  "lib/jsExpressionEval/parser.js",

  "includes/javascript/common.js",
  "includes/javascript/functions.js",
  "includes/javascript/tooltip.js",
  "includes/javascript/controls.js",
  "includes/javascript/cookies.js",
  "includes/javascript/url.js",
  "includes/javascript/forms.js",
  "includes/javascript/checkForms.js",
  "includes/javascript/aideSaisie.js",
  "includes/javascript/exObject.js",
  "includes/javascript/tag.js",
  "includes/javascript/mbObject.js",
	"includes/javascript/browserDetect.js",

  "includes/javascript/mbmail.js",
);

// check if we are logged in
if (!CAppUI::$instance->user_id) {
  $redirect = CValue::get("logout") ?  "" : CValue::read($_SERVER, "QUERY_STRING"); 
  $_SESSION["locked"] = null;
  
  // HTTP 403 header
  //header('HTTP/1.0 403 Forbidden');  
  
  // Ajax login alert
  if ($ajax) {
    $tplAjax = new CSmartyDP("modules/system");
    $tplAjax->assign("performance", CApp::$performance);
    $tplAjax->display("ajax_errors.tpl");
  }
  else {
    $tplLogin = new CSmartyDP("style/$uistyle");
    $tplLogin->assign("localeInfo"           , $locale_info);
    $tplLogin->assign("mediboardShortIcon"   , CFaviconLoader::loadFile("style/$uistyle/images/icons/favicon.ico"));
    $tplLogin->assign("mediboardCommonStyle" , CCSSLoader::loadFiles());
    $tplLogin->assign("mediboardStyle"       , CCSSLoader::loadFiles($uistyle));
    $tplLogin->assign("mediboardScript"      , CJSLoader::loadFiles(!$debug));
    $tplLogin->assign("errorMessage"         , CAppUI::getMsg());
    $tplLogin->assign("time"                 , time());
    $tplLogin->assign("redirect"             , $redirect);
    $tplLogin->assign("uistyle"              , $uistyle);
    $tplLogin->assign("browser"              , $browser);
    $tplLogin->assign("nodebug"              , true);
    $tplLogin->assign("offline"              , false);
    $tplLogin->assign("allInOne"             , CValue::get("_aio"));
    $tplLogin->display("login.tpl");
  }
  
  // Destroy the current session and output login page
  session_unset();
  @session_destroy(); // Escaped because of an unknown error
  CApp::rip();
}

$tab = 1;
// Set the module and action from the url
if (null == $m = CAppUI::checkFileName(CValue::get("m", 0))) {
  $m = CPermModule::getFirstVisibleModule();
  $parts = explode("-", CAppUI::pref("DEFMODULE"), 2);
  
  $pref_module = $parts[0];
  if ($pref_module && CPermModule::getViewModule(CModule::getInstalled($pref_module)->mod_id, PERM_READ)) {
    $m = $pref_module;
  }
  
  if (count($parts) == 2) {
    $tab = $parts[1];
    CValue::setSession("tab", $tab);
  }
}

// Still no target module
if (null == $m) {
  CAppUI::redirect("m=system&a=access_denied");
}

if (null == $module = CModule::getInstalled($m)) {
  // dP remover super hack
  if (null == $module = CModule::getInstalled("dP$m")) {
    CAppUI::redirect("m=system&a=module_missing&mod=$m");
  }
  $m = "dP$m";
}

// Get current module permissions
// these can be further modified by the included action files
$can = $module->canDo();

$a      = CAppUI::checkFileName(CValue::get("a"     , "index"));
$u      = CAppUI::checkFileName(CValue::get("u"     , ""));
$dosql  = CAppUI::checkFileName(CValue::post("dosql", ""));
$m_post = CAppUI::checkFileName(CValue::post("m", $m));
$class  = CAppUI::checkFileName(CValue::post("@class", ""));

$tab = $a == "index" ? 
  CValue::getOrSession("tab", $tab) : 
  CValue::get("tab");

// Check whether the password is strong enough
if (CAppUI::$instance->weak_password && 
    !CAppUI::$instance->user_remote && 
    !($m == "admin" && $tab == "chpwd")) {
  CAppUI::redirect("m=admin&tab=chpwd&forceChange=1");
}

// set the group in use, put the user group if not allowed
$g = CValue::getOrSessionAbs("g", CAppUI::$instance->user_group);
$indexGroup = new CGroups;
if ($indexGroup->load($g) && !$indexGroup->canRead()) {
  $g = CAppUI::$instance->user_group;
  CValue::setSessionAbs("g", $g);
}

// do some db work if dosql is set
if ($dosql) {
  // controller in controllers/ directory
  if (is_file("./modules/$m_post/controllers/$dosql.php")) {
    require("./modules/$m_post/controllers/$dosql.php");
  } 
  // otherwise... @FIXME to be removed
  else {
    require("./modules/$m_post/$dosql.php");
  }
}

if ($class) {
  $do = new CDoObjectAddEdit($class);
  $do->doIt();
}

// Checks if the current module is obsolete
$obsolete_module = false;
$user = CAppUI::$instance->_ref_user;

// We check only when not in the "system" module, and not in an "action" (ajax, etc)
if($m && $m != "system" && !$a && (!$user->_id || $user->isAdmin())){
  $setupclass = "CSetup$m";
  $setup = new $setupclass;
  $module->compareToSetup($setup);
  $obsolete_module = $module->_upgradable;
}

// Feed module with tabs
include("./modules/{$module->mod_name}/index.php");
if ($tab !== null) {
  $module->addConfigureTab();
}
  
if (!$a || $a === "index")
  $tab = $module->getValidTab($tab);

if (!$suppressHeaders) {
  // Liste des Etablissements
  $etablissements = CMediusers::loadEtablissements(PERM_EDIT);

  // Messages
  $messages = new CMessage();
  $messages = $messages->loadPublications("present", $m, $g);
  
  // Mails
  $mail = new CMbMail();
  $mails = $mail->loadVisibleList();
  
  // Load the SVN latest update info
  $svnStatus = null;
  if (CAppUI::pref("showLastUpdate") && is_readable("./tmp/svnstatus.txt")) {
    $svnInfo = file("./tmp/svnstatus.txt");
    $svnStatus = array( 
      "revision" => explode(": ", $svnInfo[0]),
      "date"     => explode(": ", $svnInfo[1]),
    );

    $svnStatus["revision"] = $svnStatus["revision"][1];
    $svnStatus["date"]     = $svnStatus["date"][1];
    $svnStatus["relative"] = CMbDate::relative($svnStatus["date"]);
  }
  
  // Creation du Template
  $tplHeader = new CSmartyDP("style/$uistyle");
  
  $tplHeader->assign("offline"              , false);
  $tplHeader->assign("nodebug"              , true);
  $tplHeader->assign("obsolete_module"      , $obsolete_module);
  $tplHeader->assign("configOffline"        , null);
  $tplHeader->assign("localeInfo"           , $locale_info);
  $tplHeader->assign("mediboardShortIcon"   , CFaviconLoader::loadFile("style/$uistyle/images/icons/favicon.ico"));
  $tplHeader->assign("mediboardCommonStyle" , CCSSLoader::loadFiles());
  $tplHeader->assign("mediboardStyle"       , CCSSLoader::loadFiles($uistyle));
  $tplHeader->assign("mediboardScript"      , CJSLoader::loadFiles(!$debug));
  $tplHeader->assign("dialog"               , $dialog);
  $tplHeader->assign("messages"             , $messages);
  $tplHeader->assign("mails"                , $mails);
  $tplHeader->assign("uistyle"              , $uistyle);
  $tplHeader->assign("browser"              , $browser);
  $tplHeader->assign("errorMessage"         , CAppUI::getMsg());
  $tplHeader->assign("Etablissements"       , $etablissements);
  $tplHeader->assign("svnStatus"            , $svnStatus);
  $tplHeader->assign("allInOne"             , CValue::get("_aio"));
  $tplHeader->assign("portal"               , array (
    "help" => mbPortalURL($m, $tab),
    "tracker" => mbPortalURL("tracker"),
  ));
  
  $tplHeader->display("header.tpl");
}

// tabBox et inclusion du fichier demand�
if ($tab !== null) {
  $module->showTabs();
} else {
  $module->showAction();
}

$phpChrono->stop();

arsort(CMbObject::$cachableCounts);
arsort(CMbObject::$objectCounts);

CApp::$performance["genere"]         = number_format($phpChrono->total, 3);
CApp::$performance["memoire"]        = CHTMLResourceLoader::getOutputMemory();
CApp::$performance["objets"]         = CMbObject::$objectCount;
CApp::$performance["cachableCount"]  = array_sum(CMbObject::$cachableCounts);
CApp::$performance["cachableCounts"] = CMbObject::$cachableCounts;
CApp::$performance["objectCounts"]   = CMbObject::$objectCounts;
CApp::$performance["ip"]  = $_SERVER["SERVER_ADDR"];

CApp::$performance["size"] = CHTMLResourceLoader::getOutputLength();
CApp::$performance["ccam"] = array (
  "cacheCount" => class_exists('CCodeCCAM') ? CCodeCCAM::$cacheCount : 0,
  "useCount"   => class_exists('CCodeCCAM') ? CCodeCCAM::$useCount : 0
);

// Data sources performances
foreach (CSQLDataSource::$dataSources as $dsn => $ds) {
  if (!$ds) continue;
  
  $chrono = $ds->chrono;
  CApp::$performance["dataSources"][$dsn] = array(
    "count" => $chrono->nbSteps,
    "time" => $chrono->total,
  );
}

// Unlocalized strings
if (!$suppressHeaders || $ajax) {
  CAppUI::$unlocalized = array_map("utf8_encode", CAppUI::$unlocalized);
  $unloc = new CSmartyDP("modules/system");
  $unloc->display("inc_unlocalized_strings.tpl");
}

// Inclusion du footer
if (!$suppressHeaders) {
  //$address = get_remote_address();
  
  $tplFooter = new CSmartyDP("style/$uistyle");
  $tplFooter->assign("offline"       , false);
  $tplFooter->assign("debugMode"     , $debug);
  $tplFooter->assign("performance"   , CApp::$performance);
  //$tplFooter->assign("userIP"        , $address["client"]);
  $tplFooter->assign("errorMessage"  , CAppUI::getMsg());
  $tplFooter->display("footer.tpl");
}

// Ajax performance
if ($ajax) {
  $tplAjax = new CSmartyDP("modules/system");
  $tplAjax->assign("performance", CApp::$performance);
  $tplAjax->display("ajax_errors.tpl");
}
