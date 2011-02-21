<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

global $AppUI, $m, $tab;

$current = intval(CValue::get('current', 0));
$step    = intval(CValue::get('step', 20));
$interv  = CValue::get("interv", "");
$exclude_without_code = CValue::get("exclude_without_code", "false");

$intervenant = new CIntervenantCdARR();
$intervenants = $intervenant->loadList(null, "code");

$mediuser = new CMediusers();

$ljoin = array();
$ljoin["users"] = "users.user_id = users_mediboard.user_id";
$ljoin["functions_mediboard"] = "functions_mediboard.function_id = users_mediboard.function_id";

$where = array();
$where["users_mediboard.actif"] = "= '1'";
$where["functions_mediboard.group_id"] = "= '" . CGroups::loadCurrent()->_id . "'";

if ($interv) {
  $last_space = strrpos($interv, " ");
  $last_name = substr($interv, 0, $last_space);

  $where[] = "users.user_last_name = '$last_name' 
  OR users.user_last_name = '$last_name'";
}

$limit = "$current, $step";
$order = "users.user_last_name ASC, users.user_first_name ASC";
$total = $mediuser->countList($where, $order, null, null, $ljoin);
$mediusers = $mediuser->loadList($where, $order, $limit, null, $ljoin);

foreach($mediusers as &$_mediuser) {
  $_mediuser->loadRefsFwd();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("mediuser"    , $mediuser);

$smarty->assign("intervenants", $intervenants);
$smarty->assign("mediusers"   , $mediusers);

$smarty->assign("current"     , $current);
$smarty->assign("step"        , $step);
$smarty->assign("total"       , $total);

$smarty->assign("exclude_without_code", $exclude_without_code);

$smarty->display("edit_codes_intervenants.tpl");

?>