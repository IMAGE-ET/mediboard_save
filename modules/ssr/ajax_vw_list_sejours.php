<?php /* $Id:  $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

$type = CValue::getOrSession("type");

// Week dates
$date = CValue::getOrSession("date", mbDate());
$monday = mbDate("last monday", mbDate("+1 DAY", $date));
$sunday = mbDate("next sunday", mbDate("-1 DAY", $date));

// Chargement des conges
$plage_conge = new CPlageConge();
$where = array();
$where["date_debut"] = "<= '$sunday'"; 
$where["date_fin"  ] = ">= '$monday'"; 
$order="date_debut DESC, date_fin DESC";
$plages_conge = $plage_conge->loadList($where, $order);

// D�but et fin d'activite
foreach (CEvenementSSR::getActiveTherapeutes($monday, $sunday) as $_therapeute) {
  // Pseudo plage de d�but
  if (($deb = $_therapeute->deb_activite) && $deb >= $monday) {
    $plage = CPlageConge::makePseudoPlage($_therapeute->_id, "deb", $monday);
    $plages_conge[$plage->_id] = $plage;
  }

  // Pseudo plage de fin
  if (($fin = $_therapeute->fin_activite) && $fin <= $sunday) {
    $plage = CPlageConge::makePseudoPlage($_therapeute->_id, "fin", $sunday);
    $plages_conge[$plage->_id] = $plage;
  }
}

$sejours = array();
$_sejours = array();
$count_evts = array();
$sejours_count = 0;

// Pour chaque plage de conge, recherche 
foreach ($plages_conge as $_plage_conge){
  $kine = $_plage_conge->loadRefUser();
  $_sejours = array();

  $date_min = max($monday, $_plage_conge->date_debut);
  $date_max = mbDate("+1 DAY", min($sunday, $_plage_conge->date_fin));
  
  // Cas des remplacements kin�s
  if ($type == "kine" && !$_plage_conge->_activite) {
    $_sejours = CBilanSSR::loadSejoursSurConges($_plage_conge, $monday, $sunday);
  }
  
  // Cas des transferts de r��ducateurs
  if ($type == "reeducateur") {
    $evenement = new CEvenementSSR();
    $where = array();
    $where["debut"] = " BETWEEN '$date_min' AND '$date_max'";
    $where["therapeute_id"] = " = '$_plage_conge->user_id'";
    $evenements = $evenement->loadList($where);
    
    foreach ($evenements as $_evenement){
      $sejour = $_evenement->loadRefSejour();
      $bilan = $sejour->loadRefBilanSSR();
      $bilan->loadRefTechnicien();
      $_sejours[$_evenement->sejour_id] = $_evenement->_ref_sejour;
    }
  }
  
  foreach ($_sejours as $_sejour) {
    // On compte le nombre d'evenements SSR � transferer
    $evenement_ssr = new CEvenementSSR();
    $where = array();
    $where["sejour_id"] = " = '$_sejour->_id'";
    $where["therapeute_id"] = " = '$_plage_conge->user_id'";
    $where["debut"] = " BETWEEN '$date_min' AND '$date_max'";
    $count_evts["$_plage_conge->_id-$_sejour->_id"] = $evenement_ssr->countList($where);
    
    $_sejour->checkDaysRelative($date);
    $_sejour->loadRefReplacement($_plage_conge->_id);
    $replacement =& $_sejour->_ref_replacement;
    if (!$replacement->_id || $type == "reeducateur") {
      $sejours_count++;
    }

    if ($replacement->_id || $type == "kine") {
      $replacement->loadRefReplacer()->loadRefFunction();
    }
  
    if (!$replacement->_id && $type == "kine") {
      $replacement->_ref_guessed_replacers = CEvenementSSR::getAllTherapeutes($_sejour->patient_id, $kine->function_id);
      unset($replacement->_ref_guessed_replacers[$kine->_id]);
    }

    // Bilan SSR
    $bilan = $_sejour->loadRefBilanSSR();;
    $bilan->loadFwdRef("technicien_id");
    
    // Kine principal
    $technicien =& $bilan->_fwd["technicien_id"];
    $technicien->loadRefKine()->loadRefFunction(); 
    
    // Patient
    $patient = $_sejour->loadRefPatient();
    $patient->loadIPP();
  }

  if (count($_sejours)) {
    $sejours[$_plage_conge->_id] = $_sejours;
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("sejours", $sejours);
$smarty->assign("sejours_count", $sejours_count);
$smarty->assign("plages_conge", $plages_conge);
$smarty->assign("type", $type);
$smarty->assign("count_evts", $count_evts);
$smarty->display("inc_vw_list_sejours.tpl");

?>