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
 * D�tail CCAM pour Sherpa, correspond � un acte CCAM Mediboard
 */
class CSpNGAP extends CSpObject {  
  // DB Table key
  var $idacte = null;

  // DB Fields : see getSpecs();

  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = 'CActeNGAP';
    $spec->table   = 'es_ngap';
    $spec->key     = 'idacte';
    return $spec;
  }
 	
  function makeId() {
    $ds = $this->getCurrentDataSource();
    $query = "SELECT MAX(`{$this->_spec->key}`) FROM {$this->_spec->table}";
    $latestId = $ds->loadResult($query);
    $this->_id = $latestId+1;
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    
    $specs["idacte"]   = "num";                  /* Num�ro d'acte                */
    $specs["idinterv"] = "num";                  /* Num�ro d'intervention        */
    $specs["numdos"]   = "numchar length|6";     /* Num�ro de dossier            */
    $specs["malnum"]   = "numchar length|6";     /* Num�ro de malade             */
    $specs["pracod"]   = "str length|3";         /* Code du praticien            */
//    $specs["prescr"]   = "str length|7";         /* Code du prescripteur         */
    $specs["datact"]   = "str length|19";        /* Date heure de l'acte         */
    $specs["codact"]   = "str maxLength|3";      /* Code Lettre                  */
    $specs["actqte"]   = "str maxLength|64";     /* Coefficient libell�          */
    $specs["quant" ]   = "float";                /* Quantit� r�elle              */
    $specs["coeff" ]   = "float";                /* Coefficient                    */
    $specs["depass"]   = "bool";                 /* D�passement d'honoraire      */
    $specs["valdep"]   = "currency";             /* Valeur du d�passement        */
    $specs["nuit"  ]   = "bool";                 /* Acte de nuit                 */
    $specs["ferie" ]   = "bool";                 /* Acte un jour f�ri�           */
    $specs["gratuit"]  = "bool";                 /* Acte gratuit                 */
    $specs["flag"]     = "str length|1";         /* Flag                         */
    $specs["datmaj"]   = "str length|19";        /* Date de derniere mise a jour */
    
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
    
    $query = "SELECT COUNT(*) FROM {$this->_spec->table} WHERE numdos = '$numdos'";
    $count = $ds->loadResult($query);

    $query = "DELETE FROM {$this->_spec->table} WHERE numdos = '$numdos'";
    $ds->exec($query);

    return $count;
  }
  
  function mapTo() {
    $acte = new CActeNGAP();
    
    return $acte;
  }
  
  function mapFrom(CMbObject &$mbObject) {
    $mbClass = $this->_spec->mbClass;
    if (!$mbObject instanceof  $mbClass) {
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
    $acte->loadRefPraticien();
    $praticien =& $acte->_ref_praticien;
    $idPraticien = CSpObjectHandler::getId400For($praticien);
    $this->pracod = $idPraticien->id400;
    
    // Contenu
    $acte->loadExecution();
    $this->datact = mbDateToLocale($acte->_execution);
    $this->codact = CSpObject::makeString($acte->code);
    $this->quant  = $acte->quantite;
    $this->actqte = $acte->coefficient;
    $this->coeff  = $acte->coefficient;

    if ($acte->demi) {
      $this->actqte .= "/2";
      $this->coeff  /= 2;
    }
    
    $this->depass = $acte->montant_depassement > 0 ? "1": "0";
    $this->valdep = $acte->montant_depassement > 0 ? $acte->montant_depassement : "0";
    $this->gratuit = $acte->_montant_facture == 0.0 ? "1" : "0";
    $this->nuit  = $acte->complement == "N" ? "1" : "0";
    $this->ferie = $acte->complement == "F" ? "1" : "0";
    $this->flag = "1";
    
    // Mise � jour
    $this->datmaj = mbDateToLocale(mbDateTime());
  }
}

?>