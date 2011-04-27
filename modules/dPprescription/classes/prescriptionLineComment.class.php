<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CPrescriptionLineComment extends CPrescriptionLine {
  // DB Table key
  var $prescription_line_comment_id = null;
  
  // DB Fields
  var $category_prescription_id  = null;
  var $executant_prescription_line_id = null;
  var $user_executant_id              = null;
  
  // Object references
  var $_ref_category_prescription = null;
  var $_ref_executant = null;
  var $_executant = null;
  
  // Can fields
  var $_can_vw_form_executant = null;
  var $_can_select_executant = null;
  var $_can_delete_line = null;
  var $_can_view_signature_praticien = null;
  var $_can_view_form_signature_praticien = null;
  var $_can_view_form_signature_infirmiere = null;
  var $_can_view_form_ald = null;
  
	var $_chapitre = null;
	
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'prescription_line_comment';
    $spec->key   = 'prescription_line_comment_id';
    return $spec;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["category_prescription_id"]       = "ref class|CCategoryPrescription";
    $specs["executant_prescription_line_id"] = "ref class|CExecutantPrescriptionLine";
    $specs["user_executant_id"]              = "ref class|CMediusers";
    return $specs;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["parent_line"]     = "CPrescriptionLineComment child_id";  
    $backProps["transmissions"]   = "CTransmissionMedicale object_id";
    return $backProps;
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    $this->_fin_reelle = "";
    $this->_view = $this->commentaire;
    $this->loadRefsFwd();
		
		$this->_chapitre = $this->_ref_category_prescription->chapitre;
  }
  
  function canEdit(){
    $nb_hours = CAppUI::conf("dPprescription CPrescription max_time_modif_suivi_soins");
    $datetime_max = mbDateTime("+ $nb_hours HOURS", "$this->debut $this->time_debut");
		return $this->_canEdit = (mbDateTime() < $datetime_max) && (CAppUI::$instance->user_id == $this->praticien_id);
  }
	
  function updateDBFields(){
    parent::updateDBFields();
    if($this->_executant !== null){
      if($this->_executant == ""){
        $this->executant_prescription_line_id = "";
        $this->user_executant_id = "";
      } else {
	      $explode_executant = explode("-", $this->_executant);
	      if($explode_executant[0] == "CExecutantPrescriptionLine"){
	        $this->executant_prescription_line_id = $explode_executant[1];
	      } else {
	        $this->user_executant_id = $explode_executant[1];
	      }
      }
    }
  }
  
  
  /*
   * Calcul des droits
   */
  function getAdvancedPerms($is_praticien = 0, $mode_protocole = 0, $mode_pharma = 0, $operation_id = 0) {
		global $AppUI, $can;
		       
	  $current_user = new CMediusers();
    $current_user->load($AppUI->user_id);
		
			
		$chapitre = $this->_ref_category_prescription->chapitre;
    if(!$chapitre){
    	$chapitre = "med";
    }

		if($this->_protocole){
      $protocole =& $this->_ref_prescription;
      if($protocole->praticien_id){
        $protocole->loadRefPraticien();
        $is_praticien = CAppUI::$user->isPraticien();
        $perm_edit = (!$is_praticien || ($is_praticien && CAppUI::$user->_id == $protocole->praticien_id)) ? 1 : 0;
        
      } elseif($protocole->function_id){
        $protocole->loadRefFunction();
        $perm_edit = $protocole->_ref_function->canEdit();
      } elseif($protocole->group_id){
        $protocole->loadRefGroup();
        $perm_edit = $protocole->_ref_group->canEdit();
      }
    } else {
		  $perm_edit = $can->admin || (!$this->signee && ($this->praticien_id == $AppUI->user_id || 
			                                                $is_praticien || 
																											$operation_id || 
																											($current_user->isExecutantPrescription() && CAppUI::conf("dPprescription CPrescription droits_infirmiers_$chapitre") && !CAppUI::conf("dPprescription CPrescription role_propre"))));             
		}
		$this->_perm_edit = $perm_edit;
    
    // Executant
    if($perm_edit){
      $this->_can_select_executant = 1;
      $this->_can_vw_form_executant = 1;  
    }
   
    // View ALD
    if($perm_edit){
      $this->_can_view_form_ald = 1;
    }
    // Suppression de la ligne
    if($perm_edit){
  	  $this->_can_delete_line = 1;
  	}
  	// View signature praticien
    if(!$this->_protocole){
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
  function loadRefCategory() {
    $category = new CCategoryPrescription();
  	$this->_ref_category_prescription = $category->getCached($this->category_prescription_id);	
  }
    
  /*
   * Chargement de l'executant
   */
  function loadRefExecutant(){
    if (!$this->_ref_executant) {
      if($this->executant_prescription_line_id){
		    $executant = new CExecutantPrescriptionLine();
		    $this->_ref_executant = $executant->getCached($this->executant_prescription_line_id);
      }
      if($this->user_executant_id){
        $user = new CMediusers();
        $this->_ref_executant = $user->getCached($this->user_executant_id);
      }
    }
  }
}

?>