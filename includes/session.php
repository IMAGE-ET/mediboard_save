<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage includes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
// Load AppUI from session
global $rootName;

// Manage the session variable(s)
session_name(preg_replace("/[^a-z0-9]/i", "", $rootName));

if (get_cfg_var("session.auto_start") > 0) {
  session_write_close();
}

session_start();
  
// Check if session has previously been initialised
if(empty($_SESSION["AppUI"]) || isset($_GET["logout"])) {
  $_SESSION["AppUI"] = CAppUI::init();
}

CAppUI::$instance =& $_SESSION["AppUI"];

if (!isset($_SESSION["locked"])) $_SESSION["locked"] = false; 

if (!isset($_SESSION['browser'])) {
  /** Basic browser detection */ 
  $browser = array(
    'version'   => '0.0.0',
    'majorver'  => 0,
    'minorver'  => 0,
    'build'     => 0,
    'name'      => 'unknown',
    'mobile'    => false,
    'deprecated'=> false,
    'useragent' => '',
  );
  
  $browsers = array(
    'firefox', 'msie', 'opera', 'chrome', 'safari', 'mozilla', 'seamonkey', 'konqueror', 'netscape',
    'gecko', 'navigator', 'mosaic', 'lynx', 'amaya', 'omniweb', 'avant', 'camino', 'flock', 'aol'
  );
  
  $minimal_versions = CAppUI::conf("browser_compat");
  
  if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $browser['useragent'] = $_SERVER['HTTP_USER_AGENT'];
    $user_agent = strtolower($browser['useragent']);
    foreach($browsers as $_browser) {
      if (preg_match("/($_browser)[\/ ]?([0-9.]*)/", $user_agent, $match)) {
        $browser['name'] = $match[1];
        $browser['version'] = $match[2];
        @list($browser['majorver'], $browser['minorver'], $browser['build']) = explode('.', $browser['version']);
        break;
      }
    }
    
		$ipad = preg_match("/ipad/i", $user_agent);
		if ($ipad) {
			$browser['name'] = 'ipad';
		}
		
    $browser['mobile'] = !$ipad && preg_match("/mobi|phone|symbian/i", $user_agent);
  }
  
  $browser['deprecated'] = isset($minimal_versions[$browser['name']]) && 
                             version_compare($browser['version'],  $minimal_versions[$browser['name']], "<") && 
                             !$browser['mobile'];
  
  $_SESSION['browser'] =& $browser; 
}
else $browser =& $_SESSION['browser']; 
