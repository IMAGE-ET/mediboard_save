<?php /* $Id: $*/

/**
* @package Mediboard
* @subpackage sytem
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsEdit();

CAppUI::getAllClasses();
$setupClasses = getChildClasses("CSetup");
$mbmodules = array();
$coreModules = array();
foreach($setupClasses as $setupClass) {
  $setup = new $setupClass;
  $mbmodule = new CModule();
  $mbmodule->compareToSetup($setup);
  $mbmodules[] = $mbmodule;
  if ($mbmodule->mod_type == "core" && $mbmodule->_upgradable) {
    $coreModules[] = $mbmodule;
  }
}

array_multisort(CMbArray::pluck($mbmodules, "mod_ui_order"), SORT_ASC, $mbmodules);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("mbmodules"   , $mbmodules);
$smarty->assign("coreModules" , $coreModules);

$smarty->display("view_modules.tpl");

?>