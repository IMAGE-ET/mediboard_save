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

$event_ids = CValue::post("event_ids");
$period    = CValue::post("period");
$days      = array("");

// Propagation aux autres jours
if (CValue::post("propagate")) {
  $days = array();
  
  $date = CValue::getOrSession("date", CMbDT::date());
  $monday = CMbDT::date("last monday", CMbDT::date("+1 day", $date));
  foreach (CValue::post("_days") as $_number) {
    $days[] = CMbDT::date("+$_number DAYS", $monday);
  }
}

$elts_id = explode("|", $event_ids);

foreach ($days as $day) {
  foreach ($elts_id as $_elt_id) {
    $evenement = new CEvenementSSR();
    $evenement->load($_elt_id);
    $evenement->loadRefsActesCdARR();
    $evenement->loadRefsActesCsARR();
  
    // Duplication de l'�v�nement  
    $evenement->_id = "";
    $evenement->realise = 0;
    $evenement->annule = 0;
    $evenement->debut = $day ? "$day ".CMbDT::time($evenement->debut) :  CMbDT::dateTime($period, $evenement->debut);
  
    // Cas des s�ances collectives
    if ($evenement->seance_collective_id) {
      CAppUI::displayMsg("Impossible de dupliquer des �v�nements qui sont dans des seances collectives", "CEvenementSSR-msg-create");
      continue;
    } 
  
    // Autres r��ducateurs
    global $can;
    $user = CAppUI::$user;
    $therapeute = $evenement->loadRefTherapeute();
    if ($therapeute->function_id !=  $user->function_id && !$can->admin) {
      CAppUI::displayMsg("Impossible de dupliquer les �v�nements d'un r��ducateur d'une autre sp�ciali�", "CEvenementSSR-msg-create");
      continue;
    }
  
    // Chargements pr�paratoire au transferts automatiques de r��ducateurs  
    $sejour = new CSejour;
    $sejour->load($evenement->sejour_id);
    
    $bilan = $sejour->loadRefBilanSSR();
    $referant = $bilan->loadRefKineReferent();
    $_day = CMbDT::date($evenement->debut);
    $therapeute_id = $evenement->therapeute_id;
    
    // Transfert kin� r�f�rent => kin� rempla�ant si disponible
    if ($therapeute_id == $referant->_id) {
      $conge = new CPlageConge();
      $conge->loadFor($therapeute_id, $_day);
      // R�f�rent en cong�s
      if ($conge->_id) {
        $replacement = new CReplacement();
        $replacement->conge_id = $conge->_id;
        $replacement->sejour_id = $sejour->_id;
        $replacement->loadMatchingObject();
        if ($replacement->_id) {
          $evenement->therapeute_id = $replacement->replacer_id;
        }
      }
    }
  
    // Transfert kin� remplacant => kin� r�f�rant si pr�sent
    if ($sejour->isReplacer($therapeute_id)) {
      $conge = new CPlageConge();
      $conge->loadFor($referant->_id, $_day);
      // R�f�rent pr�sent
      if (!$conge->_id) {
        $evenement->therapeute_id = $referant->_id;
      }
    }
    
    $msg = $evenement->store();
    CAppUI::displayMsg($msg, "CEvenementSSR-msg-create");
  
    // Duplication des codes CdARR
    if ($evenement->_id) {
      foreach ($evenement->_ref_actes_cdarr as $_acte) {
        $_acte->_id = "";
        $_acte->evenement_ssr_id = $evenement->_id;
        $msg = $_acte->store();
        CAppUI::displayMsg($msg, "CActeCdARR-msg-create");
      }
      foreach ($evenement->_ref_actes_csarr as $_acte) {
        $_acte->_id = "";
        $_acte->evenement_ssr_id = $evenement->_id;
        $msg = $_acte->store();
        CAppUI::displayMsg($msg, "CActeCdARR-msg-create");
      }
    }
  }
}

echo CAppUI::getMsg();
CApp::rip();
