<?php 
/**
 * Main URL Dispatcher 
 *
 * PHP version 5.1.x+
 *  
 * @category Dispatcher
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id$ 
 * @link     http://www.mediboard.org
 */

require "./includes/compat.php";
require "./includes/magic_quotes_gpc.php";

/* 
 * The order of the keys is important (only the first keys 
 * are displayed in the short view of the Firebug console).
 */
$performance = array(
  // Performance
  "genere" => null,
  "memoire" => null,
  "size" => null,
  "objets" => 0,
  
  // Errors
  "error" => 0,
  "warning" => 0,
  "notice" => 0,
  
  // Cache
  "cachableCount" => null,
  "cachableCounts" => null,
);

if (!is_file("./includes/config.php")) {
  header("Location: install/");
  die("Redirection vers l'assistant d'installation");
}

require "./includes/config_dist.php";
require "./includes/config.php";

$rootName = basename($dPconfig["root_dir"]);

require "./includes/version.php";
require "./classes/sharedmemory.class.php";

// PHP Configuration
foreach ($dPconfig["php"] as $key => $value) {
  if ($value !== "") {
    ini_set($key, $value);
  }
}

if ($dPconfig["offline"]) {
  header("Location: offline.php");
  die("Le syst�me est actuellement en cours de maintenance");
}

if ($dPconfig["migration"]["active"]) {
  header("Location: migration.php");
  exit;
}

// Check that the user has correctly set the root directory
if (!is_file($dPconfig["root_dir"]."/includes/config.php")) {
  die("ERREUR FATALE: Le r�pertoire racine est probablement mal configur�");
}

require "./includes/mb_functions.php";
require "./includes/errors.php";

date_default_timezone_set($dPconfig["timezone"]);

// Start chrono
require "./classes/chrono.class.php";
$phpChrono = new Chronometer;
$phpChrono->start();

// Load AppUI from session
require "./classes/app.class.php";
require "./classes/ui.class.php";
require "./includes/session.php";

// Register shutdown
register_shutdown_function(array("CApp", "checkPeace"));

require "./classes/sqlDataSource.class.php";
require "./classes/mysqlDataSource.class.php";

if (!CSQLDataSource::get("std")) {
  header("Location: offline.php?reason=bdd");
  die("La base de donn�es n'est pas connect�e");
}

// Write the HTML headers
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");  // Date in the past
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");  // always modified
header("Cache-Control: no-cache, no-store, must-revalidate");  // HTTP/1.1
header("Pragma: no-cache");  // HTTP/1.0
header("X-UA-Compatible: IE=8"); // Force IE8 mode
//header("X-UA-Compatible: IE=8;chrome=1"); // To use the ChromeFrame plugin in IE

require "./includes/autoload.php";

// Load default preferences if not logged in
if (!CAppUI::$instance->user_id) {
  CAppUI::loadPrefs();
}

// Don't output anything. Usefull for fileviewers, popup dialogs, ajax requests, etc.
$wsdl = CValue::request("wsdl");
if (isset($wsdl)) {
  $wsdl = 1;
}

$suppressHeaders = CValue::request("suppressHeaders", $wsdl);

// Output the charset header in case of an ajax request
$ajax = CValue::request("ajax", false);

// Check if we are in the dialog mode
$dialog = CValue::request("dialog");

// check if the user is trying to log in
if (isset($_REQUEST["login"])) {
  include "./locales/core.php";
  if (null == $ok = CAppUI::login()) {
    CAppUI::setMsg("Auth-failed", UI_MSG_ERROR);
  }

  if (isset($_SESSION['browser']['deprecated']) 
      && $_SESSION['browser']['deprecated'] 
      && !CValue::get("password")
  ) { // If we are not connecting directly
    $tpl = new CSmartyDP("style/mediboard");
    $tpl->display("old_browser.tpl");
    CApp::rip();
  }
  
  $redirect = CValue::request("redirect");
  parse_str($redirect, $parsed_redirect);
  if ($ok && $dialog && isset($parsed_redirect["login_info"])) {
    $redirect = "m=system&a=login_ok&dialog=1";
  }

  if ($redirect) {
    CAppUI::redirect($redirect);
  }
  
  // Empty post data only if we login by POST (with the login page)
  if (isset($_POST["login"])) {
    CApp::emptyPostData();
  }
}

// clear out main url parameters
$m = $a = $u = $g = "";

// load locale settings
require "./locales/core.php";

if (empty($locale_info["names"])) {
  $locale_info["names"] = array();
}
setlocale(LC_TIME, $locale_info["names"]);

if (empty($locale_info["charset"])) {
  $locale_info["charset"] = "UTF-8";
}

CApp::$encoding = $locale_info["charset"];
// We don't use mb_internal_encoding as it may be redefined by libs

// output the character set header
if (!$suppressHeaders || $ajax) {
  header("Content-type: text/html;charset=".CApp::$encoding);
}

// Show errors to admin
ini_set("display_errors", CAppUI::pref("INFOSYSTEM"));

$user = new CMediusers();
if ($user->isInstalled()) {
  $user->load(CAppUI::$instance->user_id);
  $user->getBasicInfo();
  CAppUI::$user = $user;
  CAppUI::$instance->_ref_user =& CAppUI::$user;
}

CAppUI::requireSystemClass("smartydp");

ob_start();

// We check if the mobile feature is available and if the user agent is a mobile
if (is_file("./mobile/main.php") 
    && isset($_SESSION['browser']['mobile']) 
    && $_SESSION['browser']['mobile']
) {
  include "./mobile/main.php";
}
else {
  include "./includes/main.php";
}

require "./includes/access_log.php";

if (CValue::get("_aio")) {
  echo CHTMLResourceLoader::allInOne(ob_get_clean());
} 
else {
  ob_end_flush();
}

CApp::rip();
