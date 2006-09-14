<?php /* $Id: $*/

/**
* @package Mediboard
* @subpackage sytem
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("mediusers"));
require_once($AppUI->getModuleClass("dPplanningOp", "planning"));

if(!$canEdit) {
  $AppUI->redirect("m=system&a=access_denied");
}

$sql = "SELECT * FROM modules ORDER BY mod_ui_order";
$modules = db_loadList($sql);

// get the modules actually installed on the file system
$modFiles = $AppUI->readDirs("modules");
mbRemoveValuesInArray(".svn", $modFiles);

$coreModules = array();

// do the modules that are installed on the system
foreach ($modules as $keyRow => $row) {
  $modules[$keyRow]["is_setup"]     = @include_once($AppUI->cfg["root_dir"]."/modules/".$row["mod_name"]."/setup.php");
  $modules[$keyRow]["is_upToDate"]  = $config["mod_version"] == $row["mod_version"];
  $modules[$keyRow]["is_config"]    = is_file("modules/".$row["mod_name"]."/configure.php");
  $modules[$keyRow]["href"]         = "?m=$m&amp;a=domodsql&amp;mod_id=".$row["mod_id"];
  
  if($row["mod_name"] == "system" || $row["mod_name"] == "admin") {
    if(!$modules[$keyRow]["is_upToDate"]) {
      $coreModules[] = $modules[$keyRow];
    }
  }

  if(isset($modFiles[$row["mod_name"]])) {
    unset($modFiles[$row["mod_name"]]);
  } 
}

// Cr�ation du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->debugging = false;
$smarty->assign("modules"     , $modules);
$smarty->assign("coreModules" , $coreModules);
$smarty->assign("modFiles"    , $modFiles);

$smarty->display("view_modules.tpl");

?>