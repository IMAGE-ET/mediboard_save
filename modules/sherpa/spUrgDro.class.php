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
 * Classe de l'ouverture des droits Sherpa UPATOU
 */
class CSpUrgDro extends CSpObject {  
  // DB Table key
  var $numdos = null;

  // DB Fields : see getSpecs();
  
	function CSpUrgDro() {
	  $this->CSpObject("t_urgdro", "numdos");    
	}
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->mbClass = "CSejour";
    return $spec;
  }
 	
  function getSpecs() {
    $specs = parent::getSpecs();
        
    $specs["urgfla"]  = "str length|1"    ; /* Flag                             */
    $specs["numdos"]  = "numchar length|6"; /* No de dossier (Annnnn)           */
    $specs["malnum"]  = "numchar length|6"; /* No de malade                     */
    $specs["datarr"]  = "str length|19"   ; /* Date et Heure Arrivee            */
    $specs["datdep"]  = "str length|19"   ; /* Date et Heure D�part             */
    $specs["datmaj"]  = "str length|19"   ; /* DateTime de derniere mise a jour */
    
    
//    $specs["accide"]  = "bool"            ; /* Accident  O/N                    */
//    $specs["acctie"]  = "bool"            ; /*          Cause par 1 Tiers O/N   */
//    $specs["acctra"]  = "bool"            ; /*          Du Travail        O/N   */
//    $specs["datacc"]  = "str length|10"   ; /* Date de l'accident               */
//    $specs["numacc"]  = "str maxLength|9" ; /* No de l'accident                 */
//    $specs["oridro"]  = "bool"            ; /* Origine des Droits               */
//    $specs["datval"]  = "str length|10"   ; /* Date Validite CAS ou etabl. PEC  */
//    $specs["codorg"]  = "str maxLength|3" ; /* Code organ. delivr. CAS ou PEC   */
//    $specs["grdreg"]  = "str maxLength|2" ; /* Grand regime                     */
//    $specs["caisse"]  = "str maxLength|3" ; /* Organisme gestionnaire           */
//    $specs["centre"]  = "str maxLength|3" ; /* centre gestionnaire              */
//    $specs["cleorg"]  = "bool"            ; /* Cle organisme                    */
//    $specs["regime"]  = "str maxLength|3" ; /* Regime                           */
//    $specs["risque"]  = "str maxLength|2" ; /* Nature d'assurance               */

//    $specs["exoner"]  = "bool"            ; /* Justification exoneration        */
//    $specs["mutuel"]  = "str maxLength|12"; /* Code mutuelle                    */
//    $specs["opsion"]  = "str maxLength|2" ; /* Code Option                      */
//    $specs["dnaiss"]  = "str length|10"   ; /* Date Naiss. Selon Secu jj/mm/aaaa*/

//    $specs["nomemp"]  = "str maxLength|30"; /* Nom employeur                    */
//    $specs["adremp"]  = "str maxLength|30"; /* Adresse employeur                */
//    $specs["vilemp"]  = "str maxLength|30"; /* Code postal et ville employeur   */
//    $specs["matemp"]  = "str maxLength|9" ; /* No matricule employeur           */
//    $specs["telemp"]  = "str maxLength|14"; /* Forfait gouvernemental  O/N      */
//    $specs["exoatu"]  = "str length|1"    ; /* Exoneration Forfait ATU          */
    

    /* Provenance                       */
    $urprov = array (
      "AM", // Ambulance
      "AT", // Accident du travail
      "DO", // Domicile
      "EC", // Ecole
      "MT", // M�decin Traitant
      "OT", // Autre �tablissement
      "RA", // Radio
      "RC", // Reconvocation
      "SP", // Sport
      "VP", // Voie public
    );
    
    $urprov = implode("|", $urprov);
    $specs["urprov"]  = "enum list|$urprov"; 
    
    /* Code GEMSA                       */
    $urgems = array (
      "1", // D�c�d� � l'arriv�e avant r�animation
      "2", // Pas de reconvocation
      "3", // Convocation pour soins
      "4", // Hospitalisation non attendue dans service
      "5", // Hospitalisation attendue dans service
      "6", // Traiement imm�diat important
    );

    $urgems = implode("|", $urgems);
    $specs["urgems"]  = "enum list|$urgems";
    
    /* Code CCMU                        */
    $specs["urccmu"]  = "str enum list|1|2|3|4|5";
    
    /* Mode de transport (arrivee)      */
    $urmtra = array (
      "1", // Propres moyens
      "2", // VSL
      "3", // Ambulance
      "4", // Police
      "5", // Pompiers
      "6", // SAMU
      "7", // Taxi
    );
    
    $urmtra = implode("|", $urmtra);
    $specs["urmtra"]  = "enum list|$urmtra";
    
    /* Destination                      */
    $urdest = array (
      "D", // D�c�s
      "M", // Mutation
      "S", // Sortie
    );
    
    $urdest = implode("|", $urdest);
    $specs["urdest"]  = "enum list|$urdest";

    /* Cause Transfert   (Depart)       */
    $urmuta = array (
      "A", // Absence moyen sp�cifique
      "D", // Demande du patient
      "M", // Mutation
      "P", // Pas de lits (plus de place)
      "X", // Sans tranfert
    );
    
    $urmuta = implode("|", $urmuta);
    $specs["urmuta"]  = "enum list|$urmuta";

    /* Type d'Urgence                   */
    $urtype = array (
      "C", // Chirurgical
      "E", // P�diatrique
      "M", // M�dical
      "P", // Psychatrique
      "T", // Traumato
    );
    
    $urtype = implode("|", $urtype);
    $specs["urtype"]  = "enum list|$urtype";    
    
    /* Urgence Traumato                 */
    $urtrau = array (
      "I", // Immobilisation
      "S", // Suture
      "T", // Traitement m�dical
    );
    
    $urtrau = implode("|", $urtrau);
    $specs["urtrau"]  = "enum list|$urtrau";    
    
    return $specs;
  }
  
  function updateFormFields() {
    $this->_view = "$this->numdos ($this->malnum)";
  }
  
  function mapTo() {
    // Load patient
    $malade = new CSpMalade();
    $malade->load($this->malnum); 
    if (!$patient = $malade->loadMbObject()) {
      throw new Exception("Malade '$this->malnum' is not linked to a Mb Patient");
    }
    
    $sejour = new CSejour();
    $sejour->patient_id = $patient->_id;
    return $sejour;
  }
  
  function isConcernedBy(CMbObject &$mbObject) {
    $mbClass = $this->_spec->mbClass;
    if (!$mbObject instanceof $mbClass) {
      trigger_error("mapping object should be a '$mbClass'");
      return false;
    }
    
    return $mbObject->type == "urg";
  }
  
  function mapFrom(CMbObject &$mbObject) {
    $mbClass = $this->_spec->mbClass;
    if (!$mbObject instanceof $mbClass) {
      trigger_error("mapping object should be a '$mbClass'");
    }
    
    $sejour = $mbObject;
    $sejour->loadRefsFwd();
    
    $this->urgfla = "1";
    
    // Malade
    $idMalde = CSpObjectHandler::getId400For($sejour->_ref_patient);
    $this->malnum = $idMalde->id400;
        
    // Mise � jour
    $this->datmaj = mbDateToLocale(mbDateTime());
  }
}

?>