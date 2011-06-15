<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage system
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkAdmin();
$date     = CValue::getOrSession("date"    , mbDate());
$groupmod = CValue::getOrSession("groupmod", 2);

// Hour range for daily stats
$hour_min = CValue::getOrSession("hour_min", "6");
$hour_max = CValue::getOrSession("hour_max", "23");
$hours = range(0, 23);

$left_mode      = CValue::getOrSession("left_mode", "request_time"); // request_time, cpu_time, errors, memory_peak
$left_sampling  = CValue::getOrSession("left_sampling", "mean"); // total, mean

$right_mode     = CValue::getOrSession("right_mode", "hits"); // hits, size
$right_sampling = CValue::getOrSession("right_sampling", "total"); // total, mean

$module = null;
if (!is_numeric($groupmod)) {
  $module = $groupmod;
  $groupmod = 0;
}

CAppUI::requireModuleFile('dPstats', 'graph_accesslog');

$to    = mbDate("+1 DAY", $date);
switch ($interval = CValue::getOrSession("interval", "day")) {
  default:
  case "day":
    $from = mbDate("-1 DAY", $to);
		// Hours limitation
    $from = mbDateTime("+$hour_min HOUR", $from);
    $to   = mbDateTime("-1 DAY +$hour_max HOUR", $to  );
    break;
  case "month":
    $from = mbDate("-1 MONTH", $to);
    break;
  case "hyear":
    $from = mbDate("-6 MONTH", $to);
    break;
  case "twoyears":
    $from = mbDate("-2 YEARS", $to);
    break;
  case "twentyyears":
    $from = mbDate("-20 YEARS", $to);
    break;
}

CSQLDataSource::$trace = false;
$logs = CAccessLog::loadAgregation($from, $to, $groupmod, $module);
$graphs = array();
$left = array($left_mode, $left_sampling);
$right = array($right_mode, $right_sampling);
foreach($logs as $log) {
	switch($groupmod) {
		case 0: $graphs[] = graphAccessLog($log->module, $log->action, $from, $to,$interval, $left, $right); break;
	  case 1: $graphs[] = graphAccessLog($log->module, null        , $from, $to,$interval, $left, $right); break;
	  case 2: $graphs[] = graphAccessLog(null        , null        , $from, $to,$interval, $left, $right); break;
	}
}

CSQLDataSource::$trace = false;

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("graphs"     , $graphs);
$smarty->assign("groupmod"   , $groupmod);

$smarty->assign("date"       , $date);
$smarty->assign("hours"      , $hours);
$smarty->assign("hour_min"   , $hour_min);
$smarty->assign("hour_max"   , $hour_max);

$smarty->assign("left_mode"    , $left_mode);
$smarty->assign("left_sampling", $left_sampling);

$smarty->assign("right_mode"    , $right_mode);
$smarty->assign("right_sampling", $right_sampling);

$smarty->assign("module"     , $module);
$smarty->assign("interval"   , $interval);
$smarty->assign("listModules", CModule::getInstalled());

$smarty->display("view_access_logs.tpl");

?>