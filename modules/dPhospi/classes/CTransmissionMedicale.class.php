<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @abstract Permet d'ajouter des transmissions m�dicales � un s�jour 
 */

class CTransmissionMedicale extends CMbMetaObject {
  // DB Table key
  var $transmission_medicale_id = null;	
  
  // DB Fields
  var $sejour_id   = null;
  var $user_id     = null;
  var $degre       = null;
  var $date        = null;
  var $date_max    = null;
  var $text        = null;
  var $type        = null;
  var $libelle_ATC = null;
  
  // References
  var $_ref_sejour = null;
  var $_ref_user   = null;
  var $_ref_cible  = null;
  
  // Form fields
  var $_cible      = null;
  var $_text_data   = null;
  var $_text_action = null;
  var $_text_result = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'transmission_medicale';
    $spec->key   = 'transmission_medicale_id';
    return $spec;
  }

  function getProps() {
  	$props = parent::getProps();
    $props["object_id"]    = "ref class|CMbObject meta|object_class";
  	$props["object_class"] = "enum list|CPrescriptionLineElement|CPrescriptionLineMedicament|CPrescriptionLineComment|CCategoryPrescription|CAdministration|CPrescriptionLineMix show|0";
    $props["sejour_id"]    = "ref notNull class|CSejour";
    $props["user_id"]      = "ref notNull class|CMediusers";
    $props["degre"]        = "enum notNull list|low|high default|low";
    $props["date"]         = "dateTime notNull";
    $props["date_max"]     = "dateTime";
    $props["text"]         = "text helped|type|object_id";
    $props["type"]         = "enum list|data|action|result";
    $props["libelle_ATC"]  = "text";
    $props["_text_data"]   = "text helped|type|object_id";
    $props["_text_action"] = "text helped|type|object_id";
    $props["_text_result"] = "text helped|type|object_id";
    return $props;
  }
  
  function loadRefSejour(){
    return $this->_ref_sejour = $this->loadFwdRef("sejour_id", true);
  }
  
  function loadRefUser(){
    $this->_ref_user = $this->loadFwdRef("user_id", true);
    $this->_ref_user->loadRefFunction();
		return $this->_ref_user;
	}
  
  function loadRefsFwd() {
  	parent::loadRefsFwd();
    $this->loadRefSejour();
    $this->loadRefUser();
  	$this->_view = "Transmission de ".$this->_ref_user->_view;
  }
  
	function canEdit(){
		$nb_hours = CAppUI::conf("soins max_time_modif_suivi_soins");
    $datetime_max = mbDateTime("+ $nb_hours HOURS", $this->date);
		return $this->_canEdit = (mbDateTime() < $datetime_max) && (CAppUI::$instance->user_id == $this->user_id);
	}
	
  function calculCibles(&$cibles = array()){
    if($this->object_id && $this->object_class){
      // Ligne de medicament, cible => classe ATC
      if($this->object_class == "CPrescriptionLineMedicament"){
        $libelle_ATC = $this->_ref_object->_ref_produit->_ref_ATC_2_libelle;
        $this->_cible = $libelle_ATC;
        if(!isset($cibles["ATC"]) || !in_array($libelle_ATC, $cibles["ATC"])){
          $cibles["ATC"][] = $libelle_ATC;
        }
      }
      
      // Ligne d'element, cible => categorie
      if($this->object_class == "CPrescriptionLineElement"){
        $category = $this->_ref_object->_ref_element_prescription->_ref_category_prescription;
        $this->_cible = $category->_view;
        $cibles["CCategoryPrescription"][$category->_id] = $category->_view;
      }
      
      // Administration => ATC ou categorie
      if($this->object_class == "CAdministration"){
        if($this->_ref_object->object_class == "CPrescriptionLineMedicament"){
          $this->_ref_object->loadTargetObject();
          $libelle_ATC = $this->_ref_object->_ref_object->_ref_produit->_ref_ATC_2_libelle;
	        $this->_cible = $libelle_ATC;
	        if(!isset($cibles["ATC"]) || !in_array($libelle_ATC, $cibles["ATC"])){
	          $cibles["ATC"][] = $libelle_ATC;
	        }
        }
        if($this->_ref_object->object_class == "CPrescriptionLineElement"){
          $this->_ref_object->loadTargetObject();
          $category = $this->_ref_object->_ref_object->_ref_element_prescription->_ref_category_prescription;
          $this->_cible = $category->_view;
          $cibles["CCategoryPrescription"][$category->_id] = $category->_view;
        }
      }
      
      if($this->object_class == "CCategoryPrescription"){
        $this->_cible = $this->_ref_object->_view;
        $cibles[$this->object_class][$this->object_id] = $this->_ref_object->_view;
      }
      
      if($this->object_class == "CPrescriptionLineMix"){
        $this->_cible = "prescription_line_mix";
        $cibles["perf"][0] = "prescription_line_mix";
      }
    }
    
    if($this->libelle_ATC){
      $this->_cible = $this->libelle_ATC;
      if(!isset($cibles["ATC"]) || !in_array($this->libelle_ATC, $cibles["ATC"])){
        $cibles["ATC"][] = $this->libelle_ATC;
      }
    }
  }
  
  function getPerm($perm) {
    if(!isset($this->_ref_sejour->_id)) {
      $this->loadRefsFwd();
    }
    return $this->_ref_sejour->getPerm($perm);
  }
}

?>