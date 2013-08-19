<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Hospi
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$date_min    = CValue::getOrSession("date_min", CMbDT::date("-1 month"));
$date_max    = CValue::getOrSession("date_max", CMbDT::date());
$service_id  = CValue::getOrSession("service_id");
$display_stat = CValue::getOrSession("display_stat", array("ouvert" => 1, "prevu" => 1, "reel" => 1, "entree" => 1));

$group = CGroups::loadCurrent();
$service = new CService();
$where = array();
$where["group_id"]  = "= '$group->_id'";
$where["cancelled"] = "= '0'";
$order = "nom";
$services = $service->loadListWithPerms(PERM_READ, $where, $order);

// Template avec �chec
$smarty = new CSmartyDP;
$smarty->assign("date_min", $date_min);
$smarty->assign("date_max", $date_max);
$smarty->assign("type", "occupation");
$smarty->assign("service_id", $service_id);
$smarty->assign("services", $services);

if (!$service_id) {
  $smarty->display("inc_form_stats.tpl");
  CAppUI::stepMessage(UI_MSG_ALERT  , "warning-hospi-stats-choose_service");
  return;
}

$ds        = CSQLDataSource::get("std");
$dates     = array();
$date_temp = $date_min;
$series    = array();

while ($date_temp <= $date_max) {
  $dates[] = array(count($dates), CMbDT::dateToLocale($date_temp));
  $date_temp = CMbDT::date("+1 day", $date_temp);
}

// Table temporaraire de dates pour les jointures
$tab_name = CSQLDataSource::tempTableDates($date_min, $date_max);

// Nombre de lits totaux sur le service
$lit = new CLit();

$where = array();
$ljoin = array();

$ljoin["chambre"] = "chambre.chambre_id = lit.chambre_id";
$where["service_id"] = " = '$service_id'";
$where["lit.annule"] = " = '0'";

$nb_lits = $lit->countList($where, null, $ljoin);
if (!$nb_lits) {
  $smarty->display("inc_form_stats.tpl");
  CAppUI::stepMessage(UI_MSG_WARNING  , "warning-hospi-stats-no_beds");
  return;
}

// Lits ouverts (non bloqu�s - non compris les blocages des urgence)
$serie = array(
  "data" => array(),
  "label" => utf8_encode("Ouvert / Total"),
  "markers" => array("show" => true)
);

// Sauvegarde des lits ouverts par date
$lits_ouverts_par_date = array();

foreach ($dates as $key=>$_date) {
  $date = CMbDT::dateFromLocale($_date[1]);
  $query = "SELECT count(DISTINCT l.lit_id) as lits_ouverts
    FROM lit l
    JOIN affectation a ON a.lit_id = l.lit_id AND
    DATE_FORMAT(a.entree, '%Y-%m-%d') <= '$date' AND DATE_FORMAT(a.sortie, '%Y-%m-%d') >= '$date'
    AND a.sejour_id != 0
    LEFT JOIN chambre c ON c.chambre_id = l.chambre_id
    WHERE  c.service_id = '$service_id'";
  $lits_ouverts = $ds->loadResult($query);
  
  $serie['data'][] =
    array(count($serie['data']) - 0.3,
          $lits_ouverts,
          $lits_ouverts / $nb_lits);
  $lits_ouverts_par_date[$date] = $lits_ouverts;
}

// Pour les autres stats, on a besoin du nombre de lits ouverts,
// donc la calculer dans tous les cas

if (isset($display_stat["ouvert"])) {
  $series[] = $serie;
}

// Pr�vu (s�jours)
// WHERE s.service_id = '$service_id' => le service_id est pas notNull (en config)

if (isset($display_stat["prevu"])) {
  $serie = array(
    "data" => array(),
    "label" => utf8_encode("Pr�vu"),
    "markers" => array("show" => true)
  );
  
  foreach ($dates as $key=>$_date) {
    $date = CMbDT::dateFromLocale($_date[1]);
    $query = "SELECT count(sejour_id) as nb_prevu
    FROM sejour
    WHERE entree <= '$date 00:00:00' AND sortie >= '$date 00:00:00'
    AND service_id = '$service_id'";
    $prevu_count = $ds->loadResult($query);
    
    $serie["data"][] =
      array(count($serie["data"]) - 0.1,
            $prevu_count,
            isset($lits_ouverts_par_date[$date]) && $lits_ouverts_par_date[$date] != 0?
              $prevu_count / $lits_ouverts_par_date[$date] : 0);
  }
  
  $series[] = $serie;
}

// R�el (affectations)
if (isset($display_stat["reel"])) {
  $serie = array(
    "data" => array(),
    "label" => utf8_encode("R�el"),
    "markers" => array("show" => true)
  );
  
  foreach ($dates as $key=>$_date) {
    $date = CMbDT::dateFromLocale($_date[1]);
    $query = "SELECT count(affectation_id) as nb_reel
    FROM affectation d
    WHERE entree <= '$date 00:00:00' AND sortie >= '$date 00:00:00'
    AND service_id = '$service_id'";
    $reel_count = $ds->loadResult($query);
    
    $serie["data"][] =
      array(count($serie['data']) + 0.1,
            $reel_count,
            isset($lits_ouverts_par_date[$date]) && $lits_ouverts_par_date[$date] != 0 ?
              $reel_count / $lits_ouverts_par_date[$date] : 0);
  }
  
  $series[] = $serie;
}

// Entr�es dans la journ�e (nb de placements sur tous les lits sur chaque journ�e)
// Ne pas compter les blocages

if (isset($display_stat["entree"])) {
  $query = "SELECT d.date, count(affectation_id) as entrees
    FROM $tab_name d
    LEFT JOIN affectation a ON
      DATE_FORMAT(a.entree, '%Y-%m-%d') <= d.date AND DATE_FORMAT(a.sortie, '%Y-%m-%d') >= d.date
      AND a.sejour_id != 0 AND a.service_id = '$service_id'
    GROUP BY d.date
    ORDER BY d.date";
  
  $entrees_journee = $ds->loadList($query);
  
  $serie = array(
    "data" => array(),
    "label" => utf8_encode("Entr�es"),
    "markers" => array("show" => true)
  );
  
  foreach ($entrees_journee as $_entrees_by_day) {
    $serie["data"][] =
      array(count($serie['data']) + 0.3,
            $_entrees_by_day["entrees"],
            isset($lits_ouverts_par_date[$_entrees_by_day["date"]]) && $lits_ouverts_par_date[$_entrees_by_day["date"]] != 0 ?
              $_entrees_by_day["entrees"] / $lits_ouverts_par_date[$_entrees_by_day["date"]] : 0);
  }
  
  $series[] = $serie;
}

$options = CFlotrGraph::merge("bars", array(
  "title"    => utf8_encode("Taux d'occupation"),
  "xaxis"    => array("ticks" => $dates),
  "grid"     => array("verticalLines" => true),
  "bars"     => array("barWidth" => 0.15, "stacked" => true)
));

$graph = array("series" => $series, "options" => $options);

$smarty = new CSmartyDP();

$smarty->assign("date_min"    , $date_min);
$smarty->assign("date_max"    , $date_max);
$smarty->assign("services"    , $services);
$smarty->assign("graph"       , $graph);
$smarty->assign("service_id"  , $service_id);
$smarty->assign("display_stat", $display_stat);

$smarty->display("inc_vw_stats_occupation.tpl");

