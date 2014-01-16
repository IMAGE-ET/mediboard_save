<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage System
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$object_guid = CValue::get("object_guid");
$module      = CValue::get("module");
$inherit     = CValue::get("inherit");
$uid         = CValue::get("uid");

$object = null;

if ($object_guid && $object_guid != "global") {
  $object = CMbObject::loadFromGuid($object_guid);
  $configs = CConfiguration::getClassConfigs($object->_class, $module, $inherit);
}
else {
  if (!CAppUI::$user->isAdmin()) {
    global $can;
    $can->redirect();
    return;
  }

  $model = CConfiguration::getModuleConfigs($module, $inherit);
  
  $configs = array();
  foreach ($model as $_model) {
    $configs = array_merge($configs, $_model);
  }
}

$ancestor_configs = CConfiguration::getAncestorsConfigs($inherit, array_keys($configs), $object);

$smarty = new CSmartyDP();
$smarty->assign("ancestor_configs", $ancestor_configs);
$smarty->assign("object_guid",      $object_guid);
$smarty->assign("configs",          $configs);
$smarty->assign("inherit",          $inherit);
$smarty->assign("uid",              $uid);
$smarty->display("inc_edit_object_config.tpl");