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
$rdv          = CValue::get('rdv', 0);
$compta       = CValue::get('compta', 0);
$edit         = CValue::get('edit', 0);
$keywords     = CValue::get($input_field);
$limit        = CValue::get('limit', 30);
$where        = CValue::get('where', array());
$whereComplex = CValue::get('whereComplex', array());
$ljoin        = CValue::get("ljoin", array());

/** @var CMediusers $object */
$object = new CMediusers();
$user = CMediusers::get();

$use_edit           = CAppUI::pref("useEditAutocompleteUsers");
if (!$edit && $use_edit) {
  $edit = 1;
}

// Droits sur les utilisateurs retourn�s
$permType = $edit ?
  PERM_EDIT :
  PERM_READ;

// R�cup�ration de la liste des utilisateurs
if ($rdv) {
  $listUsers = $object->loadProfessionnelDeSanteByPref($permType, null, $keywords);
}
elseif ($praticiens) {
  $listUsers = $object->loadPraticiens($permType, null, $keywords);
}
else {
  $listUsers = $object->loadUsers($permType, null, $keywords);
}

if ($compta) {
  $listUsersCompta = CConsultation::loadPraticiensCompta();
  foreach ($listUsers as $_user) {
    if (!isset($listUsersCompta[$_user->_id])) {
      unset($listUsers[$_user->_id]);
    }
  }
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
