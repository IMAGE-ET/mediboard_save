<?php
/**
 * $Id: httpreq_widget_files.php 20068 2013-07-26 13:21:27Z rhum1 $
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 20068 $
 */

CCanDo::check();

$object        = mbGetObjectFromGet("object_class", "object_id", "object_guid");
$only_files    = CValue::get("only_files", 0);
$name_readonly = CValue::get("name_readonly", 0);

// Chargement des fichiers
$object->loadRefsFiles();
$object->canDo();

if ($object->_ref_files) {
  foreach ($object->_ref_files as $_file) {
    $_file->canDo();
  }
}

$file = new CFile();

$smarty = new CSmartyDP();
$smarty->assign("object",        $object);
$smarty->assign("canDoFiles",    $file->loadPermClass());
$smarty->assign("name_readonly", $name_readonly);

$smarty->display($only_files ? "inc_widget_list_files.tpl" : "inc_widget_vw_files.tpl");
