<?php 
/**
 * Access logging 
 *
 * PHP version 5.1.x+
 *  
 * @category   Dispatcher
 * @package    Mediboard
 * @subpackage Includes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    SVN: $Id$ 
 * @link       http://www.mediboard.org
 */

if (CAppUI::conf("readonly")) {
	return;
}

global $m, $action;

$ds = CSQLDataSource::get("std");
// $action is not defined when the module is inactive
if (!$ds->loadTable("access_log") || !$action) { 
  return;
}

$period = mbTransformTime(null, null, "%Y-%m-%d %H:00:00");

$log = new CAccessLog();
$log->module   = $m;
$log->action   = $action;
$log->period   = $period;

if (!$log->loadMatchingObject()) {
  $log->hits        = 0;
  $log->duration    = 0.0;
  $log->processus   = 0.0;
  $log->processor   = 0.0;
  $log->request     = 0.0;
  $log->size        = 0;
  $log->peak_memory = 0;
  $log->errors      = 0;
  $log->warnings    = 0;
  $log->notices     = 0;
  $log->peak_memory = 0;
}

$getrusage = getrusage();
$log->hits++;
$log->duration    += $phpChrono->total;
$log->processus   += floatval($getrusage["ru_utime.tv_usec"]) / 1000000 + $getrusage["ru_utime.tv_sec"];
$log->processor   += floatval($getrusage["ru_stime.tv_usec"]) / 1000000 + $getrusage["ru_stime.tv_sec"];
$log->request     += $ds->chrono->total;
$log->size        += ob_get_length();
$log->peak_memory += memory_get_peak_usage();
$log->errors      += $performance["error"];
$log->warnings    += $performance["warning"];
$log->notices     += $performance["notice"];

if ($msg = $log->store()) {
  trigger_error($msg, E_USER_WARNING);
}
