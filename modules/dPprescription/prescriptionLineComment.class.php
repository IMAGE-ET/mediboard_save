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
class CPrescriptionLineComment extends CPrescriptionLine {
  // DB Table key
  var $prescription_line_comment_id = null;
  
  // DB Fields
  var $commentaire               = null;
  var $category_prescription_id  = null;
  var $executant_prescription_line_id = null;
  
  // Object references
  var $_ref_category_prescription = null;
  var $_ref_executant = null;

  // Can fields
  var $_can_vw_form_executant = null;
  var $_can_select_executant = null;
  var $_can_delete_line = null;
  var $_can_view_signature_praticien = null;
  var $_can_view_form_signature_praticien = null;
  var $_can_view_form_signature_infirmiere = null;
  var $_can_view_form_ald = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prescription_line_comment';
    $spec->key   = 'prescription_line_comment_id';
    return $spec;
  }
  
  function getSpecs() {
  	$specsParent = parent::getSpecs();
    $specs = array (
      "category_prescription_id"       => "ref class|CCategoryPrescription",
      "executant_prescription_line_id" => "ref class|CExecutantPrescriptionLine",
      "commentaire"                    => "text"
    );
    return array_merge($specsParent, $specs);
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_date_arret_fin = "";
  }
  
  /*
   * Calcul des droits
   */
  function getAdvancedPerms($is_praticien = 0, $prescription_type = "", $mode_protocole = 0, $mode_pharma = 0) {
		global $AppUI;
		
		$perm_edit = ($this->praticien_id == $AppUI->user_id) && !$this->signee;
    if($this->_class_name == "CPrescriptionLineElement"){
    	// Visualisation du forulaire de gestion d'executant
    	$this->_can_vw_form_executant = 1;  
    	// Modification de l'executant
    	if($perm_edit){
    		$this->_can_select_executant = 1;
    	}
    }
    // View ALD
    if($perm_edit && !$this->protocole){
      $this->_can_view_form_ald = 1;
    }
    // Suppression de la ligne
    if(($perm_edit) || $this->_protocole){
  	  $this->_can_delete_line = 1;
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
    if(!$this->_protocole && !$is_praticien){
    	$this->_can_view_form_signature_infirmiere = 1;
    }
	}
  
	
  function loadRefsFwd() {
    parent::loadRefsFwd();
    $this->loadRefCategory();
    $this->loadRefExecutant();
  }
  
  /*
   * Chargement de la categorie du commentaire
   */
  function loadRefCategory(){
  	$this->_ref_category_prescription = new CCategoryPrescription();
  	$this->_ref_category_prescription->load($this->category_prescription_id);	
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