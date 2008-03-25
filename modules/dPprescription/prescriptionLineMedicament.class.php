<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Romain Ollivier
 */

/**
 * The CPrescription class
 */
class CPrescriptionLineMedicament extends CPrescriptionLine {
  // DB Table key
  var $prescription_line_id = null;
  
  // DB Fields
  var $code_cip        = null;
  var $no_poso         = null;
  var $commentaire     = null;
  var $debut           = null;
  var $duree           = null;
  var $unite_duree     = null;
  var $stoppe          = null;  
  
  // Form Field
  var $_fin            = null;
  var $_unite_prise    = null;
  var $_specif_prise   = null;
  
  // Object References
  var $_ref_prescription = null;
  var $_ref_produit      = null;
  var $_ref_posologie    = null;
  
  // Alertes
  var $_ref_alertes      = null;
  var $_ref_alertes_text = null;
  var $_nb_alertes       = null;

  // BackRefs
  var $_ref_prises = null;
  
  // Behaviour field
  var $_delete_prises = null;
  
  
  function CPrescriptionLineMedicament() {
    $this->CMbObject("prescription_line", "prescription_line_id");
    
    $this->loadRefModule(basename(dirname(__FILE__)));
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "code_cip"        => "notNull numchar|7",
      "no_poso"         => "num max|128",
      "commentaire"     => "str",
      "debut"           => "date",
      "duree"           => "num",
      "unite_duree"     => "enum list|minute|heure|demi_journee|jour|semaine|quinzaine|mois|trimestre|semestre|an default|jour",
      "stoppe"          => "bool",
      "_fin"            => "date",
      "_unite_prise"    => "str"
    );
    return array_merge($specsParent, $specs);
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["prise_posologie"] = "CPrisePosologie prescription_line_id";
    return $backRefs;
  }
  
  function getSeeks() {
    return array (
    );
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    $this->loadRefsFwd();
    $this->_nb_alertes = 0;
    $this->_view = $this->_ref_produit->libelle;
    $this->_duree_prise = "";
    if($this->debut){
      $this->_duree_prise .= "� partir du ".mbTranformTime(null, $this->debut, "%d/%m/%Y");
    }
    if($this->duree && $this->unite_duree){
    	$this->_duree_prise .= " pendant ".$this->duree." ".$this->unite_duree;
    }
  }
  
  // Store-like function
  function deletePrises(){
  	$this->_delete_prises = 0;
  	// Chargement des prises 
    $this->loadRefsPrises();
    // Parcours des suppression des prises
    foreach($this->_ref_prises as &$_prise){
      if($msg = $_prise->delete()){
      	mbTrace($msg);
      	return $msg;
      }
    }
  }
  
  function store(){
  	if($msg = parent::store()){
  		return $msg;
  	}
  	
  	if($this->_delete_prises){
  		if($msg = $this->deletePrises()){
  			return $msg;
  		}
  	}
  }
  
  function loadRefsFwd() {
  	$this->_ref_prescription = new CPrescription();
    $this->_ref_prescription->load($this->prescription_id);
    $this->_ref_produit = new CBcbProduit();
    $this->_ref_produit->load($this->code_cip);
    $this->loadPosologie();
  }
  
  
  function loadRefsPrises(){
    $this->_ref_prises = $this->loadBackRefs("prise_posologie");	
  }
  
  function loadPosologie() {
    $posologie = new CBcbPosologie();
    if($this->_ref_produit->code_cip && $this->no_poso) {
      $posologie->load($this->_ref_produit->code_cip, $this->no_poso);
    }
    $this->_unite_prise = $posologie->_code_unite_prise["LIBELLE_UNITE_DE_PRISE"];
    $this->_specif_prise = $posologie->_code_prise1;
    $this->_ref_posologie = $posologie;
  }
  
  function checkAllergies($listAllergies) {
    $this->_ref_alertes["allergie"] = array();
    foreach($listAllergies as $key => $all) {
      if($all->CIP == $this->code_cip) {
        $this->_nb_alertes++;
        $this->_ref_alertes["allergie"][$key]      = $all;
        $this->_ref_alertes_text["allergie"][$key] = $all->LibelleAllergie;
      }
    }
  }
  
  function checkInteractions($listInteractions) {
    $this->_ref_alertes["interaction"] = array();
    foreach($listInteractions as $key => $int) {
      if($int->CIP1 == $this->code_cip) {
        $this->_nb_alertes++;
        $this->_ref_alertes["interaction"][$key]      = $int;
        $this->_ref_alertes_text["interaction"][$key] = $int->Type;
      }
    }
  }
  
  function checkIPC($listIPC) {
    $this->_ref_alertes["IPC"]      = array();
    $this->_ref_alertes_text["IPC"] = array();
  }
  
  function checkProfil($listProfil) {
    $this->_ref_alertes["profil"] = array();
    foreach($listProfil as $key => $pro) {
      if($pro->CIP == $this->code_cip) {
        $this->_nb_alertes++;
        $this->_ref_alertes["profil"][$key]      = $pro;
        $this->_ref_alertes_text["profil"][$key] = $pro->LibelleMot;
      }
    }
  }
  
}

?>