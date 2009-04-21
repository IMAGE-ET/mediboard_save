<?php

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Sherpa
*/

class CSpActesExporter {
  static $detCCAM = array();
  static $detCIM  = array();
	static $entCCAM = array();
	static $actNGAP = array();
	
	static $delDetCCAM = array();
  static $delDetCIM  = array();
  static $delEntCCAM = array();
  static $delActNGAP = array();
  
  /**
   * Export du s�jour complet
   */
  static function exportSejour(CSejour &$sejour) {
	  $sejour->loadRefPatient();
	  $sejour->loadRefPraticien();
	  
	  // Suppression des actes
	  $sejour->loadNumDossier();
	  if ($sejour->_num_dossier == "-") {
      trigger_error("Pas de num�ro de dossier pour le s�jour [$sejour->_id] '$sejour->_view''", E_USER_WARNING);
	    return;
	  }
	  
	  // Suppression des anciens d�tails CCAM
	  CSpActesExporter::deleteForDossier($sejour);
	      
	  // Actes du s�jour
	  $sejour->loadRefsActes();
	  CSpActesExporter::exportEntCCAM($sejour);
	  CSpActesExporter::exportDetsCIM($sejour, "0");
	  
	  // Op�rations
	  $sejour->loadRefsOperations();
	  foreach ($sejour->_ref_operations as &$operation) {
	    CSpActesExporter::exportCodable($operation);
	  }
	  
	  // Op�rations
	  $sejour->loadRefsConsultations();
	  CSpActesExporter::exportCodable($sejour->_ref_consult_atu);    
  }
  
  static function exportCodable(CCodable &$codable) {
    if (!$codable->_id) {
      return;
    }
    
    $codable->_ref_sejour =& $sejour;
    $codable->loadRefPraticien();
    $codable->loadRefsActes();
    
    CSpActesExporter::exportEntCCAM($codable);
    
    if ($codable instanceof COperation) {
	    CSpActesExporter::exportInfoCIM($codable, "anapath");
	    CSpActesExporter::exportInfoCIM($codable, "labo");
    }
    
    // Association d'un id400
    $idCodable = CSpObjectHandler::getId400For($codable);
    if (!$idCodable->_id) {
      $idCodable->id400 = $codable->_idinterv;
      $idCodable->last_update = mbDateTime();
      if ($msg = $idCodable->store()) {
        trigger_error("Impossible de cr�er un idenfiant externe pour le codable de type '$codable->_class_name' : $msg", E_USER_WARNING);
        break;
      }
    }
  }
  
  /**
   * Associations entre actes CCAM Mediboard et les d�tails CCAM Sherpa
   */
	static function exportDetCCAM(CActeCCAM &$acte_ccam, $idinterv) {
	  $spDetCCAM = new CSpDetCCAM();
	  $spDetCCAM->makeId();
	  $spDetCCAM->idinterv = $idinterv;
	  $spDetCCAM->mapFrom($acte_ccam);
	  $spDetCCAM->getCurrentDataSource();
	  self::$detCCAM[$acte_ccam->_id] = $spDetCCAM->store();
	}
  

  /**
   * Associations entre actes CCAM Mediboard et les d�tails CCAM Sherpa
   */
	static function exportActNGAP(CActeNGAP &$acte_ngap, $idinterv) {
	  $spNGAP = new CSpNGAP();
	  $spNGAP->makeId();
	  $spNGAP->idinterv = $idinterv;
	  $spNGAP->mapFrom($acte_ngap);
	  $spNGAP->getCurrentDataSource();
	  self::$actNGAP[$acte_ngap->_id] = $spNGAP->store();
	}
	
	/**
	 * Associations diagnostics CIM Mediboard et les d�tails CIM Sherpa
	 */
	static function exportDetsCIM(CSejour &$sejour) {
	  if (!isset(self::$detCIM[$sejour->_class_name])) {
	    self::$detCIM[$sejour->_class_name] = array();
	  }
	  
	  $spDetCIM = new CSpDetCIM();
	  
	  if ($sejour->DP) {
		  $spDetCIM->makeId();
		  $spDetCIM->mapFrom($sejour);
		  $spDetCIM->idinterv = $sejour->_idinterv;
		  $spDetCIM->getCurrentDataSource();
		
		  self::$detCIM[$sejour->_class_name][$sejour->_id][] = $spDetCIM->store();
	  }
	  
	  // Diagnostic reli�
	  if ($sejour->DR) {
	    $spDetCIM->makeId();
	    $spDetCIM->typdia = "R";
	    $spDetCIM->coddia = CSpObject::makeString($sejour->DR);
	    $spDetCIM->datmaj = mbDateToLocale(mbDateTime());
	    self::$detCIM[$sejour->_class_name][$sejour->_id][] = $spDetCIM->store();
	  }
	  
	  // Diagnostics associ�s
	  $sejour->loadRefDossierMedical();
	  if ($sejour->_ref_dossier_medical->_codes_cim) {
		  foreach ($sejour->_ref_dossier_medical->_codes_cim as $code_cim) {
		    $spDetCIM->makeId();
		    $spDetCIM->typdia = "S";
		    $spDetCIM->coddia = CSpObject::makeString($code_cim);
		    $spDetCIM->datmaj = mbDateToLocale(mbDateTime());
		    self::$detCIM[$sejour->_class_name][$sejour->_id][] = $spDetCIM->store();
		  }
	  }
	}
	
	/**
	 * Association des info CIM
	 */
	static function exportInfoCIM(COperation &$operation, $key) {
	  if (!isset(self::$detCIM[$operation->_class_name])) {
	    self::$detCIM[$operation->_class_name] = array();
	  }
	  
	  if (!$operation->$key) {
	    return;
	  }
	  
	  $spDetCIM = new CSpDetCIM();
	  $spDetCIM->makeId();
	  $spDetCIM->mapFrom($operation);
	  $spDetCIM->idinterv = $operation->_idinterv;
	  $spDetCIM->getCurrentDataSource();
	  $spDetCIM->typdia = "S";
	  $spDetCIM->coddia = CSpObject::makeString($key);
	
	  self::$detCIM[$operation->_class_name][$operation->_id][] = $spDetCIM->store();
	}
	
	/**
	 * Associations entre codable Mediboard et les ent�tes CCAM Sherpa
	 */
	static function exportEntCCAM(CCodable &$codable) {
	  $spEntCCAM = new CSpEntCCAM();
	  $spEntCCAM->makeId($codable);
	  $spEntCCAM->mapFrom($codable);
	  $spEntCCAM->getCurrentDataSource();
	  self::$entCCAM[$codable->_class_name][$codable->_id] = $spEntCCAM->store();
	  
	  foreach ($codable->_ref_actes_ccam as &$acte_ccam) {
	    self::exportDetCCAM($acte_ccam, $spEntCCAM->_id);
	  }

	  foreach ($codable->_ref_actes_ngap as &$acte_ngap) {
	    self::exportActNGAP($acte_ngap, $spEntCCAM->_id);
	  }
	  
	  $codable->_idinterv = $spEntCCAM->_id;
	}
	
	/**
	 * Purge des actes et diagnostics pour un dossier donn�
	 */
	static function deleteForDossier(CSejour $sejour) {
	  // Suppression des anciens d�tails CCAM
	  $spDetCCAM = new CSpDetCCAM();
	  self::$delDetCCAM[$sejour->_id] = $spDetCCAM->deleteForDossier($sejour->_num_dossier);
	  
	  // Suppression des anciens actes NGAP
	  $spNGAP = new CSpNGAP();
	  self::$delActNGAP[$sejour->_id] = $spNGAP->deleteForDossier($sejour->_num_dossier);
	  
	  // Suppression des anciens d�tails CIM
	  $spDetCIM = new CSpDetCIM();
	  self::$delDetCIM[$sejour->_id] = $spDetCIM->deleteForDossier($sejour->_num_dossier);
	  
	  // Suppression des anciens ent�tes
	  $spEntCCAM = new CSpEntCCAM();
	  self::$delEntCCAM[$sejour->_id] = $spEntCCAM->deleteForDossier($sejour->_num_dossier);
	}
}

?>