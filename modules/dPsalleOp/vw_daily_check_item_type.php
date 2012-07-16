<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author Fabien M�nager
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $g;
$can->needsAdmin();

$item_type_id = CValue::getOrSession('item_type_id');

$item_type = new CDailyCheckItemType;
if (!$item_type->load($item_type_id)) {
  $item_type->index = 1;
}

$item_category = new CDailyCheckItemCategory;
$item_categories_list = array();

$target_class_list = $item_category->_specs["target_class"]->_list;

foreach(CDailyCheckList::$_HAS_classes as $_class) {
  CMbArray::removeValue($_class, $target_class_list);
}

foreach($target_class_list as $_target) {
  $item_category->target_class = $_target;
  $list_cat = $item_category->loadMatchingList('target_class, title');
  
  foreach ($list_cat as $cat) {
    $cat->loadBackRefs('item_types', 'title');
    foreach($cat->_back['item_types'] as $id => $type) {
      if ($type->group_id != $g) {
        unset($cat->_back['item_types'][$id]);
      }
    }
  }
  $item_categories_list[$_target] = $list_cat;
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("item_type", $item_type);
$smarty->assign("item_category", $item_category);
$smarty->assign("item_categories_list", $item_categories_list);
$smarty->assign("target_class_list", $target_class_list);
$smarty->display("vw_daily_check_item_type.tpl");

?>