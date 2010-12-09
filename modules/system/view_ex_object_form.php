<?php /* $Id: view_messages.php 7622 2009-12-16 09:08:41Z phenxdesign $ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision: 7622 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

//CCanDo::checkAdmin();

$ex_class_id = CValue::get("ex_class_id");
$ex_object_id = CValue::get("ex_object_id");
$object_guid = CValue::get("object_guid");

if (!$ex_class_id) {
  $msg = "Impossible d'afficher le formulaire sans connaitre la classe de base";
  CAppUI::stepAjax($msg, UI_MSG_WARNING);
  trigger_error($msg, E_USER_ERROR);
  return;
}

$object = CMbObject::loadFromGuid($object_guid);

$ex_object = new CExObject;
$ex_object->setObject($object);
$ex_object->_ex_class_id = $ex_class_id;
$ex_object->setExClass();

if ($ex_object_id) {
  $ex_object->load($ex_object_id);
}

foreach($ex_object->_ref_ex_class->_ref_fields as $_field) {
  $_field->updateTranslation();
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("ex_object", $ex_object);
$smarty->assign("object", $object);
$smarty->display("view_ex_object_form.tpl");
