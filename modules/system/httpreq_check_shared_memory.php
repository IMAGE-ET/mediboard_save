<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m, $shm;

$can->needsEdit();

// Check locales
foreach (glob("locales/*", GLOB_ONLYDIR) as $localeDir) {
  $localeName = basename($localeDir);
  $locales = array();
  $localeFiles = array_merge(glob("locales/$localeName/*.php"), glob("modules/*/locales/$localeName.php"));
  foreach ($localeFiles as $localeFile) {
    if (basename($localeFile) != "encoding.php") {
      require $localeFile;
    }
  }

  if (null == $sharedLocale = $shm->get("locales-$localeName")) {
    echo "<div class='message'>Table absente en m�moire pour langage '$localeName'</div>";
    continue;
  }      
    
  if ($sharedLocale != $locales) {
    echo "<div class='warning'>Table p�rim�e pour langage '$localeName'</div>";
    continue;
  }
  
  echo "<div class='message'>Table � jour pour langage '$localeName'</div>";
}

// Check class paths
$AppUI->getAllClasses();
$classNames = getChildClasses(null);
foreach($classNames as $className) {
  $class = new ReflectionClass($className);
  $classPaths[$className] = $class->getFileName();
}

if (null == $sharedClassPaths = $shm->get("class-paths")) {
  echo "<div class='message'>Table des classes absente en m�moire</div>";
  return;
}      
  
if ($sharedClassPaths != $classPaths) {
  echo "<div class='error'>Table des classes p�rim�e</div>";
  return;
}

echo "<div class='message'>Table des classes � jour</div>";

