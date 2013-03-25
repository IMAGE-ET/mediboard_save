<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPcabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Facture li�e � une ou plusieurs consultations
 *
 */
class CFactureCabinet extends CFacture {
  // DB Table key
  public $facture_id;
  
  /**
   * getSpec
   * 
   * @return $spec
  **/
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'facture_cabinet';
    $spec->key   = 'facture_id';
    return $spec;
  }
    
  /**
   * getBackProps
   * 
   * @return $backProps
  **/
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["consultations"] = "CConsultation facture_id";
    $backProps["reglements"]    = "CReglement object_id";
    return $backProps;
  }
   
  /**
   * getProps
   * 
   * @return $props
  **/
  function getProps() {
    $props = parent::getProps();
    return $props;
  }
     
  /**
   * updateFormFields
   * 
   * @return void
  **/
  function updateFormFields() {
    parent::updateFormFields();
    $this->_view = sprintf("FA%08d", $this->_id);
  }

  /**
   * Chargement des r�glements de la facture
   * 
   * @param bool $cache cache
   * 
   * @return $this->_ref_reglements
  **/
  function loadRefsReglements($cache = 1) {
    $this->_ref_reglements = $this->loadBackRefs("reglements", 'date');
    return parent::loadRefsReglements($cache);
  }
  
  /**
   * loadRefsFwd
   * 
   * @return void
  **/
  function loadRefsFwd(){
    parent::loadRefsFwd();
    $this->loadRefsConsults();
  } 
  
  /**
   * Red�finition du store
   * 
   * @return void
  **/
  function store() {
    if (CModule::getActive("dPfacturation")) {
      //Si on cloture la facture on cr�� les lignes de facture
      if ($this->cloture && $this->fieldModified("cloture")) {
        $this->creationLignesFacture();
      }
    }
    // A v�rifier pour le == 0 s'il faut faire un traitement
    if ($this->facture !== '0') {
      foreach ($this->loadBackRefs("consultations") as $_consultation) {
        if ($this->facture == -1 && $_consultation->facture == 1) {
          $_consultation->facture = 0;
          $_consultation->store();
        }
        elseif ($this->facture == 1 && $_consultation->facture == 0) {
          $_consultation->facture = 0;
          $_consultation->store();
        }
      }
    }
    
    // Etat des r�glement � propager sur les consultations
    if ($this->fieldModified("patient_date_reglement") || $this->fieldModified("tiers_date_reglement")) {
      foreach ($this->loadBackRefs("consultations") as $_consultation) {
        $_consultation->patient_date_reglement = $this->patient_date_reglement;
        $_consultation->tiers_date_reglement   = $this->tiers_date_reglement;
        
        if ($msg = $_consultation->store()) {
          return $msg;
        }
      }
    }
    
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }
    
  }

  /**
   * Chargement des diff�rentes consultations li�es � la facture
   * 
   * @param bool $cache cache
   * 
   * @return void
  **/
  function loadRefsConsults($cache = 1) {
    parent::loadRefsObjects();
    
    //@todo v�rifier l'utilit� de ceci...
    if (!count($this->_ref_consults)) {
      $this->_ref_consults = $this->loadBackRefs("consultations", "consultation_id");
    }
    
    return $this->_ref_consults;
  }

  /**
   * loadRefs
   * 
   * @return void
  **/
  function loadRefs(){
    $this->loadRefsFwd();
    $this->loadRefsBack();
    $this->updateMontants();
    $this->loadNumerosBVR();
  }
  
  //Ne pas supprimer cette fonction!
  /**
   * loadRefPlageConsult
   * 
   * @return void
  **/
  function loadRefPlageConsult(){
    
  }
  
  /**
   * Fonction permettant � partir d'un num�ro de r�f�rence de retrouver la facture correspondante
   * 
   * @param string $num_reference le num�ro de r�f�rence 
   * 
   * @return $facture
  **/
  function findFacture($num_reference){
    $facture = new CFactureCabinet();
    $facture->num_reference = $num_reference;
    $facture->loadMatchingObject();
    return $facture;
  }
  
  /**
   * Fonction permettant l'ajout d'une consultation � une facture
   * 
   * @param string $patient_id   le patient
   * @param string $chirsel_id   le praticien
   * @param string $consult_id   l'id de la consultation
   * @param string $type_facture le type de la facture
   * 
   * @return void
  **/
  function ajoutConsult($patient_id, $chirsel_id, $consult_id, $type_facture) {
    //@todo � d�placer dans le store de la facture/consult
    $this->_consult_id = $consult_id;
    // Si la facture existe d�j� on la met � jour
    $where["patient_id"]    = "= '$patient_id'";
    $where["praticien_id"]  = "= '$chirsel_id'";
    //La cloture est automatique en france donc toujours valu�e
    if (CAppUI::conf("ref_pays") == 2) {
      $where["cloture"]       = "IS NULL";
    }
    
    //Si la facture existe d�j�
    if ($this->loadObject($where)) {
      $this->loadRefsConsults();
      if (CModule::getActive("dPfacturation")) {
        $ligne = new CFactureLiaison();
        $ligne->facture_id    = $this->_id;
        $ligne->facture_class = $this->_class;
        $ligne->object_id     = $consult_id;
        $ligne->object_class  = 'CConsultation';
        if (!$ligne->loadMatchingObject()) {
          $ligne = new CFactureLiaison();
          $ligne->facture_id    = $this->_id;
          $ligne->facture_class = $this->_class;
          $ligne->object_id     = $consult_id;
          $ligne->object_class  = 'CConsultation';
          $ligne->store();
        }
      }
    }
    else {
      // Sinon on la cr�e
      $consult = new CConsultation();
      $consult->load($consult_id);
      $this->ouverture    = CMbDT::date();
      $this->patient_id   = $patient_id;
      $this->praticien_id = $chirsel_id;
      $this->type_facture = $type_facture;
      $this->du_patient   = $consult->du_patient;
      $this->du_tiers     = $consult->du_tiers;
      if (CAppUI::conf("ref_pays") == 1) {
        $this->cloture    = CMbDT::date();
      }
      $this->store();
      
      if (CModule::getActive("dPfacturation")) {
        $ligne = new CFactureLiaison();
        $ligne->facture_id    = $this->_id;
        $ligne->facture_class = $this->_class;
        $ligne->object_id     = $consult_id;
        $ligne->object_class  = 'CConsultation';
        $ligne->store();
      }
      else {
        $consult->facture_id = $this->_id;
        $consult->store();
      }
    }
  }
}
