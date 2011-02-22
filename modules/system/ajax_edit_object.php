<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$object_guid = CValue::get("object_guid");
$values      = CValue::get("_v", array()); // pre-filled values

if (!$object_guid) {
	CAppUI::stepAjax("Un identifiant d'objet doit �tre fourni", UI_MSG_WARNING);
	return;
}

$object = CMbObject::loadFromGuid($object_guid);

global $can;
$can->read = $object->canRead();
$can->edit = $object->canEdit();
$can->needsRead();

$template = $object->getTypedTemplate("edit");

if (!$object->_id && !empty($values)) {
	foreach($values as $_key => $_value) {
		$object->$_key = $_value;
	}
}

$object->loadView();
$object->loadRefsTagItems();

$smarty = new CSmartyDP();
$smarty->assign("object", $object);
$smarty->display($template);
