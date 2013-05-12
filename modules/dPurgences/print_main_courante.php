<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Urgences
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$offline = CValue::get("offline");

// Chargement des rpu de la main courante
$sejour = new CSejour;

$where = array();
$ljoin["rpu"] = "sejour.sejour_id = rpu.sejour_id";

// Par date
$date = CValue::getOrSession("date", CMbDT::date());
$date_tolerance = CAppUI::conf("dPurgences date_tolerance");
$date_before = CMbDT::date("-$date_tolerance DAY", $date);
$date_after  = CMbDT::date("+1 DAY", $date);
$where[] = "sejour.entree BETWEEN '$date' AND '$date_after'
  OR (sejour.sortie_reelle IS NULL AND sejour.entree BETWEEN '$date_before' AND '$date_after')";

// RPUs
$where[] = CAppUI::pref("showMissingRPU") ?
  "sejour.type = 'urg' OR rpu.rpu_id IS NOT NULL" :
  "rpu.rpu_id IS NOT NULL";
$where["sejour.group_id"] = "= '".CGroups::loadCurrent()->_id."'";

$order = "sejour.entree ASC";

/** @var CSejour[] $sejours */
$sejours = $sejour->loadList($where, $order, null, null, $ljoin);

$stats = array (
  "entree" => array (
    "total" => 0,
    "less_than_1" => 0,
    "more_than_75" => 0,
),
  "sortie" => array (
    "total" => 0,
    "transferts_count" => 0, 
    "mutations_count" => 0,
    "etablissements_transfert" => array(),
    "services_mutation" => array(),
),
);

$offlines = array();

// D�tail du chargement
foreach ($sejours as &$_sejour) {
  $_sejour->loadRefsFwd(1);
  $_sejour->_ref_patient->loadIPP();
  $_sejour->loadRefRPU();
  $_sejour->_ref_rpu->loadRefSejourMutation();
  $_sejour->_veille = CMbDT::date($_sejour->entree) != $date;
  
  // Statistiques de sortie
  if (CMbDT::date($_sejour->sortie) == $date) {
    $stats["sortie"]["total"]++;

    // Statistiques de mutations de sejours
    $service_mutation = $_sejour->_ref_service_mutation;
    if ($service_mutation->_id) {
      $stats["sortie"]["mutations_count"]++;
      $stat_service =& $stats["sortie"]["services_mutation"][$service_mutation->_id];
      if (!isset($stat_service)) {
        $stat_service = array(
          "ref" => $service_mutation,
          "count" => 0
        );
      }
      $stat_service["count"]++;
    }

    // Statistiques de transferts de sejours
    $etablissement_tranfert = $_sejour->_ref_etablissement_transfert;
    if ($etablissement_tranfert->_id) {
      $stats["sortie"]["transferts_count"]++;
      $stat_etablissement =& $stats["sortie"]["etablissements_transfert"][$etablissement_tranfert->_id];
      if (!isset($stat_etablissement)) {
        $stat_etablissement = array(
          "ref" => $etablissement_tranfert,
          "count" => 0
        );
      }
      $stat_etablissement["count"]++;
    }
  }

  // Statistiques d'entr�e
  if (CMbDT::date($_sejour->entree) == $date) {
    $stats["entree"]["total"]++;

    // Statistiques  d'�ge de patient
    $patient =& $_sejour->_ref_patient;
    if ($patient->_annees < "1") {
      $stats["entree"]["less_than_1"]++;
    }
     
    if ($patient->_annees >= "75") {
      $stats["entree"]["more_than_75"]++;
    }
  }

  // Chargement n�cessaire du mode offline
  if ($offline) {
    $params = array(
        "rpu_id" => $_sejour->_ref_rpu->_id,
        "dialog" => 1,
        "offline" => 1,
      );
    $offlines[$_sejour->_id] = CApp::fetch("dPurgences", "print_dossier", $params);
  }
}


// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("date"         , $date);
$smarty->assign("stats"        , $stats);
$smarty->assign("sejours"      , $sejours);
$smarty->assign("offline"      , $offline);
$smarty->assign("dateTime"     , CMbDT::dateTime());
$smarty->assign("offlines"     , $offlines);

$smarty->display("print_main_courante.tpl");
