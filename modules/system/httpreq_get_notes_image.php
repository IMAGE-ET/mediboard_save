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

if (!$object_guid) {
  return;
}

$object = CMbObject::loadFromGuid($object_guid);
if ($object->_id) {
  $object->loadRefsNotes(PERM_READ);
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("object", $object);
$smarty->assign("mode" , "edit");
$smarty->assign("float", "left");
$smarty->display("inc_get_notes_image.tpl");
