<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$group = CGroups::loadCurrent();

$graphs        = CSupervisionGraph::getAllFor($group);
$timed_data    = CSupervisionTimedData::getAllFor($group);
$timed_pictures = CSupervisionTimedPicture::getAllFor($group);
$instant_data  = CSupervisionInstantData::getAllFor($group);
$packs         = CSupervisionGraphPack::getAllFor($group, true);

foreach ($graphs as $_graph) {
  $_axes = $_graph->loadRefsAxes();

  foreach ($_axes as $_axis) {
    $_axis->loadBackRefs("series");
  }
}

$smarty = new CSmartyDP();
$smarty->assign("graphs",         $graphs);
$smarty->assign("packs",          $packs);
$smarty->assign("timed_data",     $timed_data);
$smarty->assign("timed_pictures", $timed_pictures);
$smarty->assign("instant_data",   $instant_data);
$smarty->display("inc_list_supervision_graph.tpl");
