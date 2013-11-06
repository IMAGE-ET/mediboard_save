<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage mediusers
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$object_class = CValue::get('object_class');
$field        = CValue::get('field');
$view_field   = CValue::get('view_field', $field);
$input_field  = CValue::get('input_field', $view_field);
$show_view    = CValue::get('show_view', 'false') == 'true';
$praticiens   = CValue::get('praticiens', 0);
$keywords     = CValue::get($input_field);
$limit        = CValue::get('limit', 30);
$where        = CValue::get('where', array());
$whereComplex = CValue::get('whereComplex', array());
$ljoin        = CValue::get("ljoin", array());

/** @var CMbObject $object */
$object = new CMediusers();
$user = CMediusers::get();
if ($praticiens) {
  // V�rification des droits sur les praticiens
  $listUsers = $user->isAnesth() ? $user->loadPraticiens(null, null, $keywords) : $user->loadPraticiens(PERM_EDIT, null, $keywords);
}
else {
  $listUsers = $user->loadUsers(PERM_READ, null, $keywords);
}

$template = $object->getTypedTemplate("autocomplete");

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign('matches'   , $listUsers);
$smarty->assign('field'     , $field);
$smarty->assign('view_field', $view_field);
$smarty->assign('show_view' , $show_view);
$smarty->assign('template'  , $template);
$smarty->assign('nodebug'   , true);
$smarty->assign("input"     , "");

$smarty->display("../../system/templates/inc_field_autocomplete.tpl");