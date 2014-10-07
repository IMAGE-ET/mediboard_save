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

$object_class = CValue::get('object_class');
$field        = CValue::get('field');
$view_field   = CValue::get('view_field', $field);
$input_field  = CValue::get('input_field', $view_field);
$show_view    = CValue::get('show_view', 'false') == 'true';
$keywords     = CValue::get($input_field);
$limit        = CValue::get('limit', 30);
$where        = CValue::get('where', array());
$whereComplex = CValue::get('whereComplex', array());
$ljoin        = CValue::get("ljoin", array());

/** @var CMbObject $object */
$object = new $object_class;
$ds = $object->_spec->ds;

foreach ($where as $key => $value) {
  $where[$key] = $ds->prepare("= %", $value);
  $object->$key = $value;
}

foreach ($whereComplex as $key => $value) {
  $where[$key] = stripslashes($value);
}

if ($keywords == "") {
  $keywords = "%";
}

$matches = $object->getAutocompleteList($keywords, $where, $limit, $ljoin);

$template = $object->getTypedTemplate("autocomplete");

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign('matches'   , $matches);
$smarty->assign('field'     , $field);
$smarty->assign('view_field', $view_field);
$smarty->assign('show_view' , $show_view);
$smarty->assign('template'  , $template);
$smarty->assign('nodebug'   , true);
$smarty->assign("input"     , "");

$smarty->display('inc_field_autocomplete.tpl');
