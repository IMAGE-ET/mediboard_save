<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// This script has to be launched via installer
global $can;

// Only check permissions when connected to mediboard, not to the installer
if ($can) {
  $can->needsAdmin();
}

// Remove locales
foreach (glob("locales/*", GLOB_ONLYDIR) as $localeDir) {
  $localeName = basename($localeDir);
  $sharedName = "locales-$localeName";
  
  if (!SHM::get($sharedName)) {
    CAppUI::stepAjax("Locales-shm-none", UI_MSG_OK, $localeName);
    continue;
  }
  
  if (!SHM::rem($sharedName)) {
    CAppUI::stepAjax("Locales-shm-rem-ko", UI_MSG_ERROR, $localeName);
    continue;
  }
  
  CAppUI::stepAjax("Locales-shm-rem-ok", UI_MSG_OK, $localeName);
}

// Remove class paths
if (!SHM::get("class-paths")) {
  CAppUI::stepAjax("Classes-shm-none", UI_MSG_WARNING);
}
else {
	if (!SHM::rem("class-paths")) {
	  CAppUI::stepAjax("Classes-shm-rem-ko", UI_MSG_ERROR);
	}
	
	CAppUI::stepAjax("Classes-shm-rem-ok", UI_MSG_OK);
}
  
CJSLoader::writeLocaleFile();
CAppUI::stepAjax("Locales-javascript-cache-allup", UI_MSG_OK);

// Module specific removals
foreach (glob("modules/*/empty_shared_memory.php") as $script) {
	require $script;
}