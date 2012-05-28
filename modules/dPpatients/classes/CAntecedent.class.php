<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

class CAntecedent extends CMbObject {
  // DB Table key
  var $antecedent_id = null;

  // DB fields
  var $type               = null;
  var $appareil           = null;
  var $date               = null;
  var $rques              = null;
  var $dossier_medical_id = null;
  var $annule             = null;
  
  // Form Fields
  var $_search = null;
  
  // Distant fields
  var $_count_rques_aides = null;
  
  // Types
  static $types = array(
	  'med', 'alle', 'trans', 'obst', 'deficience', 'chir', 'fam', 'anesth', 'gyn', 
	  'cardio', 'pulm', 'stomato', 'plast', 'ophtalmo', 'digestif', 'gastro', 
	  'stomie', 'uro', 'ortho', 'traumato', 'amput', 'neurochir', 'greffe', 'thrombo',
    'cutane', 'hemato', 'rhumato', 'neuropsy', 'infect', 'endocrino', 'carcino', 
    'orl', 'addiction', 'habitus', 'coag'
	);
	
	// Types that should not be types, mostly appareils
	static $non_types = array(
    'obst', 'gyn', 'cardio', 'stomato', 'digestif', 'gastro', 'stomie', 'neuropsy', 
    'endocrino', 'orl', 'uro', 'ortho', 'pulm',
	);
	
	// Appareils
	static $appareils = array(
	  'cardiovasculaire', 'digestif', 'endocrinien', 'neuro_psychiatrique',
	  'pulmonaire', 'uro_nephrologique', 'orl', 'gyneco_obstetrique', 'orthopedique',
	  'ophtalmologique', 'locomoteur',
	);
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'antecedent';
    $spec->key   = 'antecedent_id';
    return $spec;
  }

  function getProps() {
    $props = parent::getProps();
    $props["type"]  = "enum list|".CAppUI::conf("patients CAntecedent types");
    $props["appareil"] = "enum list|".CAppUI::conf("patients CAntecedent appareils");
    $props["date"]  = "date progressive";
    $props["rques"] = "text helped|type|appareil";
    $props["dossier_medical_id"] = "ref notNull class|CDossierMedical";
    $props["annule"] = "bool";
    $props["_search"] = "str";
    return $props;
  }
  
	function updateFormFields() {
		parent::updateFormFields();
		$this->_view = $this->rques;
	}
	
  function loadRefDossierMedical() { 
    $this->_ref_dossier_medical = new CDossierMedical();
    $this->_ref_dossier_medical->load($this->dossier_medical_id);
  }
  
  function loadView(){
    $this->loadLogs();
    $this->loadRefDossierMedical();
  }
  
  function store() {
    // Standard store
    if ($msg = parent::store()) {
      return $msg;
    }
    // DossierMedical store
    $this->checkCodeCim10();
  }
  
  function checkCodeCim10(){
    preg_match_all("/[A-Z]\d{2}\.?\d{0,2}/i", $this->rques, $matches);
    
    foreach($matches as $key => $match_){
      foreach($match_ as &$match){
        // Transformation du code CIM pour le tester
        $match = str_replace(".","",$match);
        $match = strtoupper($match);
        
        // Chargement du code CIM 10
        $code_cim10 = new CCodeCIM10($match, 1);
    
        if($code_cim10->libelle != "Code CIM inexistant"){
          // Cas du code valide, sauvegarde du code CIM
          $dossier_medical = new CDossierMedical();
          $dossier_medical->load($this->dossier_medical_id);
          
          // si le code n'est pas deja present, on le rajoute
          if(!array_key_exists($match, $dossier_medical->_ext_codes_cim)){
            if($dossier_medical->codes_cim != ""){
              $dossier_medical->codes_cim .= "|";
            }
            $dossier_medical->codes_cim .= $match;
            $dossier_medical->store();
          }
        }
      }
    }
  }
  
  /**
   * Add a count behaviour to parent load
   */
  function loadAides($user_id, $needle = null, $depend_value_1 = null, $depend_value_2 = null) {
    parent::loadAides($user_id, $needle, $depend_value_1, $depend_value_2);
    
    $rques_aides =& $this->_aides_all_depends["rques"];
    if (!isset($rques_aides)) {
      return;
    }

    $depend_field_1 = $this->_specs["rques"]->helped[0];
    $depend_values_1 = $this->_specs[$depend_field_1]->_list;
    asort($depend_values_1);
    $depend_values_1[] = "";
    foreach ($depend_values_1 as $depend_value_1) {
      $count =& $this->_count_rques_aides;
      $count[$depend_value_1] = 0;
      if (isset($rques_aides[$depend_value_1])) {
	      foreach ($rques_aides[$depend_value_1] as $aides_by_depend_field_2) {
	        $count[$depend_value_1] += count($aides_by_depend_field_2);
	      }
      }
    }
  }
}

?>