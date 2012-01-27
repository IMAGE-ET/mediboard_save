<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$operation_id = CValue::get("operation_id");

$interv = new COperation;
$interv->load($operation_id);
$interv->loadRefSejour()->loadRefPatient()->loadRefConstantesMedicales();
$interv->loadRefPlageOp();

$consult_anesth = $interv->loadRefsConsultAnesth();

list($results, $times) = CObservationResultSet::getResultsFor($interv);

global $time_min, $time_max;

$time_min = $interv->entree_salle;
$time_max = mbTime("+".mbMinutesRelative("00:00:00", $interv->temp_operation)." MINUTES", $interv->entree_salle);

$date = mbDate($interv->_datetime);

$time_debut_op_iso = "$date $time_min";
$time_debut_op     = CMbDate::toUTCTimestamp($time_debut_op_iso);

$time_fin_op_iso   = "$date $time_max";
$time_fin_op       = CMbDate::toUTCTimestamp($time_fin_op_iso);

$round_minutes = 10;
$round = $round_minutes * 60000;

$time_min = floor(CMbDate::toUTCTimestamp("$date $time_min") / $round) * $round;
$time_max =  ceil(CMbDate::toUTCTimestamp("$date $time_max") / $round) * $round;

$graph_object = new CSupervisionGraph;
$graph_objects = $graph_object->loadList(array(
  "disabled" => "= '0'",
));

$graphs = array();
foreach($graph_objects as $_go) {
  $graphs[] = $_go->buildGraph($results, $time_min, $time_max);;
}

$yaxes_count = 0;
foreach($graphs as $_graph) {
  $yaxes_count = max($yaxes_count, count($_graph["yaxes"]));
}

foreach($graphs as &$_graph) {
  if (count($_graph["yaxes"]) < $yaxes_count) {
    $_graph["yaxes"] = array_pad($_graph["yaxes"], $yaxes_count, CSupervisionGraphAxis::$default_yaxis);
  }
}

function getPosition($datetime){
  global $time_min, $time_max;
  return 100 * (CMbDate::toUTCTimestamp($datetime) - $time_min) / ($time_max - $time_min);
}

function getWidth($datetime_start, $datetime_end){
  global $time_min, $time_max;
  $delta = strtotime($datetime_end) - strtotime($datetime_start);
  return 100 * ($delta*1000) / ($time_max - $time_min);
}

// ---------------------------------------------------
// Gestes, Medicaments, Perfusions peranesth
$gestes = array(
  "CAffectationPersonnel" => array(),
  "CAnesthPerop" => array(),
);

// Personnel de l'interv
$interv->loadAffectationsPersonnel();
foreach ($interv->_ref_affectations_personnel as $emplacement => $affectations) {
  foreach($affectations as $_affectation) {
    if (!$_affectation->debut || !$_affectation->fin) continue;
    
    $gestes["CAffectationPersonnel"][$_affectation->_id] = array(
      "icon" => null,
      "label" => $_affectation->_ref_personnel,
      "unit"  => null,
      "alert" => false,
      "datetime" => $_affectation->debut,
      "position" => getPosition($_affectation->debut),
      "width" => getWidth($_affectation->debut, $_affectation->fin),
      "object" => $_affectation,
    );
  }
}

// Personnel de la plage
$plageop = $interv->_ref_plageop;
$plageop->loadAffectationsPersonnel();
foreach ($plageop->_ref_affectations_personnel as $emplacement => $affectations) {
  foreach($affectations as $_affectation) {
    if (!$_affectation->debut || !$_affectation->fin) continue;
    
    $gestes["CAffectationPersonnel"][$_affectation->_id] = array(
      "icon" => null,
      "label" => $_affectation->_ref_personnel,
      "unit"  => null,
      "alert" => false,
      "datetime" => $_affectation->debut,
      "position" => getPosition($_affectation->debut),
      "width" => getWidth($_affectation->debut, $_affectation->fin),
      "object" => $_affectation,
    );
  }
}

// Gestes perop
$interv->loadRefsAnesthPerops();
foreach($interv->_ref_anesth_perops as $_perop) {
  $gestes["CAnesthPerop"][$_perop->_id] = array(
    "icon" => null,
    "label" => $_perop->libelle,
    "unit"  => null,
    "alert" => $_perop->incident,
    "datetime" => $_perop->datetime,
    "position" => getPosition($_perop->datetime),
    "object" => $_perop,
  );
}

// Lignes de medicaments et d'elements
$sejour = $interv->loadRefSejour();
$prescription = $sejour->loadRefPrescriptionSejour();

if($prescription->_id){
  $lines = $prescription->loadPeropLines(false);
  
  foreach($lines as $_guid => $_line_array) {
    $_line = $_line_array["object"];
    
    $_view = "";
    
    if ($_line instanceof CPrescriptionLineElement) {
      $_view = $_line->_view;
    }
    elseif ($_line instanceof CPrescriptionLineMix) {
      foreach ($_line->_ref_lines as $_mix_item) {
        $_view .= "$_mix_item->_ucd_view<br />";
      }
    }
    else {
      $_view = $_line->_ucd_view;
    }
    
    $key = "CPrescription._chapitres.$_line->_chapitre";
    if (!isset($gestes[$key])) {
      $gestes[$key] = array();
    }
    
    /*
    foreach($_line_array["planifications"] as $_planifs) {
      foreach($_planifs as $_planif) {
        if ($_planif->_ref_object instanceof CPrescriptionLineMixItem) {
          $quantite = $_planif->_ref_object->_quantite_administration;
        }
        else {
          $quantite = $_planif->_ref_prise->_quantite_administrable;
        }
        
        if ($_line instanceof CPrescriptionLineMedicament || $_line instanceof CPrescriptionLineMix) {
          $unite = $_planif->_ref_object->_ref_produit->libelle_unite_presentation;
        }
        else {
          $unite = $_line->_unite_prise;
        }

        $gestes[$_line->_class][] = array(
          "label" => "$quantite $unite",
          "alert" => false,
          "datetime" => $_planif->dateTime,
          "position" => 100 * (CMbDate::toUTCTimestamp($_planif->dateTime) - $time_min) / ($time_max - $time_min),
        );
      }
    }*/
   
    foreach ($_line_array["administrations"] as $_adms) {
      $_adms = CStoredObject::naturalSort($_adms, array("dateTime"));
      
      foreach ($_adms as $_adm) {
        $unite = "";
        if ($_line instanceof CPrescriptionLineMedicament || $_line instanceof CPrescriptionLineMix) {
          $unite = $_adm->_ref_object->_ref_produit->libelle_unite_presentation;
        }
        
        $gestes[$key][] = array(
          "icon"  => $_line->_chapitre,
          "label" => $_view,
          "unit"  => "$_adm->quantite $unite",
          "alert" => false,
          "datetime" => $_adm->dateTime,
          "position" => getPosition($_adm->dateTime),
          "object"   => $_line,
        );
      }
    }
  }
}

$now = 100 * (CMbDate::toUTCTimestamp(mbDateTime()) - $time_min) / ($time_max - $time_min);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("interv",      $interv);
$smarty->assign("graphs",      $graphs);
$smarty->assign("gestes",      $gestes);
$smarty->assign("time_debut_op", $time_debut_op);
$smarty->assign("time_fin_op",   $time_fin_op);
$smarty->assign("yaxes_count", $yaxes_count);
$smarty->assign("consult_anesth", $consult_anesth);
$smarty->assign("now", $now);
$smarty->assign("time_debut_op_iso", $time_debut_op_iso);
$smarty->assign("time_fin_op_iso",   $time_fin_op_iso);

$smarty->display("inc_vw_surveillance_perop.tpl");
