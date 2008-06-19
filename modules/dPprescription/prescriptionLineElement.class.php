<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

/**
 * The CPrescription class
 */
class CPrescriptionLineElement extends CPrescriptionLine {
  // DB Table key
  var $prescription_line_element_id = null;
  
  // DB Fields
  var $element_prescription_id        = null;
  var $commentaire                    = null;
  var $executant_prescription_line_id = null; 
  
  // Object references
  var $_ref_element_prescription      = null;
  var $_ref_executant                 = null;

  // Can fields
  var $_can_select_executant               = null;
  var $_can_delete_line                    = null;
  var $_can_view_signature_praticien       = null;
  var $_can_view_form_signature_praticien  = null;
  var $_can_view_form_signature_infirmiere = null;
  var $_can_view_form_ald                  = null;
  var $_can_modify_poso                    = null;
  var $_can_modify_comment                 = null;
  var $_can_modify_dates                   = null; 
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prescription_line_element';
    $spec->key   = 'prescription_line_element_id';
    return $spec;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->loadRefElement();
    $this->_ref_element_prescription->loadRefCategory();
    $this->_view = $this->_ref_element_prescription->_view;
    
    // Un element ne peut jamais �tre un traitement
    $this->_traitement = 0;	
    $this->_unite_prise = "fois";
    $this->_duree_prise = "";
    
    if($this->fin){
    	$this->_duree_prise .= "Jusqu'au ".mbTransformTime(null, $this->fin, "%d/%m/%Y");
    } else {
	    if($this->debut && !$this->_fin){
	      $this->_duree_prise .= "le ".mbTransformTime(null, $this->debut, "%d/%m/%Y");
	    }
	    if($this->duree && $this->_fin){
	    	$this->_duree_prise .= "� partir du ".mbTransformTime(null, $this->debut, "%d/%m/%Y")." pendant ".$this->duree." ".CAppUI::tr("CPrescriptionLineMedicament.unite_duree.".$this->unite_duree).".";
	    }
    }
    
    // Calcul de la date de fin de la ligne
    $this->_date_arret_fin = $this->_fin ? "$this->_fin 23:59:00" : "$this->debut 23:59:00";    
    if($this->date_arret){
    	$this->_date_arret_fin = $this->date_arret;
      $this->_date_arret_fin .= $this->time_arret ? " $this->time_arret" : " 23:59:00";
    }
  }
  
  /*
   * Calcul des droits
   */
  function getAdvancedPerms($is_praticien = 0, $prescription_type = "", $mode_protocole = 0, $mode_pharma = 0) {	
		global $AppUI;

	  $perm_infirmiere = $this->creator_id == $AppUI->user_id && 
                       !$this->signee && 
                       !$this->valide_infirmiere;
    
		$perm_edit = (!$this->signee) && 
                 ($this->praticien_id == $AppUI->user_id || $perm_infirmiere || $is_praticien);
                 
    // Modification des dates
    if($perm_edit){
    	$this->_can_modify_dates = 1;
    }
    // View ALD
    if($perm_edit && !$this->_protocole){
    	$this->_can_view_form_ald = 1;
    }
    // View signature praticien
    if(($AppUI->user_id != $this->praticien_id) && !$this->_protocole){
    	$this->_can_view_signature_praticien = 1;
    }
    // Affichage du formulaire de signature praticien
    if(!$this->_protocole && $is_praticien && ($this->praticien_id == $AppUI->user_id)){
    	$this->_can_view_form_signature_praticien = 1;
    }
    // Affichage du formulaire de signature infirmiere
    if(!$this->_protocole && !$is_praticien && !$this->signee && $this->creator == $AppUI->user_id){
    	$this->_can_view_form_signature_infirmiere = 1;
    }
    // Suppression de la ligne
    if(($perm_edit && $is_praticien) || $this->_protocole){
  	  $this->_can_delete_line = 1;
  	}
  	// Modification de la posologie
  	if ($perm_edit){
    	$this->_can_modify_poso = 1;
    }
    // Modification de l'executant et du commentaire
    if($perm_edit){
    	$this->_can_select_executant = 1;
    	$this->_can_modify_comment = 1;
    } 
	}
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "element_prescription_id"        => "notNull ref class|CElementPrescription cascade",
      "executant_prescription_line_id" => "ref class|CExecutantPrescriptionLine",
      "commentaire"                    => "str"
    );
    return array_merge($specsParent, $specs);
  }
  
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefElement();
    $this->loadRefExecutant();
  }
  
  /*
   * Chargement de l'element
   */
  function loadRefElement(){
  	$this->_ref_element_prescription = new CElementPrescription();
  	$this->_ref_element_prescription->load($this->element_prescription_id);	
  }
  
  /*
   * Chargement de l'executant
   */
  function loadRefExecutant(){
    $this->_ref_executant = new CExecutantPrescriptionLine();
    $this->_ref_executant->load($this->executant_prescription_line_id);
  }
}

?>