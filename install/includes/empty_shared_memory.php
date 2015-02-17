<?php 
/**
 * Installation Shared memory manager
 *  
 * @package    Mediboard
 * @subpackage Installer
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id$ 
 * @link       http://www.mediboard.org
 */

$rootName = basename($dPconfig["root_dir"]);
require_once "../classes/SHM.class.php";
        
// Remove locales
foreach (glob("../locales/*", GLOB_ONLYDIR) as $localeDir) {
  $localeName = basename($localeDir);
  $sharedName = "locales-$localeName";
  
  if (!SHM::get("$sharedName-.__prefixes__")) {
    echo "Table absente en m�moire pour langage '$localeName'<br />";
    continue;
  }
  
  if (!SHM::remKeys("$sharedName-*")) {
    echo "Impossible de supprimer la table pour le langage '$localeName'<br />";
    continue;
  }
    
  echo "Table supprim�e pour langage '$localeName'<br />";
}

if (!SHM::rem("class-paths")) {
  echo "Impossible de supprimer la table des classes<br />";
  return;
}

echo "Table des classes supprim�e<br />";

$classes = array(
  "CApp"
);

foreach ($classes as $_class) {
  $count = SHM::remKeys("$_class*");
  echo CAppUI::tr("module-system-msg-cache-removal", $count, $_class);
}

echo "Table des classes filles supprim�e<br />";

if (!SHM::rem("modules")) {
  echo "Impossible de supprimer la table des classes<br />";
  return;
}

echo "Table des modules supprim�e<br />";
