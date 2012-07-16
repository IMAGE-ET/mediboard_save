<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author Fabien M�nager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can;
$can->needsAdmin();

$item_category_id = CValue::getOrSession('item_category_id');

$item_category = new CDailyCheckItemCategory;
$item_category->load($item_category_id);

foreach(CDailyCheckList::$_HAS_classes as $_class) {
  unset($item_category->_specs["target_class"]->_list[$_class]);
  unset($item_category->_specs["target_class"]->_locales[$_class]);
}

$where = array(
  "target_class" => "NOT ".$item_category->_spec->ds->prepareIn(CDailyCheckList::$_HAS_classes),
);
$item_categories_list = $item_category->loadList($where, 'target_class, type, title');

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("item_category", $item_category);
$smarty->assign("item_categories_list", $item_categories_list);
$smarty->display("vw_daily_check_item_category.tpl");

?>