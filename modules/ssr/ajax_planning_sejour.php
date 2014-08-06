<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */


CCanDo::checkRead();

$sejour = new CSejour;
$sejour->load(CValue::get("sejour_id"));
$sejour->loadRefPatient();

$selectable = CValue::get("selectable");
$patient = $sejour->_ref_patient;

$height = CValue::get("height");
$print = CValue::get("print");
$large = CValue::get("large");

// Initialisation du planning
$date = CValue::getOrSession("date", CMbDT::date());
$nb_days_planning = $sejour->getNbJourPlanning($date);

$planning = new CPlanningWeek($date, $sejour->entree, $sejour->sortie, $nb_days_planning, $selectable, $height, $large, !$print);
$planning->title = "Patient '$patient->_view'";
$planning->guid = $sejour->_guid;

// Chargement des evenement SSR (ainsi que les seances collectives) 
$evenement = new CEvenementSSR();
$ljoin = array();
$ljoin[] = "evenement_ssr AS evt_seance ON (evt_seance.seance_collective_id = evenement_ssr.evenement_ssr_id)";
$where = array();
$where[] = "(evenement_ssr.sejour_id = '$sejour->_id') OR (evenement_ssr.sejour_id IS NULL AND evt_seance.sejour_id = '$sejour->_id')";
$where["evenement_ssr.debut"] = "BETWEEN '$planning->_date_min_planning 00:00:00' AND '$planning->_date_max_planning 23:59:59'";

/** @var CEvenementSSR[] $evenements */
$evenements = $evenement->loadList($where, null, null, null, $ljoin);
foreach ($evenements as $_evenement) {
  if (!$_evenement->sejour_id) {
    // Chargement de l'evenement pour ce sejour
    $evt = new CEvenementSSR();
    $evt->sejour_id = $sejour->_id;
    $evt->seance_collective_id = $_evenement->_id;
    $evt->loadMatchingObject();
    
    // On reaffecte les valeurs indispensables a l'affichage
    $evt->debut = $_evenement->debut;
    $evt->duree = $_evenement->duree;

    $draggable_guid = $_evenement->_guid;

    // Remplacement de la seance collective par le bon evenement    
    $_evenement = $evt;  
  }
  else {
    $draggable_guid = $_evenement->_guid;
  }
  
  // CSS Classes
  $class = $_evenement->equipement_id ? "equipement" : "kine";
  if ($_evenement->seance_collective_id) {
    $class = "seance";
  }
  
  if (!$_evenement->countBackRefs("actes_cdarr") && !$_evenement->countBackRefs("actes_csarr") && !$print) {
    $class = "zero-actes";
  }
  
  if ($_evenement->realise && !$print) {
    $class = "realise";
  }
  
  if ($_evenement->annule && !$print) {
    $class = "annule";
  }
    
  $css_classes = array();
  $css_classes[] = $class;

  // Title 
  $therapeute = $_evenement->loadRefTherapeute();
  $title = $therapeute->_view;
  
  // Color
  $function = $therapeute->loadRefFunction();
  $color = "#$function->color";
  
  // Title and color in prescription case
  if ($line = $_evenement->loadRefPrescriptionLineElement()) {
    $element = $line->_ref_element_prescription;
    $category = $element->loadRefCategory();
    $title = $category->_view;
  
    // Color
    $color = $element->_color ? "#$element->_color" : null;
    
    // CSS Class
    $css_classes[] = $element->_guid; 
    $css_classes[] = $category->_guid;
  }
  
  // Title Equipement
  if ($print) {
    $equipement = $_evenement->loadRefEquipement();
    $title .= $equipement->_id ? " - ". $equipement->_view : '';
    $title .= $_evenement->remarque ? "\n ".$_evenement->remarque : ''; 
  }
  
  // Instanciation
  $event = new CPlanningEvent(
    $_evenement->_guid,
    $_evenement->debut,
    $_evenement->duree,
    $title,
    $color,
    true,
    $css_classes,
    $draggable_guid
  );
  $event->draggable = (CAppUI::pref("ssr_planning_dragndrop") == 1) && !$_evenement->realise && !$print;
  $event->resizable = (CAppUI::pref("ssr_planning_resize") == 1)    && !$_evenement->realise && !$print;
  $planning->addEvent($event);
}

$planning->showNow();
$planning->rearrange(true);


// Alertes s�jour
$total_evenement = array();
foreach ($evenements as $_evenement) {
  $_date = CMbDT::date($_evenement->debut);
  if (!isset($total_evenement[$_date])) {
    $total_evenement[$_date]["duree"] = 0;
    $total_evenement[$_date]["nb"] = 0;
  }
  $total_evenement[$_date]["duree"] += $_evenement->duree;
  $total_evenement[$_date]["nb"]++;
}

foreach ($total_evenement as $_date => $_total) {
  $alerts = array();
  if ($_total["duree"] < 120) {
    $alerts[] = "< 2h";
  }
  if ($_total["nb"] < 1) {
    $alerts[] = "0 indiv. ";
  }
  if ($count = count($alerts)) {
    $color = ($count == 2) ? "#f88" : "#ff4";
    $planning->addDayLabel($_date, implode(" / ", $alerts) , null, $color);
  }
}

foreach ($sejour->loadRefReplacements() as $_replacement) {
  if ($_replacement->_id) {
    $_replacement->loadRefReplacer();
    $_replacement->loadRefConge();
    $conge =& $_replacement->_ref_conge;
    
    for ($day = $conge->date_debut; $day <= $conge->date_fin; $day = CMbDT::date("+1 DAY", $day)) {
      $planning->addDayLabel($day, $_replacement->_ref_replacer->_view);
    } 
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("planning", $planning);
$smarty->display("inc_planning_sejour.tpl");
