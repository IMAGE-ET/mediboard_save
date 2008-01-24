<?php /* $Id: patients.class.php 2249 2007-07-11 16:00:10Z mytto $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 2249 $
* @author Romain Ollivier
*/

global $AppUI;
require_once($AppUI->getModuleClass("sherpa", "spObject"));

/**
 * Ent�te CCAM pour Sherpa, correspond � une intervention Mediboard
 */
class CSpEntCCAM extends CSpObject {  
  // DB Table key
  var $idinterv = null;

  // DB Fields : see getSpecs();
  
	function CSpEntCCAM() {
	  $this->CSpObject("es_entccam", "idinterv");    
	}
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = "CCodable";
    return $spec;
  }
 	
  function getSpecs() {
    $specs = parent::getSpecs();
    
    $specs["numdos"] = "numchar length|6"; /* Num�ro de dossier            */
    $specs["malnum"] = "numchar length|6"; /* Num�ro de malade             */
    $specs["debint"] = "str length|19";    /* D�but intervention           */
    $specs["finint"] = "str length|19";    /* Fin intervention             */
    $specs["datope"] = "str length|19";    /* Entr�e en salle              */
    $specs["finope"] = "str length|19";    /* Sortie de salle              */
    $specs["pracod"] = "str length|3";     /* Code du chirugien            */
    $specs["codane"] = "str length|3";     /* Code de l'anesth�siste       */
    $specs["codsal"] = "str length|2";     /* Code de la salle d'op        */

    for ($i = 1; $i <= 3; $i++) {
	    $specs["aidop$i"] = "str length|3";    /* Code aide op�ratoire        */
	    $specs["dhaid$i"] = "str length|19";   /* D�but aide op�ratoire        */
	    $specs["fhaid$i"] = "str length|19";   /* Aide aide op�ratoire        */
    }

    $specs["datmaj"] = "str length|19"   ; /* Date de derniere mise a jour */
    
		return $specs;
  }
  
  function updateFormFields() {
    $this->_view = "$this->_id (Malade: $this->malnum, Dossier: $this->numdos)";
  }
  
  function makeId(CCodable $codable) {
    if (is_a($codable,  "CSejour")) {
      $this->_id = "0";
      return;
    }
      
    $ds = $this->getCurrentDataSource();
    $query = "SELECT MAX(`$this->_tbl_key`) FROM $this->_tbl";
    $latestId = $ds->loadResult($query);
    $this->_id = $latestId+1;
  }

  /**
   * Supprime ent�tes CCAM pour le dossier
   */
  function deleteForDossier($numdos) {
    $ds = $this->getCurrentDataSource();

    $query = "SELECT COUNT(*) FROM $this->_tbl WHERE numdos = '$numdos'";
    $count = $ds->loadResult($query);

    $query = "DELETE FROM $this->_tbl WHERE numdos = '$numdos'";
    $ds->exec($query);

    return $count;
  }
  
  function mapTo() {
    $operation = new COperation();
    $operation->cote = "inconnu";
    
    // Sejour
    $sejour = CSpObjectHandler::getMbObjectFor("CSejour", $this->numdos);
    $operation->sejour_id = $sejour->_id;
    
    // Chirurgien
    $operation->chir_id = $sejour->praticien_id;
    if ($this->pracod) {
	    $chirurgien = CSpObjectHandler::getMbObjectFor("CMediusers", $this->pracod);        
	    $operation->chir_id = $chirurgien->_id;
    }
    
    // Anesth�siste
    $anesthesiste = CSpObjectHandler::getMbObjectFor("CMediusers", $this->codane);        
    $operation->anesth_id = $anesthesiste->_id;
    
    // Horodatage
    $deb = mbDateFromLocale($this->debint);
    $fin = mbDateFromLocale($this->finint);
    
    $operation->date = mbDate($deb);
    $operation->debut_op = mbTime($deb);
    $operation->fin_op   = mbTime($fin);
    $operation->temp_operation = mbTimeRelative($operation->debut_op, $operation->fin_op);
    
    return $operation;
  }
  
  function mapFrom(CMbObject &$mbObject) {
    $mbClass = $this->_spec->mbClass;
    if (!is_a($mbObject, $mbClass)) {
      trigger_error("mapping object should be a '$mbClass'");
    }
    
    $codable = $mbObject;
    
    // Sejour
    $codable->loadRefSejour();
    $idSejour = CSpObjectHandler::getId400For($codable->_ref_sejour);
    $this->numdos = $idSejour->id400;
    
    // Patient
    $codable->loadRefPatient();
    $idPatient = CSpObjectHandler::getId400For($codable->_ref_patient);
    $this->malnum = $idPatient->id400;
    
    // Dates
    switch ($mbObject->_class_name) {
      case "COperation":
      $operation =& $mbObject;
	    $operation->loadRefPlageOp();
	    $date = mbDate($operation->_datetime);
	    
	    // Op�ration
	    $deb_op = mbGetValue($operation->debut_op, "00:00:00");
	    $fin_op = mbGetValue($operation->fin_op  , "00:00:00");
	    $this->datope = mbDateToLocale("$date $deb_op");
	    $this->finope = mbDateToLocale("$date $fin_op");
	    
	    // Intervention = salle
	    $deb_int = mbGetValue($operation->entree_salle, "00:00:00");
	    $fin_int = mbGetValue($operation->sortie_salle, "00:00:00");
	    $this->debint = mbDateToLocale("$date $deb_int");
	    $this->finint = mbDateToLocale("$date $fin_int");
	    break;
    }
    
    // Chirurgien
    $mbObject->loadRefPraticien();
    $idPrat = CSpObjectHandler::getId400For($mbObject->_ref_praticien);
    $this->pracod = $idPrat->id400;
	    
    // Anesth�siste 
    switch ($mbObject->_class_name) {
      case "COperation":
      $operation =& $mbObject;

      // D�j� charg� par la plage op
	    if ($operation->anesth_id) {
		    $idAnesth = CSpObjectHandler::getId400For($operation->_ref_anesth);
		    $this->codane = $idAnesth->id400;
	    }
	    
      break;
    }
	    
    // Salle de l'intervention
    switch ($mbObject->_class_name) {
      case "COperation":
      $operation =& $mbObject;
	    if ($operation->_ref_salle->_id) {
		    $idSalle = CSpObjectHandler::getId400For($operation->_ref_salle);
		    $this->codsal = $idSalle->id400;
	    }
	    
      break;
    }
	    
    // Aides op�ratoire et panseuse
    $mbObject->loadAffectationsPersonnel();
    $affectations_op = $mbObject->_ref_affectations_personnel["op"];
		$affectations_panseuse = $mbObject->_ref_affectation_personnel["panseuse"];
		if(!$affectations_op){
		  $affectations_op = array();
		}
    if(!$affectations_panseuse){
		  $affectations_panseuse = array();
		}
		// Fusion des deux tableaux d'affectations
		$affectations = array_merge($affectations_op, $affectations_panseuse);
	
    $aidopNumber = 0;
    foreach ($affectations as $affectationPersonnel) {
      if (++$aidopNumber <=  3) {
        $affectationPersonnel->loadRefPersonnel();
        $personnel = $affectationPersonnel->_ref_personnel;
        $personnel->loadRefUser();
        
        $idAidop = CSpObjectHandler::getId400For($personnel->_ref_user);
        $aidopField = "aidop$aidopNumber";
        $dhaidField = "dhaid$aidopNumber";
        $fhaidField = "fhaid$aidopNumber";
        $this->$aidopField = $idAidop->id400;
        $this->$dhaidField = mbDateToLocale($affectationPersonnel->debut);
        $this->$fhaidField = mbDateToLocale($affectationPersonnel->fin);
      }
    }

    // Mise � jour
    $this->datmaj = mbDateToLocale(mbDateTime());
  }
}

?>