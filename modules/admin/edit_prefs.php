<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage admin
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $can, $m, $utypes;
// Utilisateur demand�
$user_id = mbGetValueFromGet("user_id" , 0);

// V�rification des droit
if ($can->edit){
  $user_id = mbGetValueFromGetOrSession("user_id", $AppUI->user_id);
  $user_id = intval($user_id);
}else{
  $user_id = $AppUI->user_id;
}  

// Chargement User demand�
$user = null;
if($user_id!==null){
  $user = new CUser;
  $user->load($user_id);
  
  if($user_id == $AppUI->user_id){
    $prefs = $AppUI->user_prefs;
  }else{
    $sql = "SELECT pref_name, pref_value
          FROM user_preferences
          WHERE pref_user = $user_id";
    $prefs = db_loadHashList( $sql );
  }
}

// load the preferences

$prefsUser = array();

// Pr�f�rences Globales
$array_list_pref_generale = array (
  "LOCALE",
  "UISTYLE",
  "MenuPosition",
  "DEFMODULE",
);

foreach ($array_list_pref_generale as $namePref){
  if (!array_key_exists($namePref,$prefs)) {
    $prefs[$namePref] = null;
  }
  $prefsUser["GENERALE"][$namePref] = $prefs[$namePref];
}


// Pr�f�rences par Module
$array_list_module_pref = array (
  "dPpatients" => array (
    "DEPARTEMENT",
  ),
  "dPcabinet" => array (
    "AFFCONSULT",
    "MODCONSULT",
    "AUTOADDSIGN",
    "GestionFSE", 
    "InterMaxDir", 
    "DefaultPeriod",
    "DossierCabinet",
    "simpleCabinet",
    "ccam",
  ),
  "system" => array (
    "INFOSYSTEM",
  ),
);

foreach($array_list_module_pref as $modulename => $listPrefs){
  $prefsUser[$modulename] = array();	
  $prefModule = CModule::getInstalled($modulename);
  if (($user_id!==0 && $prefModule->mod_id && CPermModule::getInfoModule("view", $prefModule->mod_id, PERM_READ, $user_id)) || $user_id===0){
    foreach ($listPrefs as $namePref){
    	if(!array_key_exists($namePref,$prefs)){
    	  $prefs[$namePref] = null;
    	}
      $prefsUser[$modulename][$namePref] = $prefs[$namePref];
    }    
  }
}

// Chargement des modules
$modules = CPermModule::getVisibleModules();

// Chargement des languages
$locales = $AppUI->readDirs("locales");
mbRemoveValuesInArray(".svn", $locales);

// Chargement des styles
$styles = $AppUI->readDirs("style");
mbRemoveValuesInArray(".svn", $styles);


// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("user"     , $user);
$smarty->assign("user_id"  , $user_id);
$smarty->assign("locales"  , $locales);
$smarty->assign("styles"   , $styles);
$smarty->assign("modules"  , $modules);
$smarty->assign("prefsUser", $prefsUser);

$smarty->display("edit_prefs.tpl");
?>
