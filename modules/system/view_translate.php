<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $m;

CCanDo::checkAdmin();

$module = CValue::getOrSession("module" , "admin");

// liste des dossiers modules + common et styles
$modules = array_merge(array(
  "common" => "common",
  "styles" => "styles"), 
  CAppUI::readDirs("modules")
);

CMbArray::removeValue(".svn", $modules);
ksort($modules);

// If the locale files are in the module's "locales" directory
$in_module = (is_dir("modules/$module/locales"));

// Dossier des traductions
$localesDirs = array();
if ($in_module) {
  $files = glob("modules/$module/locales/*");
  foreach ($files as $file) {
    $name = basename($file, ".php");
    $localesDirs[$name] = $name;
  }
}
else {
  $localesDirs = CAppUI::readDirs("locales");
  CMbArray::removeValue(".svn",$localesDirs);
}

// R�cup�ration du fichier demand� pour toutes les langues
$translateModule = new CMbConfig;
$translateModule->sourcePath = null;
$contenu_file = array();
foreach($localesDirs as $locale){
  $translateModule->options = array("name" => "locales");
  $translateModule->targetPath = ($in_module ? "modules/$modules[$module]/locales/$locale.php" : "locales/$locale/$modules[$module].php");
  $translateModule->load();
  $contenu_file[$locale] = $translateModule->values;
}

// R�attribution des cl�s et organisation
$trans = array();
foreach($localesDirs as $locale){
	foreach($contenu_file[$locale] as $k=>$v){
		$trans[ (is_int($k) ? $v : $k) ][$locale] = $v;
	}
}

// Remplissage par null si la valeur n'existe pas
foreach($trans as $k=>$v){
  foreach($localesDirs as $keyLocale=>$valueLocale){
  	if(!isset($trans[$k][$keyLocale])){
  		$trans[$k][$keyLocale] = null;
  	}
  }
}
uksort($trans,"strnatcasecmp");

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("locales"  , $localesDirs);
$smarty->assign("modules"  , $modules);
$smarty->assign("module"   , $module);
$smarty->assign("trans"    , $trans);

$smarty->display("view_translate.tpl");
?>