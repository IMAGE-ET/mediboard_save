<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPcabinet
 * @version $Revision: $
 * @author Fabien M�nager
 */

class CReglement extends CMbObject {
  // DB Table key
  var $reglement_id    = null;

  // DB References
  var $consultation_id = null;
  var $banque_id       = null;

  // DB fields
  var $date            = null;
  var $montant         = null;
  var $emetteur        = null;
  var $mode            = null;
  
  // Fwd References
  var $_ref_consultation = null;
  var $_ref_banque     = null;
  
  function CReglement() {
    $this->CMbObject('reglement', 'reglement_id');
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
    $specs = parent::getSpecs();
    $specs['consultation_id'] = 'ref notNull class|CConsultation';
    $specs['banque_id']       = 'ref class|CBanque';
    $specs['date']            = 'dateTime notNull';
    $specs['montant']         = 'currency notNull';
    $specs['emetteur']        = 'enum list|patient|tiers';
    $specs['mode']            = 'enum notNull list|cheque|CB|especes|virement|autre default|cheque';
    return $specs;
  }
  
  function loadRefsFwd() {
    if (!$this->_ref_consultation) {
 	    $this->_ref_consultation = new CConsultation();
  	  $this->_ref_consultation->load($this->consultation_id);
    }
  }
  
  function loadRefsBack() {
    $this->_ref_banque = new CBanque();
    $this->_ref_banque->load($this->banque_id);
  }
  
  function check () {
    if ($msg = parent::check()) {
      return $msg;
    }
    
    if (!$this->montant) {
      return 'Le montant du r�glement ne doit pas �tre nul';
    }
    
    if (!$this->mode) {
      return 'Le mode de paiment ne doit pas �tre nul';
    }
    
    $this->loadRefsFwd();
  }
  
  /**
   * Acquite la facture automatiquement
   * @return Store-like message
   */
  function acquiteFacture() {
    // Au cas o� le reglement fait l'acquittement
    $this->loadRefsFwd();
    $consult =& $this->_ref_consultation;
    $consult->loadRefsReglements();
    
    // Acquitement patient
    if ($this->emetteur == "patient" && $consult->du_patient) {
      $consult->patient_date_reglement = $consult->_du_patient_restant <= 0 ? mbDate() : "";
    }
      
    // Acquitement tiers
    if ($this->emetteur == "tiers" && $consult->du_tiers) {
      $consult->tiers_date_reglement = $consult->_du_tiers_restant <= 0 ? mbDate() : "";
    }
      
    return $consult->store();
  }
  
  function store() {
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }
    
    return $this->acquiteFacture();
  }
  
  function delete() {
    // Preload consultation
    $this->load();
    $this->loadRefsFwd();
    
    // Standard delete
    if ($msg = parent::delete()) {
      return $msg;
    }

    return $this->acquiteFacture();
  }
  
  function getPerm($permType) {
    if(!$this->_ref_consultation) {
      $this->loadRefsFwd();
    }
    return $this->_ref_consultation->getPerm($permType);
  }
}

?>