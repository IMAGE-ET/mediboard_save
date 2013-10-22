<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPdeveloppement
 * @version $Revision$
 * @author openXtrem
 */

CCanDo::checkRead();

$locales = CAppUI::flattenCachedLocales(CAppUI::$lang);

$tabs = array();
foreach ($modules = CModule::getInstalled() as $module) {
  CAppUI::requireModuleFile($module->mod_name, "index");
  if (is_array($module->_tabs)) {
    foreach ($module->_tabs as $tab) {
      $tabs[$tab]["name"]   = "mod-$module->mod_name-tab-$tab";
      $tabs[$tab]["locale"] = CValue::read($locales, $tabs[$tab]["name"]);
    }
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("module", $modules);
$smarty->assign("tabs"  , $tabs);

$smarty->display("mnt_module_actions.tpl");
