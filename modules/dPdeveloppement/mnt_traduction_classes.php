<?php

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision:  $
 * @author Poiron Yohann
 */

global $AppUI, $can, $m;

// only user_type of Administrator (1) can access this page
$can->edit |= ($AppUI->user_type != 1);
$can->needsEdit();

$module = mbGetValueFromGetOrSession("module" , "admin");

$classes = mbGetClassByModule($module);

// liste des dossiers modules + common et styles
$modules = array_merge( array("common"=>"common", "styles"=>"styles") ,$AppUI->readDirs("modules"));
mbRemoveValuesInArray(".svn", $modules);
ksort($modules);

// Dossier des traductions
$localesDirs = $AppUI->readDirs("locales");
mbRemoveValuesInArray(".svn",$localesDirs);
mbRemoveValuesInArray("en",$localesDirs);

// R�cup�ration du fichier demand� pour toutes les langues
$translateModule = new CMbConfig;
$translateModule->sourcePath = null;
$contenu_file = array();
foreach($localesDirs as $locale){
  $translateModule->options = array("name" => "locales");
  $translateModule->targetPath = "locales/fr/$modules[$module].php";
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

function checkTrans(&$array, $key) {
  global $trans;
  $array[$key] = array_key_exists($key,$trans) ? $trans[$key]["fr"] : "";
}


$backSpecs = array();
$backRefs = array();
foreach($classes as $class) {
  $object = new $class;
  $ref_modules = $object->_specs;
  $classname = $object->_class_name;
  
  checkTrans($backSpecs[$classname][$classname], "$classname");
  checkTrans($backSpecs[$classname][$classname], "$classname.one");
  checkTrans($backSpecs[$classname][$classname], "$classname.more");
  checkTrans($backSpecs[$classname][$classname], "$classname.none");
  checkTrans($backSpecs[$classname][$classname], "$classname.modify");
  
  foreach ($object->_specs as $prop => $spec) { 
    if (!$spec->prop) {
      continue;
    }
    checkTrans($backSpecs[$classname][$prop], "$classname-$prop");
    checkTrans($backSpecs[$classname][$prop], "$classname-$prop-desc");
    checkTrans($backSpecs[$classname][$prop], "$classname-$prop-court");
    
    if (is_a($spec, "CRefSpec")) {
      // CAccessLog serves as dummy class when we need to instanciate anyhow
      $fwdClass = $spec->class != "CMbObject" ? $spec->class : "CAccessLog"; 
      $fwdObject = new $fwdClass;
      
      // Find corresponding back ref
      $fwdObject->makeBackSpecs();
      $backSpec = null;
      foreach ($fwdObject->_backSpecs as $_backSpec) {
        if ($_backSpec->class == $spec->className && $_backSpec->field == $spec->fieldName) {
          $backSpec = $_backSpec;
        }
      }
      
      checkTrans($backSpecs[$classname][$prop], "$spec->class-back-$backSpec->name");
    }
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("locales"  		, $localesDirs);
$smarty->assign("modules"  		, $modules);
$smarty->assign("module"   		, $module);
$smarty->assign("trans"    		, $trans);
$smarty->assign("backSpecs"    	, $backSpecs);

$smarty->display("mnt_traduction_classes.tpl");
?>