<?php /* $Id: httpreq_vw_all_admissions.php 11618 2011-03-20 20:22:54Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 11618 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$ds = CSQLDataSource::get("std");

// Initialisation de variables
$date = CValue::getOrSession("date", mbDate());
$month_min     = mbTransformTime("+ 0 month", $date, "%Y-%m-01");
$month_max     = mbTransformTime("+ 1 month", $month_min, "%Y-%m-01");
$lastmonth     = mbDate("-1 month", $date);
$nextmonth     = mbDate("+1 month", $date);
$type          = CValue::getOrSession("type");
$type_externe  = CValue::getOrSession("type_externe", "depart");
$service_id    = CValue::getOrSession("service_id");
$bank_holidays = mbBankHolidays($date);

$hier   = mbDate("- 1 day", $date);
$demain = mbDate("+ 1 day", $date);

// Initialisation du tableau de jours
$days = array();
for ($day = $month_min; $day < $month_max; $day = mbDate("+1 DAY", $day)) {
  $days[$day] = array(
    "num1" => "0",
    "num2" => "0",
    "num3" => "0",
  );
}

// R�cup�ration de la liste des services
$where = array();
$where["externe"]  = "= '1'";
$service = new CService;
$services = $service->loadGroupList($where);

// filtre sur les types d'admission
if($type == "ambucomp") {
  $filterType = "AND (`sejour`.`type` = 'ambu' OR `sejour`.`type` = 'comp')";
} elseif($type) {
  $filterType = "AND `sejour`.`type` = '$type'";
} else {
  $filterType = "AND `sejour`.`type` != 'urg' AND `sejour`.`type` != 'seances'";
}

// filtre sur les services
$filterService = "AND service.service_id ".CSQLDataSource::prepareIn(array_keys($services), $service_id);

$group = CGroups::loadCurrent();

// Liste des d�parts par jour
$query = "SELECT DATE_FORMAT(`affectation`.`entree`, '%Y-%m-%d') AS `date`, COUNT(`affectation`.`affectation_id`) AS `num`
  FROM `affectation`
  LEFT JOIN sejour
    ON affectation.sejour_id = sejour.sejour_id
  LEFT JOIN lit
    ON affectation.lit_id = lit.lit_id
  LEFT JOIN chambre
    ON lit.chambre_id = chambre.chambre_id
  LEFT JOIN service
    ON chambre.service_id = service.service_id
  WHERE `affectation`.`entree` BETWEEN '$month_min' AND '$month_max'
    AND `sejour`.`group_id` = '$group->_id'
    AND `sejour`.`annule` = '0'
    $filterService
    $filterType
  GROUP BY `date`
  ORDER BY `date`";

foreach ($ds->loadHashList($query) as $day => $num1) {
	$days[$day]["num1"] = $num1;
}

// Liste des retours par jour
$query = "SELECT DATE_FORMAT(`affectation`.`sortie`, '%Y-%m-%d') AS `date`, COUNT(`affectation`.`affectation_id`) AS `num`
  FROM `affectation`
  LEFT JOIN sejour
    ON affectation.sejour_id = sejour.sejour_id
  LEFT JOIN lit
    ON affectation.lit_id = lit.lit_id
  LEFT JOIN chambre
    ON lit.chambre_id = chambre.chambre_id
  LEFT JOIN service
    ON chambre.service_id = service.service_id
  WHERE `affectation`.`sortie` BETWEEN '$month_min' AND '$month_max'
    AND `sejour`.`group_id` = '$group->_id'
    AND `sejour`.`annule` = '0'
    $filterService
    $filterType
  GROUP BY `date`
  ORDER BY `date`";
foreach ($ds->loadHashList($query) as $day => $num2) {
  $days[$day]["num2"] = $num2;
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("hier"         , $hier);
$smarty->assign("demain"       , $demain);

$smarty->assign("type_externe" , $type_externe);

$smarty->assign("bank_holidays", $bank_holidays);
$smarty->assign('date'         , $date);
$smarty->assign('lastmonth'    , $lastmonth);
$smarty->assign('nextmonth'    , $nextmonth);
$smarty->assign('days'         , $days);

$smarty->display('inc_vw_all_permissions.tpl');

?>