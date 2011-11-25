<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage dPpatients
 *  @version $Revision$
 *  @author Fabien M�nager
 */

class CConstantesMedicales extends CMbObject {
  // DB Table key
  var $constantes_medicales_id = null;

  // DB Fields
  var $patient_id            = null;
  var $datetime              = null;
  var $context_class         = null;
  var $context_id            = null;
  var $comment               = null;

  // Object References
  //    Single
  var $_ref_context          = null;
  var $_ref_patient          = null;
  
  // Forms fields
  var $_imc_valeur           = null;
  var $_vst                  = null;
  var $_new_constantes_medicales = null;
  
  // Other fields
  var $_ref_user             = null;
  
  static $_specs_converted = false;
  static $_latest_values = array();
  
  static $list_constantes = array (
    "poids"             => array(
      "type" => "physio",
      "unit" => "kg", 
      "callback" => "calculImcVst", 
      "min" => "@-2", "max" => "@+2",
    ), 
    "taille"            => array(
      "type" => "physio",
      "unit" => "cm", 
      "callback" => "calculImcVst", 
      "min" => "@-5", "max" => "@+5",
    ),
    "pouls"             => array(
      "type" => "physio",
      "unit" => "puls./min", 
      "min" => 50, "max" => 120,
      "standard" => 60,
      "colors" => array("black")
    ), 
    "ta"                => array(
      "type" => "physio",
      "unit" => "cmHg", 
      "formfields" => array("_ta_systole", "_ta_diastole"), 
      "min" => -4, "max" => 25,
      "standard" => 8,
      "colors" => array("#00A8F0", "#C0D800"),
      "conversion" => array("mmHg" => 10),
      "candles" => true,
    ),
    "ta_gauche"         => array(
      "type" => "physio",
      "unit" => "cmHg", 
      "formfields" => array("_ta_gauche_systole", "_ta_gauche_diastole"), 
      "min" => -4, "max" => 25,
      "standard" => 8,
      "colors" => array("#00A8F0", "#C0D800"),
      "conversion" => array("mmHg" => 10),
      "candles" => true,
    ),
    "ta_droit"          => array(
      "type" => "physio",
      "unit" => "cmHg", 
      "formfields" => array("_ta_droit_systole", "_ta_droit_diastole"), 
      "min" => -4, "max" => 25,
      "standard" => 8,
      "colors" => array("#00A8F0", "#C0D800"),
      "conversion" => array("mmHg" => 10),
      "candles" => true,
    ),
    "_vst"              => array(
      "type" => "physio",
      "unit" => "ml",
      "min" => 5000,
      "max" => 7000,
    ),
    "_imc"              => array(
      "type" => "physio",
      "unit" => "",
      "min" => 12, "max" => 40,
      "plot" => true,
    ),
    "temperature"       => array(
      "type" => "physio",
      "unit" => "�C", 
      "min" => 36, "max" => 40,
      "standard" => 37.5,
      "colors" => array("orange")
    ), 
    "spo2"              => array(
      "type" => "physio",
      "unit" => "%", 
      "min" => 70, "max" => 100
    ), 
    "score_sensibilite" => array(
      "type" => "physio",
      "unit" => "", 
      "min" => 0, "max" => 5
    ),
    "score_motricite"   => array(
      "type" => "physio",
      "unit" => "", 
      "min" => 0, "max" => 5
    ),
    "score_sedation"    => array(
      "type" => "physio",
      "unit" => "", 
      "min" => 70, "max" => 100
    ),
    "frequence_respiratoire"=> array(
      "type" => "physio",
      "unit" => "", 
      "min" => 0, "max" => 60
    ),
    "EVA"               => array(
      "type" => "physio",
      "unit" => "", 
      "min" => 0, "max" => 10
    ),
    "glycemie"          => array(
      "type" => "physio",
      "unit" => "g/l", 
      "min" => 0, "max" => 4
    ),
    "PVC"               => array(
      "type" => "physio",
      "unit" => "cm H2O",
      "min" => 4, "max" => 16
    ),
    "perimetre_abdo"    => array(
      "type" => "physio",
      "unit" => "cm",
      "min" => 20, "max" => 200
    ),
    "perimetre_cuisse" => array(
      "type" => "physio",
      "unit" => "cm",
      "min" => 20, "max" => 100
    ),
    "perimetre_cou"    => array(
      "type" => "physio",
      "unit" => "cm",
      "min" => 20, "max" => 50
    ),
    "perimetre_thoracique"=>array(
      "type" => "physio",
      "unit" => "cm",
      "min" => 20, "max" => 150
    ),
    "injection"         => array(
      "type" => "physio",
      "unit" => "", 
      "formfields" => array("_inj", "_inj_essai"), 
      "min" => 0, "max" => 10
    ),
    
    
    /// DRAINS ///
    "sng"           => array(
      "type" => "drain",
      "unit" => "ml", 
      "min" => 0, "max" => 1000,
      "cumul_reset_config" => "sng_cumul_reset_hour",
    ),
    "redon"             => array(
      "type" => "drain",
      "unit" => "ml", 
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "redon_cumul_reset_hour",
    ),
    "redon_2"           => array(
      "type" => "drain",
      "unit" => "ml", 
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "redon_cumul_reset_hour",
    ),
    "redon_3"           => array(
      "type" => "drain",
      "unit" => "ml", 
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "redon_cumul_reset_hour",
    ),
    "redon_4"           => array(
      "type" => "drain",
      "unit" => "ml", 
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "redon_cumul_reset_hour",
    ),
    "lame_1"           => array(
      "type" => "drain",
      "unit" => "ml", 
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "lame_cumul_reset_hour",
    ),
    "lame_2"           => array(
      "type" => "drain",
      "unit" => "ml", 
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "lame_cumul_reset_hour",
    ),
    "lame_3"           => array(
      "type" => "drain",
      "unit" => "ml", 
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "lame_cumul_reset_hour",
    ),
    "drain_1"           => array(
      "type" => "drain",
      "unit" => "ml", 
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "drain_cumul_reset_hour",
    ),
    "drain_2"           => array(
      "type" => "drain",
      "unit" => "ml", 
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "drain_cumul_reset_hour",
    ),
    "drain_3"           => array(
      "type" => "drain",
      "unit" => "ml", 
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "drain_cumul_reset_hour",
    ),
    "drain_thoracique_1" => array(
      "type" => "drain",
      "unit" => "ml", 
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "drain_thoracique_cumul_reset_hour",
    ),
    "drain_thoracique_2" => array(
      "type" => "drain",
      "unit" => "ml", 
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "drain_thoracique_cumul_reset_hour",
    ),
    "drain_pleural_1"   => array(
      "type" => "drain",
      "unit" => "ml", 
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "drain_pleural_cumul_reset_hour",
    ),
    "drain_pleural_2"   => array(
      "type" => "drain",
      "unit" => "ml", 
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "drain_pleural_cumul_reset_hour",
    ),
    "drain_mediastinal" => array(
      "type" => "drain",
      "unit" => "ml", 
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "drain_mediastinal_cumul_reset_hour",
    ),
    
    // DIURESE ///////
    "_diurese"              => array( // Diur�se reelle, calcul�
      "type" => "drain",
      "unit" => "ml",
      "min" => 0, "max" => 1000,
      "plot" => true,
      "color" => "#00A8F0",
      "cumul_reset_config" => "diuere_24_reset_hour",
      "formula" => array(
        "diurese"            => "+",  // Miction naturelle
        "sonde_ureterale_1"  => "+", 
        "sonde_ureterale_2"  => "+", 
        "sonde_vesicale"     => "+", 
        "catheter_suspubien" => "+", 
        "entree_lavage"      => "-",
      ),
    ),
    "sonde_ureterale_1" => array(
      "type" => "drain",
      "unit" => "ml", 
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "sonde_ureterale_cumul_reset_hour",
    ),
    "sonde_ureterale_2" => array(
      "type" => "drain",
      "unit" => "ml", 
      "min" => 0, "max" => 100,
      "cumul_reset_config" => "sonde_ureterale_cumul_reset_hour",
    ),
    "sonde_vesicale"    => array(
      "type" => "drain",
      "unit" => "ml", 
      "min" => 0, "max" => 200,
      "cumul_reset_config" => "sonde_vesicale_cumul_reset_hour",
    ),
    "catheter_suspubien" => array(
      "type" => "drain",
      "unit" => "ml", 
      "min" => 0, "max" => 200,
    ),
    "diurese"           => array( // Miction naturelle
      "type" => "drain",
      "unit" => "ml", 
      "min" => 0, "max" => 1000,
      "cumul_reset_config" => "diuere_24_reset_hour",
    ),
    "entree_lavage" => array(
      "type" => "drain",
      "unit" => "ml", 
      "min" => 0, "max" => 200,
    ),
    // FIN DIURESE ////////
  );
  
  static $list_constantes_type = array(
    "physio" => array(),
    "drain" => array(),
  );
  
  function __construct() {
    foreach(self::$list_constantes as $_constant => $_params) {
      $this->$_constant = null;
      
      // Champs "composites"
      if (isset($_params["formfields"])) {
        foreach ($_params["formfields"] as $_formfield) {
          $this->$_formfield = null;
        }
      }
    }
    
    parent::__construct();
    
    // Conversion des specs
    if (self::$_specs_converted) return;
    
    foreach(self::$list_constantes as $_constant => &$_params) {
      $unit = "mmHg";

      if (isset($_params["conversion"][$unit])) {
        if (in_array($_constant, array("ta", "ta_gauche", "ta_droit"))) {
          if (CAppUI::conf("dPpatients CConstantesMedicales unite_ta") == "cmHg") {
            continue;
          }
        }
        $conv = $_params["conversion"][$unit];
        
        if (isset($_params["formfields"])) {
          foreach ($_params["formfields"] as $_formfield) {
            $spec = $this->_specs[$_formfield];
            $this->_specs[$_formfield]->prop = preg_replace_callback("/min\|([0-9]+)/", create_function('$matches', 'return "min|".$matches[1]*10;'), $spec);
            $this->_specs[$_formfield]->prop = preg_replace_callback("/max\|([0-9]+)/", create_function('$matches', 'return "max|".$matches[1]*10;'), $spec);
            
            if (isset($spec->min)) $spec->min *= $conv;
            if (isset($spec->max)) $spec->max *= $conv;
          }
        }
        else {
          $spec = $this->_specs[$_constant];
          $this->_specs[$_formfield]->prop = preg_replace_callback("/min\|([0-9]+)/", create_function('$matches', 'return "min|".$matches[1]*10;'), $spec);
          $this->_specs[$_formfield]->prop = preg_replace_callback("/max\|([0-9]+)/", create_function('$matches', 'return "max|".$matches[1]*10;'), $spec);
          
          if (isset($spec->min)) $spec->min *= $conv;
          if (isset($spec->max)) $spec->max *= $conv;
        }
        $_params["unit"] = $unit;
      }
    }
    
    self::$_specs_converted = true;
  }

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'constantes_medicales';
    $spec->key   = 'constantes_medicales_id';
    return $spec;
  }

  function getProps() {
    $specs = parent::getProps();
    $specs['patient_id']             = 'ref notNull class|CPatient';
    $specs['datetime']               = 'dateTime notNull';
    $specs['context_class']          = 'str';
    $specs['context_id']             = 'ref class|CMbObject meta|context_class cascade';
    $specs['comment']                = 'text';
    
    $specs['poids']                  = 'float pos';
    $specs['taille']                 = 'float pos';
    
    $specs['ta']                     = 'str maxLength|10';
    $specs['_ta_systole']            = 'num pos max|50';
    $specs['_ta_diastole']           = 'num pos max|50';
    
    $specs['ta_gauche']              = 'str maxLength|10';
    $specs['_ta_gauche_systole']     = 'num pos max|50';
    $specs['_ta_gauche_diastole']    = 'num pos max|50';
    
    $specs['ta_droit']               = 'str maxLength|10';
    $specs['_ta_droit_systole']      = 'num pos max|50';
    $specs['_ta_droit_diastole']     = 'num pos max|50';
    
    $specs['pouls']                  = 'num pos';
    $specs['spo2']                   = 'float min|0 max|100';
    $specs['temperature']            = 'float min|20 max|50'; // Au cas ou il y aurait des malades tr�s malades
    $specs['score_sensibilite']      = 'float min|0 max|5';
    $specs['score_motricite']        = 'float min|0 max|5';
    $specs['EVA']                    = 'float min|0 max|10';
    $specs['score_sedation']         = 'float';
    $specs['frequence_respiratoire'] = 'float pos';
    $specs['glycemie']               = 'float pos max|10';
    $specs['PVC']                    = 'float min|0';
    $specs['perimetre_abdo']         = 'float min|0';
    $specs['perimetre_cuisse']       = 'float min|0';
    $specs['perimetre_cou']          = 'float min|0';
    $specs['perimetre_thoracique']   = 'float min|0';
    $specs['_imc']                   = 'float pos';
    $specs['_vst']                   = 'float pos';
    
    $specs['injection']              = 'str maxLength|10';
    $specs['_inj']                   = 'num pos';
    $specs['_inj_essai']             = 'num pos moreEquals|_inj';
    
    $specs['redon']                  = 'float pos min|0';
    $specs['redon_2']                = 'float pos min|0';
    $specs['redon_3']                = 'float pos min|0';
    $specs['redon_4']                = 'float pos min|0';
    $specs['diurese']                = 'float min|0'; // Miction naturelle
    $specs['_diurese']               = 'float min|0'; // Vraie diur�se (calcul�e)
    $specs['sng']                    = 'float pos min|0';
    $specs['lame_1']                 = 'float pos min|0';
    $specs['lame_2']                 = 'float pos min|0';
    $specs['lame_3']                 = 'float pos min|0';
    $specs['drain_1']                = 'float pos min|0';
    $specs['drain_2']                = 'float pos min|0';
    $specs['drain_3']                = 'float pos min|0';
    $specs['drain_thoracique_1']     = 'float pos min|0';
    $specs['drain_thoracique_2']     = 'float pos min|0';
    $specs['drain_pleural_1']        = 'float pos min|0';
    $specs['drain_pleural_2']        = 'float pos min|0';
    $specs['drain_mediastinal']      = 'float pos min|0';
    $specs['sonde_ureterale_1']      = 'float pos min|0';
    $specs['sonde_ureterale_2']      = 'float pos min|0';
    $specs['sonde_vesicale']         = 'float pos min|0';
    $specs['catheter_suspubien']     = 'float pos min|0';
    $specs['entree_lavage']          = 'float pos min|0';
    
    return $specs;
  }

  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["administrations"]   = "CAdministration constantes_medicales_id";
    return $backProps;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->loadRefPatient();
    
    // Calcul de l'Indice de Masse Corporelle
    if($this->poids && $this->taille) {
      $this->_imc = round($this->poids / ($this->taille * $this->taille * 0.0001), 2);
    }
    
    // D�termination valeur IMC
    if ($this->poids && $this->taille) {
      $seuils = ($this->_ref_patient->sexe != 'm') ? 
        array(19, 24): 
        array(20, 25);
      
      if ($this->_imc < $seuils[0]) {
        $this->_imc_valeur = 'Maigreur';
      } 
      elseif ($this->_imc > $seuils[1] && $this->_imc <= 30) {
        $this->_imc_valeur = 'Surpoids';
      }
      elseif ($this->_imc > 30 && $this->_imc <= 40) {
        $this->_imc_valeur = 'Ob�sit�';
      }
      elseif ($this->_imc > 40) {
        $this->_imc_valeur = 'Ob�sit� morbide';
      }
    }
    
    // Calcul du Volume Sanguin Total
    if ($this->poids) {
      $this->_vst = (($this->_ref_patient->sexe != 'm') ? 65 : 70) * $this->poids;
    }
    
    $unite_ta = CAppUI::conf("dPpatients CConstantesMedicales unite_ta");
    
    $_ta = explode('|', $this->ta);
    if ($this->ta && isset($_ta[0]) && isset($_ta[1])) {
      $this->_ta_systole  = $_ta[0];
      $this->_ta_diastole = $_ta[1];
      if ($unite_ta == "mmHg") {
        $this->_ta_systole *= 10;
        $this->_ta_diastole *= 10;
      }
    }
    
    $_ta_gauche = explode('|', $this->ta_gauche);
    if ($this->ta_gauche && isset($_ta_gauche[0]) && isset($_ta_gauche[1])) {
      $this->_ta_gauche_systole  = $_ta_gauche[0];
      $this->_ta_gauche_diastole = $_ta_gauche[1];
      if ($unite_ta == "mmHg") {
        $this->_ta_gauche_systole *= 10;
        $this->_ta_gauche_diastole *= 10;
      }
    }

    $_ta_droit = explode('|', $this->ta_droit);
    if ($this->ta_droit && isset($_ta_droit[0]) && isset($_ta_droit[1])) {
      $this->_ta_droit_systole  = $_ta_droit[0];
      $this->_ta_droit_diastole = $_ta_droit[1];
      if ($unite_ta == "mmHg") {
        $this->_ta_droit_systole *= 10;
        $this->_ta_droit_diastole *= 10;
      }
    }
    
    $_injection = explode('|', $this->injection);
    if ($this->injection && isset($_injection[0]) && isset($_injection[1])) {
      $this->_inj  = $_injection[0];
      $this->_inj_essai = $_injection[1];
    }
  }
  
  function updatePlainFields() {
    // TODO: Utiliser les specs
    
    $unite_ta = CAppUI::conf("dPpatients CConstantesMedicales unite_ta");
    
    if (!empty($this->_ta_systole) && !empty($this->_ta_diastole)) {
      if ($unite_ta ==  "mmHg") {
        $this->_ta_systole /= 10;
        $this->_ta_diastole /= 10;
      }
      $this->ta = "$this->_ta_systole|$this->_ta_diastole";
    }
    if ($this->_ta_systole === '' && $this->_ta_diastole === '') {
      $this->ta = '';
    }
    
    if (!empty($this->_ta_gauche_systole) && !empty($this->_ta_gauche_diastole)) {
      if ($unite_ta == "mmHg") {
        $this->_ta_gauche_systole /= 10;
        $this->_ta_gauche_diastole /= 10;
      }
      $this->ta_gauche = "$this->_ta_gauche_systole|$this->_ta_gauche_diastole";
    }
    if ($this->_ta_gauche_systole === '' && $this->_ta_gauche_diastole === '') {
      $this->ta_gauche = '';
    }
    
    if (!empty($this->_ta_droit_systole) && !empty($this->_ta_droit_diastole)) {
      if ($unite_ta ==  "mmHg") {
        $this->_ta_droit_systole /= 10;
        $this->_ta_droit_diastole /= 10;
      }
      $this->ta_droit = "$this->_ta_droit_systole|$this->_ta_droit_diastole";
    }
    if ($this->_ta_droit_systole === '' && $this->_ta_droit_diastole === '') {
      $this->ta_droit = '';
    }
    
    if (!empty($this->_inj) && !empty($this->_inj_essai)) {
      $this->injection = "$this->_inj|$this->_inj_essai";
    }
    if ($this->_inj === '' && $this->_inj_essai === '') {
      $this->injection = '';
    }
  }
  
  function loadRefContext() {
    if ($this->context_class && $this->context_id) {
      $this->_ref_context = new $this->context_class;
      $this->_ref_context = $this->_ref_context->getCached($this->context_id);
    }
  }
  
  function loadRefPatient() {
    $this->_ref_patient = new CPatient;
    $this->_ref_patient = $this->_ref_patient->getCached($this->patient_id);
  }

  function loadRefsFwd() {
    $this->loadRefContext();
    $this->loadRefPatient();
  }
  
  function loadRefUser() {
    $first_log = $this->loadFirstLog();
    $this->_ref_user = $first_log->loadRefUser();
  }
  
  function check() {
    if ($msg = parent::check()) {
      return $msg;
    }
    
    // Verifie si au moins une des valeurs est remplie
    $ok = false;
    foreach (CConstantesMedicales::$list_constantes as $const => $params) {
      $this->completeField($const);
      if ($this->$const !== "" && $this->$const !== null) {
        $ok = true;
        break;
      }
    }
    if (!$ok) return 'Au moins une des valeurs doit �tre renseign�e';
  }
  
  function store () {
    // S'il ne reste plus qu'un seul champ et que sa valeur est pass�e � vide,
    // alors on supprime la constante.
    if ($this->_id) {
      $ok = false;
      foreach (CConstantesMedicales::$list_constantes as $const => $params) {
        $this->completeField($const);
        if ($this->$const !== "" && $this->$const !== null) {
          $ok = true;
          break;
        }
      }
      if (!$ok)
        return parent::delete();
    }
    
    if (!$this->_id && !$this->_new_constantes_medicales) {
      $this->updatePlainFields();
      $constante = new CConstantesMedicales();
      $constante->patient_id    = $this->patient_id;
      $constante->context_class = $this->context_class;
      $constante->context_id    = $this->context_id;
      
      if ($constante->loadMatchingObject()) {
        foreach (CConstantesMedicales::$list_constantes as $type => $params) {
          if (empty($this->$type) && !empty($constante->$type)) {
            $this->$type = $constante->$type;
          }
        }
        $this->_id = $constante->_id;
      }
    }
    return parent::store();
  }
  
  static function getLatestFor($patient) {
    $patient_id = ($patient instanceof CPatient) ? $patient->_id : $patient;
    
    if (isset(self::$_latest_values[$patient_id])) {
      return self::$_latest_values[$patient_id];
    }
    
    // Constante que l'on va construire
    $constante = new CConstantesMedicales();
    if(!$patient_id) {
      return array($constante, array());
    }
    
    $constante->patient_id = $patient_id;
    $constante->datetime = mbDateTime();
    $constante->loadRefPatient();
    
    $where = array(
      "patient_id" => "= '$patient_id'"
    );
    
    $list_datetimes = array();
    foreach (CConstantesMedicales::$list_constantes as $type => $params) {
      $list_datetimes[$type] = null;
      
      if ($type[0] == "_") continue;
      
      $_where = $where;
      $_where[$type] = "IS NOT NULL";
      $_list = $constante->loadList($_where, "datetime DESC", 1);
      
      if (count($_list)) {
        $_const = reset($_list);
        $constante->$type = $_const->$type;
        $list_datetimes[$type] = $_const->datetime;
      }
    }
    
    $constante->updateFormFields();
    
    return self::$_latest_values[$patient_id] = array($constante, $list_datetimes);
  }
  
  static function buildGrid($list, $full = true) {
    $grid = array();
    $selection = array_keys(CConstantesMedicales::$list_constantes);
    $cumuls_day = array();
    
    if (!$full) {
      $conf_constantes = explode("|", CConstantesMedicales::getConfig("important_constantes"));
      $selection = $conf_constantes;
      
      foreach ($list as $_constante_medicale) {
        foreach (CConstantesMedicales::$list_constantes as $_name => $_params) {
          if ($_constante_medicale->$_name != '' && isset($_params["cumul_reset_config"])) {
            $selection[] = "_{$_name}_cumul";
          }
        }
      }
      
      $selection = array_unique($selection);
    }
    
    $names = $selection;
    
    foreach ($list as $_constante_medicale) {
      if (!isset($grid["$_constante_medicale->datetime $_constante_medicale->_id"])) {
        $grid["$_constante_medicale->datetime $_constante_medicale->_id"] = array(
          "comment" => $_constante_medicale->comment, 
          "values"  => array(),
        );
      }
      
      foreach (CConstantesMedicales::$list_constantes as $_name => $_params) {
        if (in_array($_name, $selection) || $_constante_medicale->$_name != '') {
          $value = null;
					
          // cumul
          if (isset($_params["cumul_for"]) || isset($_params["formula"])) {
            $reset_hour = self::getResetHour($_name);
            $day_24h = mbTransformTime("-$reset_hour hours", $_constante_medicale->datetime, '%y-%m-%d');
            
              
            if (!isset($cumuls_day[$_name][$day_24h])) {
              $cumuls_day[$_name][$day_24h] = array(
                "id"    => $_constante_medicale->_id,
                "datetime" => $_constante_medicale->datetime,
                "value" => null,
                "span"  => 0, 
                "pair"  => (@count($cumuls_day[$_name]) % 2 ? "odd" : "even"),
                "day"   => mbTransformTime($day_24h, null, "%a"),
              );
            }
              
            // cumul simple sur le meme champ
            if (isset($_params["cumul_for"])) {
              $cumul_for  = $_params["cumul_for"];
              
              if ($_constante_medicale->$cumul_for !== null) {
                $cumuls_day[$_name][$day_24h]["value"] += $_constante_medicale->$cumul_for;
              }
            }
            
            // cumul de plusieurs champs (avec formule)
            else {
              $formula  = $_params["formula"];
              
              foreach($formula as $_field => $_sign) {
                $_value = $_constante_medicale->$_field;
                
                if ($_constante_medicale->$_field !== null) {
                  if ($_sign === "+") {
                    $cumuls_day[$_name][$day_24h]["value"] += $_value;
                  }
                  else {
                    $cumuls_day[$_name][$day_24h]["value"] -= $_value;
                  }
                }
              }
            }
              
            $cumuls_day[$_name][$day_24h]["span"]++;
            
            $value = "__empty__";
          }
          
          // valeur normale
          else {
            $spec = self::$list_constantes[$_name];
            $value = $_constante_medicale->$_name;
            
            if (isset($spec["formfields"])) {
              $arr = array();
              foreach($spec["formfields"] as $ff) {
                if ($_constante_medicale->$ff != "") {
                  $arr[] = $_constante_medicale->$ff;
                }
              }
              $value = implode(" / ", $arr);
            }
          }
          
          $grid["$_constante_medicale->datetime $_constante_medicale->_id"]["values"][$_name] = $value;
          
          if (!in_array($_name, $names)) {
            $names[] = $_name;
          }
        }
      }
    }
    
    foreach($cumuls_day as $_name => $_days) {
      foreach($_days as $_day => $_values) {
        $grid[$_values["datetime"]." ".$_values["id"]]["values"][$_name] = $_values;
      }
    }
    
    $names = self::sortConstNames($names);
    
    return array(
      $names, "names" => $names, 
      $grid,  "grid"  => $grid,
    );
  }
  
  static function sortConstNames($names) {
    $new_names = array();
    
    foreach(self::$list_constantes as $key => $params) {
      if (in_array($key, $names)) {
        $new_names[] = $key;
      }
    }
    
    return $new_names;
  }
  
  static function getRelated($selection, CPatient $patient, CMbObject $context = null, $date_min = null, $date_max = null, $limit = null) {
    $where = array(
      "patient_id" => " = '$patient->_id'"
    );
    
    if ($context) {
      $where["context_class"] = " = '$context->_class'";
      $where["context_id"]    = " = '$context->_id'";
    }
    
    $whereOr = array();
    foreach($selection as $name) {
      if ($name[0] === "_") continue;
      $whereOr[] = "`$name` IS NOT NULL";
    }
    $where[] = implode(" OR ", $whereOr);
    
    if ($date_min) {
      $where[] = "datetime >= '$date_min'";
    }
    
    if ($date_max) {
      $where[] = "datetime <= '$date_max'";
    }
    
    $constantes = new self;
    return array_reverse($constantes->loadList($where, "datetime DESC", $limit), true);
  }
  
  static function initParams(){
    // make a copy of the array as it will be modified
    $list_constantes = CConstantesMedicales::$list_constantes;
    
    foreach($list_constantes as $_constant => &$_params) {
      self::$list_constantes_type[$_params["type"]][$_constant] = &$_params;
      
      // Champs de cumuls
      if (isset($_params["cumul_reset_config"])) {
        if (empty($_params["formula"])) {
          CMbArray::insertAfterKey(CConstantesMedicales::$list_constantes, $_constant, "_{$_constant}_cumul", array(
            "cumul_for" => $_constant,
            "unit"      => $_params["unit"],
          ));
        }
      }
    }
  }
  
  static function getConfig($name, $group_id = null) {
    $service_id = isset($_SESSION["soins"]["service_id"]) && $_SESSION["soins"]["service_id"] ?
    $_SESSION["soins"]["service_id"] : "none";
    $configs = CConfigConstantesMedicales::getAllFor($service_id, $group_id);
    return $configs[$name];
  }
  
  static function getResetHour($name, $group_id = null) {
    $list = CConstantesMedicales::$list_constantes;
    
    if (isset($list[$name]["cumul_reset_config"])) {
      $confname = $list[$name]["cumul_reset_config"];
    }
    else {
      $confname = $list[$list[$name]["cumul_for"]]["cumul_reset_config"];
    }
    
    return self::getConfig($confname, $group_id);
  }
}

CConstantesMedicales::initParams();
