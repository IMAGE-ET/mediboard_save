<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision$
* @author Fabien M�nager
*/

$class = mbGetValueFromGet('class');
$field = mbGetValueFromGet('field');
$input = mbGetValueFromGet($field);
$limit = mbGetValueFromGet('limit', 15);
$wholeString = mbGetValueFromGet('wholeString', 'false') == 'true';

$search = $wholeString ? "%$input%" : "$input%";
$where = "$field LIKE '$search'";

$object = new $class;
$matches = $object->loadList($where, $field, $limit);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign('matches', $matches);
$smarty->assign('input',   $input);
$smarty->assign('field',   $field);
$smarty->assign('nodebug', true);

$smarty->display('inc_field_autocomplete.tpl');

?>