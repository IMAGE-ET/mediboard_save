<?php /* $Id: patients.class.php 2242 2007-07-11 10:21:19Z mytto $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: 2242 $
* @author Romain Ollivier
*/


/**
 * Dossier M�dical li�s aux notions d'ant�c�dents, traitements, addictions et diagnostics
 */
class CDossierMedical extends CMbMetaObject {
  // DB Fields
  var $dossier_medical_id = null;
  var $codes_cim          = null;
  
  // Form Fields
  var $_added_code_cim   = null;
  var $_deleted_code_cim = null;
  var $_codes_cim        = null;
  var $_ext_codes_cim    = null;

  // Back references
  var $_ref_antecedents = null;
  var $_ref_traitements = null;
  var $_ref_addictions  = null;
  
  // Derived back references
  var $_ref_types_addiction  = null;
  
	function CDossierMedical() {
		$this->CMbObject("dossier_medical", "dossier_medical_id");    
    $this->loadRefModule(basename(dirname(__FILE__)));
 	}
  
  
  function getSpecs() {
    $specs = parent::getSpecs();

    $specs["codes_cim"]         = "text";
    
    return $specs;
  }  
  function getBackRefs() {
    $backRefs = parent::getBackRefs();
    $backRefs["antecedents"] = "CAntecedent dossier_medical_id";
    $backRefs["addictions" ] = "CAddiction dossier_medical_id";
    $backRefs["traitements"] = "CTraitement dossier_medical_id";
    return $backRefs;
  }

  function loadRefsBack() {
    parent::loadRefsBack();
    $this->loadRefsAntecedents();
    $this->loadRefsTraitements();
    $this->loadRefsAddictions();
  }
  
  function loadRefObject(){  
    $this->_ref_object = new $this->object_class;
    $this->_ref_object->load($this->object_id);
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    // Tokens CIM
    $this->codes_cim = strtoupper($this->codes_cim);
    $this->_codes_cim = $this->codes_cim ? explode("|", $this->codes_cim) : array();
  
    // Objets CIM
    $this->_ext_codes_cim = array();
    foreach ($this->_codes_cim as $code_cim) {
      $this->_ext_codes_cim[$code_cim] = new CCodeCIM10($code_cim, 1);
    }
  }

  function updateDBFields() {
    parent::updateDBFields();
    if(!$listCodesCim = $this->codes_cim) {
      $oldDossier = new CDossierMedical();
      $oldDossier->load($this->_id);
      $listCodesCim = $oldDossier->codes_cim;
    }
    if($this->_added_code_cim) {
      if($listCodesCim) {
        $this->codes_cim = "$listCodesCim|$this->_added_code_cim";
      } else {
        $this->codes_cim = $this->_added_code_cim;
      }
    }
    if($this->_deleted_code_cim) {
      $arrayCodesCim = explode("|", $listCodesCim);
      CMbArray::removeValue($this->_deleted_code_cim, $arrayCodesCim);
      $this->codes_cim = implode("|", $arrayCodesCim);
    }
  }
    
  function loadRefsAntecedents() {
    $order = "type ASC";
    if (null == $antecedents = $this->loadBackRefs("antecedents", $order)) {
      return;
    }

    // Classements des ant�c�dents
    foreach ($antecedents as $_antecedent) {
      $this->_ref_antecedents[$_antecedent->type][$_antecedent->_id] = $_antecedent;
    }
  }

  function loadRefsTraitements() {
    $order = "fin DESC, debut DESC";
    $this->_ref_traitements = $this->loadBackRefs("traitements", $order);
  }
  
  function loadRefsAddictions() {
    $order = "type ASC";
    if (null == $this->_ref_addictions = $this->loadBackRefs("addictions", $order)) {
      return;
    }

    // Classement des addictions
    $this->_ref_types_addiction = array();
    foreach ($this->_ref_addictions as $_addiction) {
      $this->_ref_types_addiction[$_addiction->type][$_addiction->_id] = $_addiction;
    }
  }
  
  /**
   * Identifiant de dossier m�dical li� � l'objet fourni. 
   * Cr�e le dossier m�dical si n�cessaire
   * @param $object_id ref Identifiant de l'objet
   * @param $object_class str Classe de l'objet
   * @return ref|CDossierMedical
   */
  static function dossierMedicalId($object_id, $object_class) {
    $dossier = new CDossierMedical();
    $dossier->object_id    = $object_id;
    $dossier->object_class = $object_class;
    $dossier->loadMatchingObject();
    if(!$dossier->_id) {
      $dossier->store();
    }
    return $dossier->_id;
  }

  function fillTemplate(&$template, $champ = "Patient") {
    global $AppUI;
    
    // Ant�c�dents
    $this->loadRefsAntecedents();
    if(is_array($this->_ref_antecedents)){
      // R��critude des ant�c�dents
      $sAntecedents = null;
      foreach($this->_ref_antecedents as $keyAnt=>$currTypeAnt){
        if($currTypeAnt){
          if($sAntecedents){$sAntecedents.="<br />";}
          $sAntecedents .= $AppUI->_("CAntecedent.type.".$keyAnt)."\n";
          foreach($currTypeAnt as $currAnt){
            $sAntecedents .= " &bull; ";
            if($currAnt->date){
              $sAntecedents .= substr($currAnt->date, 8, 2) ."/";
              $sAntecedents .= substr($currAnt->date, 5, 2) ."/";
              $sAntecedents .= substr($currAnt->date, 0, 4) ." : ";
            }
            $sAntecedents .= $currAnt->rques;
          }
        }
      }
      $template->addProperty("$champ - ant�c�dents", $sAntecedents);
    }else{
      $template->addProperty("$champ - ant�c�dents");
    }
    

    // Traitements
    $this->loadRefsTraitements();
    if($this->_ref_traitements){
      $sTrmt = null;
      foreach($this->_ref_traitements as $curr_trmt){
        if($sTrmt){$sTrmt.=" &bull; ";}
        if ($curr_trmt->fin){
          $sTrmt .= "Du ";
          $sTrmt .= substr($curr_trmt->debut, 8, 2) ."/";
          $sTrmt .= substr($curr_trmt->debut, 5, 2) ."/";
          $sTrmt .= substr($curr_trmt->debut, 0, 4) ." au ";
          $sTrmt .= substr($curr_trmt->fin, 8, 2) ."/";
          $sTrmt .= substr($curr_trmt->fin, 5, 2) ."/";
          $sTrmt .= substr($curr_trmt->fin, 0, 4) ." : ";
        }elseif($curr_trmt->debut){
          $sTrmt .= "Depuis le ";
          $sTrmt .= substr($curr_trmt->debut, 8, 2) ."/";
          $sTrmt .= substr($curr_trmt->debut, 5, 2) ."/";
          $sTrmt .= substr($curr_trmt->debut, 0, 4) ." : ";
        }
        $sTrmt .= $curr_trmt->traitement;
      }
      $template->addProperty("$champ - traitements", $sTrmt);
    }else{
      $template->addProperty("$champ - traitements");
    }
    
    
    // Addictions
    $this->loadRefsAddictions();
    if(is_array($this->_ref_types_addiction)){
      // R��critude des addictions
      $sAddictions = null;
      foreach($this->_ref_types_addiction as $typeAdd=>$listAdd){
        if($listAdd){
          $sAddictions .= $AppUI->_("CAddiction.type.".$typeAdd)."\n";
          foreach($listAdd as $key => $curr_add){
            $sAddictions .= " &bull; ";
            $sAddictions .= $curr_add->addiction;
          }
        }
      }
      $template->addProperty("$champ - addictions", $sAddictions);
    }else{
      $template->addProperty("$champ - addictions");
    }
    
    
    // Codes CIM10
    $aCim10 = array();
    if ($this->_ext_codes_cim){
      foreach ($this->_ext_codes_cim as $curr_code){
        $aCim10[] = "$curr_code->code : $curr_code->libelle";
      }
    }
    
    $template->addProperty("$champ - diagnostics", join("&bull;", $aCim10));
  }
}

?>