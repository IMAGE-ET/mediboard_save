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
 * D�tail CIM pour Sherpa, correspond � une diagnostic Mediboard
 */
class CSpDetCIM extends CSpObject {  
  // DB Table key
  var $iddiag = null;

  // DB Fields : see getSpecs();
  
	function CSpDetCIM() {
	  $this->CSpObject("es_detcim", "iddiag");    
	}
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = "CCodable";
    return $spec;
  }
 	
  function makeId() {
    $ds = $this->getCurrentDataSource();
    $query = "SELECT MAX(`$this->_tbl_key`) FROM $this->_tbl";
    $latestId = $ds->loadResult($query);
    $this->_id = $latestId+1;
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    
    $specs["idinterv"] = "num";            /* Num�ro d'intervention        */
    $specs["numdos"] = "numchar length|6"; /* Num�ro de dossier            */
    $specs["coddia"] = "code cim10";       /* code CIM du diagnostic       */
    $specs["typdia"] = "enum list|P|R|S";  /* type de diagnostic           */
    
    $specs["datmaj"] = "str length|19"   ; /* Date de derniere mise a jour */
    
		return $specs;
  }
  
  function updateFormFields() {
    $this->_view = "$this->coddia (Interv: $this->idinterv, Dossier: $this->numdos)";
  }
  
  /**
   * Supprimer d�tails ccam pour le dossier
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
    return null;
  }
  
  /**
   * Mapping pour le seul DP du s�jour Mediboard
   */
  function mapFrom(CMbObject &$mbObject) {
    $mbClass = $this->_spec->mbClass;
    if (!is_a($mbObject, $mbClass)) {
      trigger_error("mapping object should be a '$mbClass'");
    }
    
    $codable = $mbObject;
    
    // Sejour
    $codable->loadRefSejour();
    $sejour =& $codable->_ref_sejour;
    $idSejour = CSpObjectHandler::getId400For($sejour);
    $this->numdos = $idSejour->id400;
        
    // Contenu
    $this->coddia = $sejour->DP;
    $this->typdia = "P";
    
    // Mise � jour
    $this->datmaj = mbDateToLocale(mbDateTime());
  }
}

?>