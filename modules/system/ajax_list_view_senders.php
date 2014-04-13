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

CCanDo::checkRead();

// Minute courante
$time = CMbDT::time();
$minute = intval(CMbDT::transform($time, null, "%M"));

// Chargement des senders
$sender = new CViewSender();

/** @var CViewSender[] $senders */
$senders = $sender->loadList(null, "name");
CStoredObject::massLoadBackRefs($senders, "sources_link");
foreach ($senders as $_sender) {
  $_sender->makeHourPlan($minute);
  $_sender->loadRefSendersSource();
}

// Tableau de charges
$hour_sum = array();
$hour_total = 0;
foreach (range(0, 59) as $min) {
  $hour_sum[$min] = 0;
  foreach ($senders as $_sender) {
    if ($_sender->active) {
      $hour_sum[$min] += $_sender->_hour_plan[$min];
      $hour_total += $_sender->_hour_plan[$min] / 60;
    }
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("senders", $senders);
$smarty->assign("hour_sum", $hour_sum);
$smarty->assign("hour_total", $hour_total);
$smarty->assign("time", $time);
$smarty->assign("minute", $minute);
$smarty->display("inc_list_view_senders.tpl");
