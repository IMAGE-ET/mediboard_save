<?php

/**
 * $Id$
 *
 * @category Admissions
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$ds = CSQLDataSource::get("std");

// Initialisation de variables
$date          = CValue::getOrSession("date", CMbDT::date());

$month_min     = CMbDT::date("first day of +0 month", $date);
$lastmonth     = CMbDT::date("last day of -1 month" , $date);
$nextmonth     = CMbDT::date("first day of +1 month", $date);

$selSortis     = CValue::getOrSession("selSortis", "0");
$type          = CValue::getOrSession("type");
$service_id    = CValue::getOrSession("service_id");
$prat_id       = CValue::getOrSession("prat_id");
$bank_holidays = CMbDate::getHolidays($date);
$service_id    = explode(",", $service_id);
CMbArray::removeValue("", $service_id);

$hier   = CMbDT::date("- 1 day", $date);
$demain = CMbDT::date("+ 1 day", $date);

// Initialisation des totaux
$days = array();
for ($day = $month_min; $day < $nextmonth; $day = CMbDT::date("+1 DAY", $day)) {
  $days[$day]["num1"] = 0;
  $days[$day]["num2"] = 0;
}

// filtre sur les types de sortie
if ($type == "ambucomp") {
  $filterType = "AND (`sejour`.`type` = 'ambu' OR `sejour`.`type` = 'comp')";
}
elseif ($type) {
  $filterType = "AND `sejour`.`type` = '$type'";
}
else {
  $filterType = "AND `sejour`.`type` != 'urg' AND `sejour`.`type` != 'seances'";
}

// filtre sur les services
if (count($service_id)) {
  $leftjoinService = "LEFT JOIN affectation
                        ON affectation.sejour_id = sejour.sejour_id AND affectation.sortie = sejour.sortie
                      LEFT JOIN lit
                        ON affectation.lit_id = lit.lit_id
                      LEFT JOIN chambre
                        ON lit.chambre_id = chambre.chambre_id
                      LEFT JOIN service
                        ON chambre.service_id = service.service_id";
  $filterService = "AND service.service_id " . CSQLDataSource::prepareIn($service_id);
}
else {
  $leftjoinService = $filterService = "";
}

// filtre sur le praticiens
if ($prat_id) {
  $filterPrat = "AND sejour.praticien_id = '$prat_id'";
}
else {
  $filterPrat = "";
}

$group = CGroups::loadCurrent();

// Listes des sorties par jour
$query = "SELECT DATE_FORMAT(`sejour`.`sortie`, '%Y-%m-%d') AS `date`, COUNT(`sejour`.`sejour_id`) AS `num`
  FROM `sejour`
  $leftjoinService
  WHERE `sejour`.`sortie` BETWEEN '$month_min' AND '$nextmonth'
    AND `sejour`.`group_id` = '$group->_id'
    AND `sejour`.`annule` = '0'
    $filterType
    $filterService
    $filterPrat
  GROUP BY `date`
  ORDER BY `date`";
foreach ($ds->loadHashList($query) as $day => $num1) {
  $days[$day]["num1"] = $num1;
}

// Liste des sorties non effectu�es par jour
$query = "SELECT DATE_FORMAT(`sejour`.`sortie`, '%Y-%m-%d') AS `date`, COUNT(`sejour`.`sejour_id`) AS `num`
  FROM `sejour`
  $leftjoinService
  WHERE `sejour`.`sortie` BETWEEN '$month_min' AND '$nextmonth'
    AND `sejour`.`group_id` = '$group->_id'
    AND `sejour`.`sortie_reelle` IS NULL
    AND `sejour`.`annule` = '0'
    $filterType
    $filterService
    $filterPrat
  GROUP BY `date`
  ORDER BY `date`";
foreach ($ds->loadHashList($query) as $day => $num2) {
  $days[$day]["num2"] = $num2;
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("hier"         , $hier);
$smarty->assign("demain"       , $demain);

$smarty->assign("selSortis"    , $selSortis);

$smarty->assign("bank_holidays", $bank_holidays);
$smarty->assign('date'         , $date);
$smarty->assign('lastmonth'    , $lastmonth);
$smarty->assign('nextmonth'    , $nextmonth);
$smarty->assign('days'         , $days);

$smarty->display('inc_vw_all_sorties.tpl');
