<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author Romain Ollivier
 */

global $AppUI, $can, $m;

$dialog = mbGetValueFromGet("dialog");

if (!$can->read && !$dialog) {
  $can->redirect();
}

$today = mbDate();
$tomorow = mbDate("+1 day");

$filter = new CUserLog();
$filter->_date_min    = mbGetValueFromGetOrSession("_date_min", $today);
$filter->_date_max    = mbGetValueFromGetOrSession("_date_max", $tomorow);
$filter->user_id      = mbGetValueFromGetOrSession("user_id");
$filter->object_id    = mbGetValueFromGetOrSession("object_id");
$filter->object_class = mbGetValueFromGetOrSession("object_class");
$filter->type         = mbGetValueFromGetOrSession("type"        );

// Récupération de la liste des classes disponibles
$AppUI->getAllClasses();
$listClasses = getChildClasses();

// Récupération de la liste des utilisateurs disponibles
$user = new CUser;
$user->template = "0";
$order = "user_last_name, user_first_name";
$listUsers = $user->loadMatchingList($order);

// Récupération des types disponibles
$userLog = new CUserLog;
$userLog->buildEnums();

// Récupération des logs correspondants
$where = array();
if ($filter->user_id     ) $where["user_id"     ] = "= '$filter->user_id'";
if ($filter->object_id   ) $where["object_id"   ] = "= '$filter->object_id'";
if ($filter->object_class) $where["object_class"] = "= '$filter->object_class'";
if ($filter->type        ) $where["type"        ] = "= '$filter->type'";

if (isset($_GET["_date_min"]) && isset($_GET["_date_max"])) {
  $where["date"] = "BETWEEN '$filter->_date_min' AND '$filter->_date_max'";
}

$log = new CUserLog;
$order = "date DESC";
$list = $log->loadList($where, $order, "0, 100");
$item = "";
foreach($list as $key => $value) {
  $list[$key]->loadRefsFwd();
  if($item == "")
    $item = $list[$key]->_ref_object->_view;
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("dialog"      , $dialog      );
$smarty->assign("filter"      , $filter      );

$smarty->assign("listClasses" , $listClasses );
$smarty->assign("listUsers"   , $listUsers   );
$smarty->assign("userLog"     , $userLog     );
$smarty->assign("item"        , $item        );
$smarty->assign("list"        , $list        );

$smarty->display("view_history.tpl");

?>