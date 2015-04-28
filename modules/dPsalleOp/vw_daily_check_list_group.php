<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkAdmin();
$check_list_group_id = CValue::get('check_list_group_id');
$duplicate           = CValue::get('duplicate', 0);

$check_list_group = new CDailyCheckListGroup();
if ($check_list_group_id) {
  $check_list_group->load($check_list_group_id);
  foreach ($check_list_group->loadRefChecklist() as $list_type) {
    $list_type->loadRefsCategories();
  }
}
$check_list_groups = $check_list_group->loadGroupList(null, 'title');

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("check_list_groups" , $check_list_groups);
$smarty->assign("check_list_group"  , $check_list_group);
$smarty->assign("duplicate"         , $duplicate);

if ($check_list_group_id != "") {
  $smarty->display("inc_edit_check_list_group.tpl");
}
else {
  $smarty->display("vw_daily_check_list_group.tpl");
}

