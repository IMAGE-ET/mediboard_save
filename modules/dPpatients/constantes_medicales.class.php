<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage dPpatients
 *  @version $Revision: $
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
  var $poids                 = null;
  var $taille                = null;
  var $ta                    = null;
  var $pouls                 = null;
  var $spo2                  = null;
  var $temperature           = null;

  // Object References
  //    Single
  var $_ref_context          = null;
  var $_ref_patient          = null;
  
  // Forms fields
  var $_ta_systole           = null;
  var $_ta_diastole          = null;
  var $_imc                  = null;
  var $_imc_valeur           = null;
  var $_vst                  = null;
  var $_new_constantes_medicales = null;
  
  static $list_constantes = array('poids', 'taille', 'ta', 'pouls', 'spo2', 'temperature');

  function CConstantesMedicales() {
    $this->CMbObject('constantes_medicales', 'constantes_medicales_id');
    $this->loadRefModule(basename(dirname(__FILE__)));
  }

  function getSpecs() {
    $specs = parent::getSpecs();
    $specs['patient_id']    = 'notNull ref class|CPatient';
    $specs['datetime']      = 'notNull dateTime';
    $specs['context_class'] = 'str';
    $specs['context_id']    = 'ref class|CMbObject meta|context_class cascade';
    $specs['poids']         = 'float pos';
    $specs['taille']        = 'num pos';
    $specs['ta']            = 'str maxLength|10';
    $specs['pouls']         = 'num pos';
    $specs['spo2']          = 'float minMax|0|100';
    $specs['temperature']   = 'float minMax|20|50'; // Au cas ou il y aurait des malades tr�s malades
    $specs['_imc']          = '';
    $specs['_vst']          = '';
    $specs['_ta_systole']   = 'num';
    $specs['_ta_diastole']  = 'num';
    return $specs;
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

    $_ta = explode('|', $this->ta);
    if ($this->ta && isset($_ta[0]) && isset($_ta[1])) {
      $this->_ta_systole  = $_ta[0];
      $this->_ta_diastole = $_ta[1];
    }
  }
  
  function updateDBFields() {
    if (!empty($this->_ta_systole) && !empty($this->_ta_diastole)) {
      $this->ta = "$this->_ta_systole|$this->_ta_diastole";
    }
  }
  
  function loadRefContext() {
    if ($this->context_class && $this->context_id) {
      $this->_ref_context = new $this->context_class;
      $this->_ref_context->load($this->context_id);
    }
  }
  
  function loadRefPatient() {
    $this->_ref_patient = new CPatient;
    $this->_ref_patient->load($this->patient_id);
  }

  function loadRefsFwd() {
    $this->loadRefContext();
    $this->loadRefPatient();
  }
  
  function check() {
    if ($msg = parent::check()) {
      return $msg;
    }
    
    // Verifie si au moins une des valeurs est remplie
    $ok = false;
    foreach (CConstantesMedicales::$list_constantes as $const) {
      if ($this->$const) {
        $ok = true;
        break;
      }
    }
    if (!$ok) return 'Au moins une des valeurs doit �tre renseign�e';
  }
  function store () {
    if (!$this->_id && !$this->_new_constantes_medicales) {
      $this->updateDBFields();
      $constante = new CConstantesMedicales();
      $constante->patient_id    = $this->patient_id;
      $constante->context_class = $this->context_class;
      $constante->context_id    = $this->context_id;
      
      if ($constante->loadMatchingObject()) {
        foreach (CConstantesMedicales::$list_constantes as $type) {
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
    
    // Constante que l'on va construire
    $constante = new CConstantesMedicales();
    $constante->patient_id = $patient_id;
    $constante->datetime = mbDateTime();
    $constante->loadRefPatient();
    
    // Liste des constantes enregistr�s du patient
    $list = new CConstantesMedicales();
    $list->patient_id = $patient_id;
    $list = $list->loadMatchingList('datetime DESC');
    
    // Liste des dates des derni�res valeurs
    $list_datetimes = array();
    
    foreach (CConstantesMedicales::$list_constantes as $type) {
      $list_datetimes[$type] = null;
    }
    
    // Pour toutes les constantes existantes
    foreach ($list as $const) {
      if ($constante->context_class == null && $const->context_class != null) {
        $constante->context_class = $const->context_class;
        $constante->context_id = $const->context_id;
      }
      
      $continue = false;
      foreach (CConstantesMedicales::$list_constantes as $type) {
        if ($const->$type != null && $constante->$type == null) {
          $constante->$type = $const->$type;
          $list_datetimes[$type] = $const->datetime;
        }
        if ($constante->$type == null) {
          $continue = true;
        }
      }
      if (!$continue) break;
    }
    
    $constante->updateFormFields();
    return array($constante, $list_datetimes);
  }
}
?>