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
class CSpDetCCAM extends CSpObject {  
  // DB Table key
  var $idacte = null;

  // DB Fields : see getSpecs();
  
	function CSpDetCCAM() {
	  $this->CSpObject("es_detccam", "idacte");    
	}
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = "CActeCCAM";
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
    $specs["malnum"] = "numchar length|6"; /* Num�ro de malade             */
    $specs["codpra"] = "str length|3";     /* Code du praticien            */
    $specs["codact"] = "str length|7";     /* Code CCAM                    */
    $specs["activ"] = "num length|1";      /* Activit�                     */
    $specs["phase"] = "num length|1";      /* Phase                        */
    $specs["modt1"] = "str length|1";      /* Modificateur 1               */
    $specs["modt2"] = "str length|1";      /* Modificateur 2               */
    $specs["modt3"] = "str length|1";      /* Modificateur 3               */
    $specs["modt4"] = "str length|1";      /* Modificateur 4               */
    $specs["assoc"] = "num length|1";      /* Code d'association           */
    $specs["dephon"] = "currency";         /* D�passement honoraire        */
    $specs["datact"] = "str length|19";    /* Date heure de l'acte         */
    $specs["extdoc"] = "str length|1";     /* Extension doc. anesth�sie    */
    $specs["rembex"] = "str length|1";     /* Remboursement exceptionnel   */
    $specs["codsig"] = "bool";             /* Date heure de l'acte         */
    
    $specs["datmaj"] = "str length|19"   ; /* Date de derniere mise a jour */
    
		return $specs;
  }
  
  function updateFormFields() {
    $this->_view = "$this->_id (Malade: $this->malnum, Dossier: $this->numdos)";
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
    $acte = new CActeCCAM();
    $acte->_adapt_object = true;
    
    // Op�ration
    $operation = CSpObjectHandler::getMbObjectFor("COperation", $this->idinterv);
    $acte->setObject($operation);
    
    // Execution
    $operation->loadRefPlageOp();
    $acte->execution = $operation->_datetime_reel;

    // Executant
	  $executant = CSpObjectHandler::getMbObjectFor("CMediusers", $this->codpra);        
    $acte->executant_id = $executant->_id;
    
    // Contenu
    $acte->code_acte     = $this->codact;
    $acte->code_activite = $this->activ;
    $acte->code_phase    = $this->phase;
    $acte->modificateurs = trim("$this->modt1$this->modt2$this->modt3$this->modt4");
    $acte->code_association = $this->assoc;

    return $acte;
  }
  
  function mapFrom(CMbObject &$mbObject) {
    $mbClass = $this->_spec->mbClass;
    if (!is_a($mbObject, $mbClass)) {
      trigger_error("mapping object should be a '$mbClass'");
    }
    
    $acte = $mbObject;
    
    // Sejour
    $acte->loadRefSejour();
    if ($sejour =& $acte->_ref_sejour) {
      $idSejour = CSpObjectHandler::getId400For($sejour);
      $this->numdos = $idSejour->id400;
    }
    
    // Patient
    $acte->loadRefPatient();
    $patient =& $acte->_ref_patient;
    $idPatient = CSpObjectHandler::getId400For($patient);
    $this->malnum = $idPatient->id400;
    
    // Ex�cutant
    $acte->loadRefExecutant();
    $executant =& $acte->_ref_executant;
    $idExecutant = CSpObjectHandler::getId400For($executant);
    $this->codpra = $idExecutant->id400;  
    
    // Contenu
    $this->codact = $acte->code_acte;
    $this->activ  = $acte->code_activite;
    $this->phase  = $acte->code_phase;
    $this->assoc  = $acte->code_association;
    $this->modt1 = @$acte->_modificateurs[0];
    $this->modt2 = @$acte->_modificateurs[1];
    $this->modt3 = @$acte->_modificateurs[2];
    $this->modt4 = @$acte->_modificateurs[3];   
    $this->datact = mbDateToLocale($acte->execution);
    $this->dephon = mbDateToLocale($acte->montant_depassement);
    
    // Mise � jour
    $this->datmaj = mbDateToLocale(mbDateTime());

//    mbDump($this->getProps(), $this->_class_name);
  }
}

?>