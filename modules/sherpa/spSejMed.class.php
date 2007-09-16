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
 * Classe du malade sherpa
 */
class CSpSejMed extends CSpObject {  
  // DB Table key
  var $numdos = null;

  // DB Fields : see getSpecs();
  
	function CSpSejMed() {
	  $this->CSpObject("t_sejmed", "numdos");    
	}
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = "CSejour";
    return $spec;
  }
 	
  function getSpecs() {
    $specs = parent::getSpecs();

    $specs["sejfla"]  = "str length|1"    ; /* Flag                        */
    $specs["numdos"] = "numchar length|6" ; /* Numero de dossier           */
    $specs["malnum"] = "numchar length|6" ; /* Numero de malade            */
    $specs["datent"] = "str length|19"; /* Date et heure d'entree      */
    $specs["litcod"] = "str length|4" ; /* Lit                         */
    $specs["sercod"] = "str length|2" ; /* Service                     */
    $specs["pracod"] = "str length|3" ; /* Code interne du praticien   */
    $specs["datsor"] = "str length|19"; /* Date et heure de sortie     */

    /* Nature Interruption Sejour  */
    $specs["depart"] = "enum list|D|T|S|E|R|P";
//              /* D(eces) , T(ransfert > 48)  */
//              /* S(ortie), E(xterieur < 48)  */
//              /* R(etour Trf > 48), P(sy)    */
//    $specs["etapro"] = "str length|2" ; /* Code Etabl. Ext. Prov.      */
//    $specs["etades"] = "str length|2" ; /* Code Etabl. Ext. Dest.      */

    return $specs;
  }
  
  function updateFormFields() {
    $this->_view = "$this->numdos ($this->malnum)";
  }
  
  function mapTo() {
    global $g;

    // Etablissement
    $sejour = new CSejour();
    $sejour->group_id = $g;
    
    // Patient
    $malade = new CSpMalade();
    $malade->load($this->malnum);
    if ($patient = $malade->loadMbObject()) {
      $sejour->patient_id = $patient->_id;
    }
    
    // Praticien
    $praticien = CSpObjectHandler::getMbObjectFor("CMediusers", $this->pracod);
    $sejour->praticien_id = $praticien->_id;
    
    
    // Entr�e et sortie
    $this->datent = trim(preg_replace("/(\d{2})\/(\d{2})\/(\d{2})/", "$1/$2/20$3", $this->datent));
    $sejour->entree_prevue = mbDateFromLocale($this->datent);
    $this->datsor = trim(preg_replace("/(\d{2})\/(\d{2})\/(\d{2})/", "$1/$2/20$3", $this->datsor));
    $sejour->sortie_prevue = mbDateFromLocale($this->datsor);
    
    
    if ($this->datsor) {
      $sejour->entree_reelle = $sejour->entree_prevue;
      $sejour->sortie_reelle = $sejour->sortie_prevue;
    }
    
    // Sercod
    $duree_prevue = 2;
    switch ($this->sercod) {
      case "ZT":
      $sejour->type = "ambu";
      $sejour->zt = "1";
      $duree_prevue = 0;
      break;

      case "SR":
      $sejour->type = "comp";
      $sejour->reanimation = 1;
      $duree_prevue = 3;
      break;

      case "PB":
      $sejour->type = "ambu";
      $sejour->facturable = 1;
      $duree_prevue = 0;
      break;
      
      case "EX":
      $sejour->type = "ambu";
      $sejour->facturable = 0;
      $duree_prevue = 0;
      break;
      
      default:
	    switch (substr($this->sercod, 0, 1)) {
	      case "A" : 
	      $sejour->type = "ambu"; 
        $duree_prevue = 1;
	      break;
	      
	      case "2" : 
	      $sejour->type = "comp"; 
	      $duree_prevue = 3;
	      break;
	    }

	    $presta = substr($this->sercod, 1, 1);
	    if ($presta == "2") {
	      $sejour->chambre_seule = 0;
	    }
	    else {
	      $sejour->chambre_seule = 1;
        $mbpresta = CSpObjectHandler::getMbObjectFor("CPrestation", $presta);
        
        $sejour->prestation_id = $mbpresta->_id;
	    }
    }
    
    if (!$sejour->sortie_prevue) {
      $sejour->sortie_prevue = mbDateTime("+$duree_prevue DAYS", $sejour->entree_prevue);
    }
    
    switch ($this->depart) {
      case "S" : $sejour->mode_sortie = "normal"; break;
      case "D" : $sejour->mode_sortie = "deces" ; break;
      case "T" : 
      case "E" : $sejour->mode_sortie = "transfert"; break;
    }
    
    return $sejour;
  }
  
  function mapFrom(CMbObject &$mbObject) {
    $mbClass = $this->_spec->mbClass;
    if (!is_a($mbObject, $mbClass)) {
      trigger_error("mapping object should be a '$mbClass'");
    }
    
    $this->sejfla = "A";
    
    $sejour = $mbObject;
    $sejour->loadRefsFwd();
    $idMalade = CSpObjectHandler::getId400For($sejour->_ref_patient);
    
    $this->malnum = $idMalade->id400;
    
    // Date d'entr�e
    $entree = mbGetValue($sejour->entree_reelle, $sejour->entree_prevue); 
    $this->datent = mbDateToLocale($entree);
    
    // Date de sortie, on ne passe pas les sorties pr�vues
    $sortie_prevue = $sejour->type == "ambu" ? $sejour->sortie_prevue : null;
    $sortie = mbGetValue($sejour->sortie_reelle ,$sortie_prevue);
    $this->datsor = $sortie ? mbDateToLocale($sortie) : "";
    
    // Code du praticien
    $idPraticien = CSpObjectHandler::getId400For($sejour->_ref_praticien);
    $this->pracod = $idPraticien->id400;
    
    // Codes d'admission/prestation    
    $sercod = "";
    if ($sejour->type == "exte") {
      $sercod = $sejour->facturable ? "PB" : "EX";
    } 
    else {
      if ($sejour->type == "comp") $sercod = "2";
      $duree_time = mbTimeRelative($sejour->entree_prevue, $sejour->sortie_prevue);
      if ($duree_time  <= "48:00:00") $sercod = "A";
      
      if (!$sejour->chambre_seule) {
        $sercod .= 2;
      } else {
	      $presta = new CPrestation();
	      $presta->load($sejour->prestation_id);
        $idPresta = CSpObjectHandler::getId400For($presta);
        $sercod .= mbGetValue($idPresta->id400, 1);
      }
      
      if ($sejour->reanimation) {
        $sercod = "SR";
      }
      
      if ($sejour->zt) {
        $sercod = "ZT";
      }
    }

    $this->sercod = $sercod;
    
    // Codes du lit 
    $sejour->loadRefsAffectations();
    $affectation = $sejour->_ref_first_affectation;
    $affectation->loadRefLit();
    $idLit = CSpObjectHandler::getId400For($affectation->_ref_lit);
    $this->litcod = $idLit->id400;

    // Si externe, valeur prioritaire
    if ($sejour->type == "exte") {
      $this->litcod = $sejour->facturable ? "BLOC" : "AMEX";
    }
    
    // Mode de sortie 
    $this->depart = "";
    if ($sejour->sortie_reelle) {
      if ($sejour->mode_sortie == "normal"   ) $this->depart = "S";
      if ($sejour->mode_sortie == "deces"    ) $this->depart = "D";
      if ($sejour->mode_sortie == "transfert") $this->depart = $sejour->_duree_reelle >= 2 ? "T" : "E";
    }
  }
}

?>