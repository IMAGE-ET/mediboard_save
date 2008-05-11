<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPqualite
* @version $Revision:  $
* @author S�bastien Fillonneau
*/

global $AppUI, $can, $m, $g;


$doc_ged_id = 0;
$file_id = null;
$_validation   = mbGetValueFromPost("_validation", null);

class CDoDocGedAddEdit extends CDoObjectAddEdit {
  function CDoDocGedAddEdit() {
    $this->CDoObjectAddEdit("CDocGed", "doc_ged_id");
  }

  function doBind() {
    global $AppUI;
    
    $this->ajax = mbGetValueFromPost("ajax");
    $this->suppressHeaders = mbGetValueFromPost("suppressHeaders");
    $this->callBack = mbGetValueFromPost("callback");
    unset($_POST["ajax"]);
    unset($_POST["suppressHeaders"]);
    unset($_POST["callback"]);
    
    // Object binding
    $this->_obj->bind($_POST["ged"]);
    $this->_objBefore->load($this->_obj->_id);
  }
  
  function doStore() {
    global $AppUI, $doc_ged_id, $file_id, $_validation;
    
    if($this->_obj->doc_ged_id){
      // Proc�dure Existante --> Verification

      //if($this->_objBefore->etat == CDocGed::REDAC && $_validation === null){
      if(isset($_FILES["formfile"])){
        // Test d'upload du fichier
        $objFile = new CFileAddEdit;
        $objFile->redirect = null;
        $objFile->doIt();
        if(!$AppUI->isMsgOK()){
          // Erreur sur le fichier !
          if ($this->redirectError) {
            $this->redirect =& $this->redirectError;
          }
          $this->doRedirect();
        }else{
          $file_id = $objFile->_obj->file_id;
        }
      }      
    }
    
    if($this->_objBefore->group_id && $this->_obj->doc_chapitre_id && $this->_obj->doc_categorie_id && !$this->_objBefore->num_ref){
      // Nouvelle Proc�dure
      $this->_obj->version = 1;
      if($this->_obj->num_ref) {
        $this->_obj->num_ref = intval($this->_obj->num_ref);
        // Num�rot�e manuellement
        $where = array();
        $where["num_ref"]          = "IS NOT NULL";
        $where["group_id"]         = "= '".$this->_objBefore->group_id."'";
        $where["doc_chapitre_id"]  = "= '".$this->_obj->doc_chapitre_id."'";
        $where["doc_categorie_id"] = "= '".$this->_obj->doc_categorie_id."'";
        $where["num_ref"]          = "= '".$this->_obj->num_ref."'";
        $where["annule"]           = "= '0'";
        $order = "num_ref DESC";
        $sameNumRef = new CDocGed;
        $sameNumRef->loadObject($where,$order);
        if($sameNumRef->_id) {
          $this->_obj->num_ref = null;
        }
      }
      if(!$this->_obj->num_ref) {
        // Pas de num�ro : R�cup n� dernier doc dans meme chapitre et cat�gorie
        $where = array();
        $where["num_ref"]          = "IS NOT NULL";
        $where["group_id"]         = "= '".$this->_objBefore->group_id."'";
        $where["doc_chapitre_id"]  = "= '".$this->_obj->doc_chapitre_id."'";
        $where["doc_categorie_id"] = "= '".$this->_obj->doc_categorie_id."'";
        $where["annule"]           = "= '0'";
        $order = "num_ref DESC";
        $lastNumRef = new CDocGed;
        $lastNumRef->loadObject($where,$order);
        if(!$lastNumRef->doc_ged_id){
          $this->_obj->num_ref = 1;
        }else{
          $this->_obj->num_ref = intval($lastNumRef->num_ref) + 1;
        }
      }
    }

    if(!($this->_objBefore->etat == CDocGed::VALID && $this->_obj->etat == CDocGed::TERMINE)){
      // Annulation changement de version
      $this->_obj->version = $this->_objBefore->version;
    }
    
    if ($msg = $this->_obj->store()) {
      $AppUI->setMsg($msg, UI_MSG_ERROR );
      if ($this->redirectError) {
        $this->redirect =& $this->redirectError;
      }
    }else{
      $this->redirect = null;
      $doc_ged_id = $this->_obj->doc_ged_id;
    } 
  }
    
}


class CDoDocGedSuiviAddEdit extends CDoObjectAddEdit {
  function CDoDocGedSuiviAddEdit() {
    $this->CDoObjectAddEdit("CDocGedSuivi", "doc_ged_suivi_id");
  }

  function doBind() {
    global $AppUI;
    
    $this->ajax = mbGetValueFromPost("ajax");
    $this->suppressHeaders = mbGetValueFromPost("suppressHeaders");
    $this->callBack = mbGetValueFromPost("callback");
    unset($_POST["ajax"]);
    unset($_POST["suppressHeaders"]);
    unset($_POST["callback"]);
    
    // Object binding
    $this->_obj->bind($_POST["suivi"]);
    $this->_objBefore->load($this->_obj->_id);
  }

  function doStore() {
    global $AppUI,$doc_ged_id,$file_id,$_validation;
    $this->_obj->date       = mbDateTime();
    $this->_obj->doc_ged_id = $doc_ged_id;
    if($file_id !== null){
      $this->_obj->file_id  = $file_id;
    }
    if ($msg = $this->_obj->store()) {
      $AppUI->setMsg($msg, UI_MSG_ERROR );
      if ($this->redirectError) {
        $this->redirect =& $this->redirectError;
      }
    }    
  }
    
}
$do1 = new CDoDocGedAddEdit;
$do1->doIt();

if(!$_validation){
  $do2 = new CDoDocGedSuiviAddEdit;
  $do2->doIt();
}

?>