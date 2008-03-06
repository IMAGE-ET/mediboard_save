<?php

class CCodable extends CMbObject {
	
  // DB Fields
  var $codes_ccam          = null;
  
  // Form fields
  var $_acte_execution     = null;
  var $_acte_depassement   = null;
  var $_ref_anesth         = null;
  var $_anesth             = null;
  var $_associationCodesActes = null;
  
  // Abstract fields
  var $_praticien_id       = null;
  var $_coded              = 0;    // Initialisation � 0 => codable qui peut etre cod� !
  var $_datetime           = null;
  
  // Actes CCAM
  var $_text_codes_ccam    = null;
  var $_codes_ccam         = null;
  var $_tokens_ccam        = array();
  var $_ref_actes_ccam     = null;
  var $_ext_codes_ccam     = null;
  
  // Actes NGAP
  var $_store_ngap     = null;
  var $_ref_actes_ngap = null;
  var $_codes_ngap     = null;
  var $_tokens_ngap    = null;

  // Back references
  var $_ref_actes = null;
  var $_ref_prescriptions = null;
  
  // Distant references
  var $_ref_sejour = null;
  var $_ref_patient = null;
  var $_ref_praticien = null;
  
  function loadRefSejour() {
  }
  
  function loadRefPatient() {
  }
  
  function updateFormFields() {
  	parent::updateFormFields();
    
    $this->codes_ccam = strtoupper($this->codes_ccam);
    $this->_text_codes_ccam = str_replace("|", ", ", $this->codes_ccam);
    $this->_codes_ccam = $this->codes_ccam ? 
      explode("|", $this->codes_ccam) : 
      array(); 
 
  }
  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["actes_ngap"] = "CActeNGAP object_id";
    $backRefs["actes_ccam"] = "CActeCCAM object_id";
    return $backRefs;
  }
  
  function getAssociationCodesActes() {
    $this->updateFormFields();
    $this->loadRefsActesCCAM();
    if($this->_ref_actes_ccam){
      foreach ($this->_ref_actes_ccam as &$acte_ccam) {
        $acte_ccam->loadRefExecutant();
      }
    }
    $this->_associationCodesActes = array();
    $listCodes = $this->_codes_ccam;
    $listActes = $this->_ref_actes_ccam;
    $i = 0;
    foreach($listCodes as $curr_code) {
      $code_complet = explode("-", $curr_code);
      $ccam     = $code_complet[0];
      $phase    = isset($code_complet[1]) ? $code_complet[1] : null;
      $activite = isset($code_complet[2]) ? $code_complet[2] : null;
      $this->_associationCodesActes[$i]["code"]    = $curr_code;
      $this->_associationCodesActes[$i]["nbActes"] = 0;
      $this->_associationCodesActes[$i]["ids"]     = "";
      foreach($listActes as $key_acte => $curr_acte) {
        $test = ($curr_acte->code_acte == $ccam);
        $test = $test && ($phase === null || $curr_acte->code_phase == $phase);
        $test = $test && ($activite === null || $curr_acte->code_activite == $activite);
        $test = $test && (!isset($this->_associationCodesActes[$i]["actes"][$curr_acte->code_phase][$curr_acte->code_activite]));
        if($test) {
          $this->_associationCodesActes[$i]["actes"][$curr_acte->code_phase][$curr_acte->code_activite] = $curr_acte;
          $this->_associationCodesActes[$i]["nbActes"]++;
          $this->_associationCodesActes[$i]["ids"] .= "$curr_acte->_id|";
          unset($listActes[$key_acte]);
        }
      }
      $i++;
    }
  }
  
  function updateDBCodesCCAMField() {
    if (null !== $this->_codes_ccam) {
      $this->codes_ccam = implode("|", $this->_codes_ccam);
    }
  }
  
  
  function doUpdateMontants(){
    
  }
  
  function updateDBFields() {
    // Should update codes CCAM. Very sensible, test a lot before uncommenting
    // $this->updateDBCodesCCAMField();
  }
  
  function getSpecs() {
  	$specs["codes_ccam"] = "str";
  	return $specs;
  }
  
  function preparePossibleActes() {
  }
  
  function getExecutantId($code_activite) {
    return null;
  }
  
  function loadRefsPrescriptions() {
    $prescription = new CPrescription();
    $where = array("object_class" => "= '$this->_class_name'", "object_id" => "= $this->_id");
    $this->_ref_prescriptions = $prescription->loadList($where);
  }
  
  function loadRefsActes(){
    $this->loadRefsActesCCAM();
    $this->loadRefsActesNGAP();  
    
    foreach($this->_ref_actes_ccam as $acte_ccam){
      $this->_ref_actes[] = $acte_ccam;
    }
    foreach($this->_ref_actes_ngap as $acte_ngap){
      $this->_ref_actes[] = $acte_ngap;
    }
  }
  
  function loadRefsActesNGAP() {
    if (null === $this->_ref_actes_ngap = $this->loadBackRefs("actes_ngap")) {
      return;
    }
    $this->_codes_ngap = array();
    foreach ($this->_ref_actes_ngap as $_actes_ngap){
      if($_actes_ngap->montant_depassement < 0){
        $_montant_depassement_temp = str_replace("-", "*", $_actes_ngap->montant_depassement);
      } else {
        $_montant_depassement_temp = $_actes_ngap->montant_depassement;
      }
      $this->_codes_ngap[] = $_actes_ngap->quantite."-".$_actes_ngap->code."-".$_actes_ngap->coefficient."-".$_actes_ngap->montant_base."-".$_montant_depassement_temp."-".$_actes_ngap->demi; 
    }
    $this->_tokens_ngap = join($this->_codes_ngap, "|");
  }
  
  /**
   * Charge les codes CCAM en tant qu'objets externes
   */
  function loadExtCodesCCAM($full = 0) {
    $this->_ext_codes_ccam = array();
    if ($this->_codes_ccam !== null) {
      foreach ($this->_codes_ccam as $code) {
        $this->_ext_codes_ccam[] = CCodeCCAM::get($code, $full?(CCodeCCAM::FULL):(CCodeCCAM::LITE));
      }
    }
  }

  function getMaxCodagesActes() {
    if(!$this->_id || $this->codes_ccam === null) {
      return;
    }

    $oldObject = new $this->_class_name;
	  $oldObject->load($this->_id);
	  $oldObject->codes_ccam = $this->codes_ccam;
	  $oldObject->updateFormFields();
	    
	  $nb_codes_ccam_minimal = array();
	  $codes_ccam_minimal = array();
	  $nb_codes_ccam = array();
	    
	  $oldObject->loadRefsActesCCAM();
	    
	  // Creation du tableau minimal de codes ccam
	  foreach($oldObject->_ref_actes_ccam as $key => $acte) {
	    @$codes_ccam_minimal[$acte->code_acte][$acte->code_activite][$acte->code_phase]++;
	  }
	  foreach($codes_ccam_minimal as $key => $acte){
	    $max = max($acte);
	    $nb_codes_ccam_minimal[$key] = reset($max);
	  }
	  foreach($nb_codes_ccam_minimal as $key => $acte) {
	    for($i = 0; $i < $acte; $i++){
	      $oldObject->_codes_ccam_minimal[] = $key;
	    }
	  }

	  // Transformation du tableau de codes ccam
	  foreach($oldObject->_codes_ccam as $key => $code) {
	    if(strlen($code) > 7){
	      // si le code est de la forme code-activite-phase
        $detailCode = explode("-", $code);
        $code = $detailCode[0];
	    }
	    @$nb_codes_ccam[$code]++;
	  }
	  
	  
	  // Test entre les deux tableaux
	  foreach($nb_codes_ccam_minimal as $code => $nb_code_minimal){
	    if($nb_code_minimal > @$nb_codes_ccam[$code]){
	      return "Impossible de supprimer le code";
	    }
	  }
  }
  
  
  
  function check(){
    $oldObject = new $this->_class_name;
    if($this->_id) {
      $oldObject->load($this->_id);
    }
    
    if($this->codes_ccam != $oldObject->codes_ccam){
      if ($msg = $this->getMaxCodagesActes()) {
        return $msg;
      }
    }   
    return parent::check();
  }
  
  /**
   * Charge les actes CCAM cod�s
   */
  function loadRefsActesCCAM() {
    if ($this->_ref_actes_ccam) {
      return;
    }

  	$order = array();
  	$order[] = "code_association";
  	$order[] = "code_acte";
  	$order[] = "code_activite";
  	$order[] = "code_phase";
  	$order[] = "acte_id";
  	
    if (null === $this->_ref_actes_ccam = $this->loadBackRefs("actes_ccam", $order)) {
      return;
    }
  	
    $this->_temp_ccam = array();
    foreach ($this->_ref_actes_ccam as $_acte_ccam) {
      if($_acte_ccam->montant_depassement < 0){
        $_montant_depassement_temp = str_replace("-", "*", $_acte_ccam->montant_depassement);
      } else {
        $_montant_depassement_temp = $_acte_ccam->montant_depassement;
      }
      $this->_temp_ccam[] = $_acte_ccam->code_acte."-".$_acte_ccam->code_activite."-".$_acte_ccam->code_phase."-".$_acte_ccam->modificateurs."-".$_montant_depassement_temp; 
    }
    $this->_tokens_ccam = join($this->_temp_ccam, "|");
  }
  
  /**
   * Charge les actes CCAM codables en fonction des code CCAM fournis
   */
  function loadPossibleActes () {
    $this->preparePossibleActes();
    $depassement_affecte = false;
    // existing acts may only be affected once to possible acts
    $used_actes = array();
    
    $this->loadExtCodesCCAM(1);
    foreach ($this->_ext_codes_ccam as $code_ccam) {
     
      foreach ($code_ccam->activites as &$activite) {
        foreach ($activite->phases as &$phase) {
          $possible_acte = new CActeCCAM;
          $possible_acte->montant_depassement = "";
          $possible_acte->code_acte = $code_ccam->code;
          $possible_acte->code_activite = $activite->numero;
          
          $possible_acte->_anesth = ( $activite->numero == 4 ) ? true : false;
          
          $possible_acte->code_phase = $phase->phase;
          $possible_acte->execution = $this->_acte_execution;
          
          // Affectation du d�passement au premier acte de chirugie
          if (!$depassement_affecte and $possible_acte->code_activite == 1) {
            $depassement_affecte = true;     	
            $possible_acte->montant_depassement = $this->_acte_depassement;
          }
          
          $possible_acte->executant_id = $this->getExecutantId($possible_acte->code_activite);
          $possible_acte->updateFormFields();
          $possible_acte->loadRefs();
          $possible_acte->getAnesthAssocie();
                    
          // Affect a loaded acte if exists
          foreach ($this->_ref_actes_ccam as $curr_acte) {
            if ($curr_acte->code_acte     == $possible_acte->code_acte 
             && $curr_acte->code_activite == $possible_acte->code_activite 
             && $curr_acte->code_phase    == $possible_acte->code_phase) {
              if (!isset($used_actes[$curr_acte->acte_id])) {
                $possible_acte = $curr_acte;
                $used_actes[$curr_acte->acte_id] = true;
                break;
              }
            }
          }
          
          $possible_acte->guessAssociation();
          $possible_acte->getTarif();
          
          // Keep references !
          $phase->_connected_acte = $possible_acte;
          
          foreach ($phase->_modificateurs as &$modificateur) {
            if (strpos($phase->_connected_acte->modificateurs, $modificateur->code) !== false) {
              $modificateur->_value = $modificateur->code;
            } else {
              $modificateur->_value = "";              
            }
          }
        }
      }
    } 
  }
  
}
?>