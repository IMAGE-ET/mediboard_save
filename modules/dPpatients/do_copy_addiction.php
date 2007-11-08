<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI;

class CDoCopyAddiction extends CDoObjectAddEdit {
  function CDoCopyAddiction() {
    $this->CDoObjectAddEdit("CAddiction", "addiction_id");
    
    $this->createMsg = "Addiction cr��e";
    $this->modifyMsg = "Addiction modifi�e";
    $this->deleteMsg = "Addiction supprim�e";
  }  
  
  function doBind() {
    parent::doBind();
    
    // recuperation du sejour_id
    $_sejour_id = mbGetValueFromPost("_sejour_id"  , null);

    // si pas de sejour_id, redirection
    if (!$_sejour_id){
       $this->doRedirect();
    }
    
    // Creation de la nouvelle addiction affect�e au sejour
    unset($_POST["addiction_id"]);
    $this->_obj = $this->_objBefore;
    $this->_obj->_id = null;
    $this->_obj->addiction_id = null;
    
    // Calcul de la valeur de l'id du dossier_medical du sejour
    $this->_obj->dossier_medical_id = CDossierMedical::dossierMedicalId($_sejour_id,"CSejour");
  }
}
$do = new CDoCopyAddiction;
$do->doIt();
?>