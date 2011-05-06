<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $m, $AppUI;

$dialog = CValue::get("dialog");
$start  = CValue::get("start", 0);

if (!$can->edit && !$dialog) {
  $can->redirect();
}

$filter = new CUserLog();
$filter->_date_min    = CValue::getOrSession("_date_min");
$filter->_date_max    = CValue::getOrSession("_date_max");
$filter->user_id      = CValue::getOrSession("user_id");
$filter->object_id    = CValue::getOrSession("object_id");
$filter->object_class = CValue::getOrSession("object_class");
$filter->type         = CValue::getOrSession("type");
$ex_class_id          = CValue::get("ex_class_id");

$object = new CMbObject();
if($filter->object_id && $filter->object_class) {
	$object = new $filter->object_class;
	
	if ($ex_class_id && $filter->object_class == "CExObject") {
		$object->_ex_class_id = $ex_class_id;
		$object->setExClass();
		$filter->object_class .= "_$ex_class_id";
	}
	
	$object->load($filter->object_id);
	$object->loadHistory();
}

// R�cup�ration de la liste des classes disponibles
$listClasses = array();

if (!$dialog) {
  $listClasses = CApp::getChildClasses();
}

// R�cup�ration de la liste des utilisateurs disponibles
$user = new CUser;
$user->template = "0";
$order = "user_last_name, user_first_name";
$listUsers = $user->loadMatchingList($order);

// R�cup�ration des logs correspondants
$where = array();
if ($filter->user_id     ) $where["user_id"     ] = "= '$filter->user_id'";
if ($filter->object_id   ) $where["object_id"   ] = "= '$filter->object_id'";
if ($filter->object_class) $where["object_class"] = "= '$filter->object_class'";
if ($filter->type        ) $where["type"        ] = "= '$filter->type'";
if ($filter->_date_min   ) $where[] = "date >= '$filter->_date_min'";
if ($filter->_date_max   ) $where[] = "date <= '$filter->_date_max'";

$log = new CUserLog;
$list = $log->loadList($where, "date DESC", "$start,100");
$list_count = $log->countList($where);

$group_id = CGroups::loadCurrent()->_id;

foreach($list as $key => $log) {
  $log->loadRefsFwd();
  $log->_ref_user->loadRefMediuser();
  $mediuser = $log->_ref_user->_ref_mediuser;
  $mediuser->loadRefFunction();
  $log->getOldValues();
  if (!$can->admin) {
    if ($mediuser->_ref_function->group_id != $group_id) {
      unset($list[$key]);
    }
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("dialog"      , $dialog      );
$smarty->assign("filter"      , $filter      );
$smarty->assign("object"      , $object      );
$smarty->assign("listClasses" , $listClasses );
$smarty->assign("listUsers"   , $listUsers   );
$smarty->assign("list"        , $list        );
$smarty->assign("start"       , $start       );
$smarty->assign("list_count"  , $list_count  );

$smarty->display("view_history.tpl");

?>