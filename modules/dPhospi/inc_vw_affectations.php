<?php /* $Id: vw_affectations.php 1124 2006-10-18 15:18:12Z MyttO $ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision: 1124 $
* @author Thomas Despoix
*/


/**
 * Retourne une r�f�rence sur un praticien donn�, 
 * apr�s mise en cache si n�cessaire
 */
function & getCachedPraticien($praticien_id) {
  static $listPraticiens = array();
  
  if (!array_key_exists($praticien_id, $listPraticiens)) {
    $praticien = new CMediusers;
    $praticien->load($praticien_id);
    $praticien->_ref_function =& getCachedFunction($praticien->function_id);
    $listPraticiens[$praticien_id] =& $praticien;
  }
  
  return $listPraticiens[$praticien_id];  
}

/**
 * Retourne une r�f�rence sur une fonction donn�e, 
 * apr�s mise en cache si n�cessaire
 */
function & getCachedFunction($function_id) {
  static $listFunctions = array();
  
  if (!array_key_exists($function_id, $listFunctions)) {
    $function = new CFunctions;
    $function->load($function_id);
    $listFunctions[$function_id] =& $function;
  }
  
  return $listFunctions[$function_id];  
}

/**
 * Retourne une r�f�rence sur un patient donn�, 
 * apr�s mise en cache si n�cessaire
 */
function & getCachedPatient($patient_id) {
  static $listPatients = array();
  
  if (!array_key_exists($patient_id, $listPatients)) {
    $patient = new CPatient;
    $patient->load($patient_id);
    $listPatients[$patient_id] =& $patient;
  } 
  
  return $listPatients[$patient_id];  
}

/**
 * Retourne une r�f�rence sur un lit donn�, 
 * apr�s mise en cache si n�cessaire
 */
function &getCachedLit($lit_id) {
  static $listLits = array();
  
  if (!array_key_exists($lit_id, $listLits)) {
    $lit = new CLit;
    $lit->load($lit_id);
    $lit->loadRefChambre();
    $listLits[$lit_id] =& $lit;
  }

  return $listLits[$lit_id];  
}

/**
 * Charge compl�tement un service pour l'affichage des affectations
 */
function loadServiceComplet(&$service, $date, $mode) {
  $service->loadRefsBack();
  $service->_nb_lits_dispo = 0;

  foreach ($service->_ref_chambres as $chambre_id => &$chambre) {
    $chambre->loadRefsBack();

    foreach ($chambre->_ref_lits as $lit_id => &$lit) {
      $lit->loadAffectations($date);

      foreach ($lit->_ref_affectations as $affectation_id => &$affectation) {
        if (!$affectation->effectue || $mode) {
          $affectation->loadRefSejour();
          $affectation->loadRefsAffectations();
          $affectation->checkDaysRelative($date);

          $aff_prev =& $affectation->_ref_prev;
          if ($aff_prev->affectation_id) {
            $aff_prev->_ref_lit =& getCachedLit($aff_prev->lit_id);
          }

          $aff_next =& $affectation->_ref_next;
          if ($aff_next->affectation_id) {
            $aff_next->_ref_lit =& getCachedLit($aff_next->lit_id);
          }

          $sejour =& $affectation->_ref_sejour;
          $sejour->loadRefPrestation();
          $sejour->loadRefsOperations();
          $sejour->loadNumDossier();
          $sejour->_ref_praticien =& getCachedPraticien($sejour->praticien_id);
          $sejour->_ref_patient =& getCachedPatient($sejour->patient_id);
		      // Chargement des droits CMU
          $sejour->getDroitsCMU();

          foreach($sejour->_ref_operations as $operation_id => $curr_operation) {
            $sejour->_ref_operations[$operation_id]->loadExtCodesCCAM();
          }
                    
        } else {
          unset($lit->_ref_affectations[$affectation_id]);
        }
      }
    }
    $chambre->checkChambre();
    $service->_nb_lits_dispo += $chambre->_nb_lits_dispo;
  }
}

/**
 *  Chargement des admissions � affecter
 */
function loadSejourNonAffectes($where, $order = null) {
  global $g;
  
  $leftjoin = array(
    "affectation"     => "sejour.sejour_id = affectation.sejour_id",
    "users_mediboard" => "sejour.praticien_id = users_mediboard.user_id",
    "patients"        => "sejour.patient_id = patients.patient_id"
  );
  $where["sejour.group_id"] = "= '$g'";
  
  // On enleve de l'affichage les urgences
  $where["sejour.type"] = " != 'urg'";
  
  $where[] = "affectation.affectation_id IS NULL";
  
  if($order == null){
    $order = "users_mediboard.function_id, sejour.entree_prevue, patients.nom, patients.prenom";
  }

  $sejourNonAffectes = new CSejour;
  $sejourNonAffectes = $sejourNonAffectes->loadList($where, $order, null, null, $leftjoin);

  foreach ($sejourNonAffectes as &$sejour) {
  	$sejour->loadRefPrestation();
  	$sejour->loadNumDossier();
  	$sejour->loadRefsPrescriptions();
    $sejour->_ref_praticien =& getCachedPraticien($sejour->praticien_id);
    $sejour->_ref_patient   =& getCachedPatient($sejour->patient_id);
    
    // Chargement des droits CMU
    $sejour->getDroitsCMU();
    
    // Chargement des op�rations
    $sejour->loadRefsOperations();
    foreach($sejour->_ref_operations as &$operation) {
      $operation->loadExtCodesCCAM();
    }
  }
  
  return $sejourNonAffectes;
}

?>