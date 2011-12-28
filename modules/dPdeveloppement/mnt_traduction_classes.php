<?php

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author Poiron Yohann
 */

CCanDo::checkEdit();

$module = CValue::getOrSession("module", "system");
$classes = CModule::getClassesFor($module);
global $language;
$language = CValue::getOrSession("language", "fr");

// Hack to have CModule in system locale file
if ($module == "system") {
  $classes[] = "CModule";
}

// liste des dossiers modules + common et styles
$modules = array_keys(CModule::getInstalled());
sort($modules);

// Dossier des traductions
$localesDirs = array();
$files = glob("modules/$module/locales/*");
foreach ($files as $file) {
  $name = basename($file, ".php");
  $localesDirs[$name] = $name;
}

// R�cup�ration du fichier demand� pour toutes les langues
$translateModule = new CMbConfig;
$translateModule->sourcePath = null;
$contenu_file = array();
foreach($localesDirs as $locale){
  $translateModule->options = array("name" => "locales");
  $translateModule->targetPath = "modules/$module/locales/$locale.php";
  $translateModule->load();
  $contenu_file[$locale] = $translateModule->values;
}

// R�attribution des cl�s et organisation
global $trans;
$trans = array();
foreach($localesDirs as $locale){
	foreach($contenu_file[$locale] as $k=>$v){
		$trans[ (is_int($k) ? $v : $k) ][$locale] = $v;
	}
}


global $items, $completions, $all_locales;
$items = array();
$completions = array();
$all_locales = $contenu_file[$locale];

// Ajoute un item de localisation
function addLocale($class, $cat, $name) {
  global $trans, $items, $completions, $language, $all_locales;
  
  $items[$class][$cat][$name] = array_key_exists($name, $trans) ? @$trans[$name][$language] : "";
  $items[$class][$cat][$name] = str_replace('\n', "\n", $items[$class][$cat][$name]);
  
  unset($all_locales[$name]);
  
  // Stats
  @$completions[$class]["total"]++;
  if ($items[$class][$cat][$name]) {
    @$completions[$class]["count"]++;
  }
  
  @$completions[$class]["percent"] = round(100 * $completions[$class]["count"] / $completions[$class]["total"]);
}

// Parcours des classes
foreach ($classes as $class) {
  $object = new $class;
  $ref_modules = $object->_specs;
  $classname = $object->_class;
  
  // Traductions au niveau classe
  addLocale($classname, $classname, "$classname");
  addLocale($classname, $classname, "$classname.none");
  addLocale($classname, $classname, "$classname.one");
  addLocale($classname, $classname, "$classname.all");
  addLocale($classname, $classname, "$classname-msg-create");
  addLocale($classname, $classname, "$classname-msg-modify");
  addLocale($classname, $classname, "$classname-msg-delete");
  addLocale($classname, $classname, "$classname-title-create");
  addLocale($classname, $classname, "$classname-title-modify");
  
  // Traductions pour la cl� 
  $prop = $object->_spec->key;
  addLocale($classname, $prop, "$classname-$prop");
  addLocale($classname, $prop, "$classname-$prop-desc");
  addLocale($classname, $prop, "$classname-$prop-court");
  
  // Traductions de chaque propri�t�
	foreach ($object->_specs as $prop => $spec) { 
    if (!$spec->prop) {
      continue;
    }
    
    if (in_array($prop, array($object->_spec->key, "_view", "_shortview"))) {
      continue;
    }
    
    // Ajout des _ dans la maintenance traduction
    //if ($prop[0] == "_") {
    //  continue;
    //}
    
	  addLocale($classname, $prop, "$classname-$prop");
	  addLocale($classname, $prop, "$classname-$prop-desc");
	  addLocale($classname, $prop, "$classname-$prop-court");
  
    if ($spec instanceof CEnumSpec) {
      if (!$spec->notNull) {
	      addLocale($classname, $prop, "$classname.$prop.");
      }
      
      foreach (explode("|", $spec->list) as $value) {
	      addLocale($classname, $prop, "$classname.$prop.$value");
      }
    }
    
    if ($spec instanceof CRefSpec && $prop[0] != "_") {
      $fwdClass = $spec->class;
      $fwdObject = new $fwdClass;
      
      // Find corresponding back name
      $backName = array_search("$spec->className $spec->fieldName", $fwdObject->_backProps);
      addLocale($classname, $prop, "$spec->class-back-$backName");
      addLocale($classname, $prop, "$spec->class-back-$backName.empty");
    }
  }
  
  // Traductions pour les uniques
  foreach (array_keys($object->_spec->uniques) as $unique) {
    addLocale($classname, "Failures", "$classname-failed-$unique");
  }
}

// Parcours des variables de configuration
function addConfigConfigCategory($chapter, $category, $values) {
  $prefix = $chapter ? "$chapter-$category" : $category;
  
  if (!is_array($values)) {
    addLocale("Config", "global", "config-$prefix");
    addLocale("Config", "global", "config-$prefix-desc");
    return;
  }
  
  foreach ($values as $key => $value) {
    addLocale("Config", $category, "config-$prefix-$key");
    addLocale("Config", $category, "config-$prefix-$key-desc");
  }
}

if ($categories = @CAppUI::conf($module)) {
	foreach ($categories as $category => $values) {
	  addConfigConfigCategory($module, $category, $values);
	}
}

if ($module == "system") {
  foreach (CAppUI::conf() as $chapter => $values) {
    if (!CModule::exists($chapter) && $chapter != "db") {
      addConfigConfigCategory(null, $chapter, $values);
    }
  }
}

$files = CAppUI::readFiles("modules/$module", '\.php$');

addLocale("Module", "Name", "module-$module-court");
addLocale("Module", "Name", "module-$module-long");

foreach($files as $_file) {
  $_tab = substr($_file, 0, -4);
  
  if (in_array($_tab, array("setup", "index", "config", "preferences"))/* ||
      preg_match("/^httpreq|^ajax/", $_tab)*/) continue;
  
  addLocale("Module", "Tabs", "mod-$module-tab-$_tab");
}

$empty_locales = array_fill(0, 5, null);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("items"  	    , $items);
$smarty->assign("completions" , $completions);
$smarty->assign("locales"  		, $localesDirs);
$smarty->assign("modules"  		, $modules);
$smarty->assign("module"   		, $module);
$smarty->assign("trans"    		, $trans);
$smarty->assign("language"    , $language);
$smarty->assign("other_locales", $all_locales);
$smarty->assign("empty_locales", $empty_locales);

$smarty->display("mnt_traduction_classes.tpl");
?>