<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPqualite
* @version $Revision:  $
* @author S�bastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m, $g;


$doc_ged_id = 0;
$file_id = null;
$_validation   = mbGetValueFromPost("_validation", null);

class CDoDocGedAddEdit extends CDoObjectAddEdit {
  function CDoDocGedAddEdit() {
    $this->CDoObjectAddEdit("CDocGed", "doc_ged_id");
    
    $this->createMsg = "Proc�dure cr��e";
    $this->modifyMsg = "Proc�dure modifi�e";
    $this->deleteMsg = "Proc�dure supprim�e";
  }

  function doBind() {
    global $AppUI;
    
    $this->ajax = mbGetValueFromPost("ajax");
    $this->suppressHeaders = mbGetValueFromPost("suppressHeaders");
    $this->callBack = mbGetValueFromPost("callback");
    unset($_POST["ajax"]);
    unset($_POST["suppressHeaders"]);
    unset($_POST["callback"]);
    
    // UTF8 issue for Ajax
    if ($this->ajax) {
      foreach($_POST as $key => $value) {
        $_POST[$key] = utf8_decode($value);
      }
    }
    
    // Object binding
    if (!$this->_obj->bind( $_POST["ged"] )) {
      $AppUI->setMsg( $this->_obj->getError(), UI_MSG_ERROR );
      if ($this->redirectError) {
        $this->redirect =& $this->redirectError;
      }
      $this->doRedirect();
    }
    
    $this->_objBefore->load($this->_obj->_id);
  }
  
  function doStore() {
    global $AppUI, $doc_ged_id, $file_id, $_validation;
    
    if($this->_obj->doc_ged_id){
      // Proc�dure Existante --> Verification

      if($this->_objBefore->etat == CDOC_REDAC && $_validation===null){
        // Test d'upload du fichier
        $objFile = new CFileAddEdit;
        $objFile->redirect = null;
        $objFile->doIt();
        if($AppUI->msgNo != UI_MSG_OK){
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

    if($this->_objBefore->etat == CDOC_DEMANDE && $this->_obj->etat == CDOC_REDAC && !$this->_objBefore->num_ref){
      // Pas de num�ro : Nouvelle Proc�dure --> R�cup n� dernier doc dans meme chapitre et cat�gorie
      $this->_obj->version = 1;
        
      $sql = "SELECT num_ref FROM doc_ged WHERE num_ref IS NOT NULL";
      $where = array();
      $where["num_ref"]          = "IS NOT NULL";
      $where["doc_chapitre_id"]  = "= '".$this->_obj->doc_chapitre_id."'";
      $where["doc_categorie_id"] = "= '".$this->_obj->doc_categorie_id."'";
      $order = "num_ref DESC";
      $lastNumRef = new CDocGed;
      $lastNumRef->loadObject($where,$order);
      if(!$lastNumRef->doc_ged_id){
        $this->_obj->num_ref = 1;
      }else{
        $this->_obj->num_ref = intval($lastNumRef->num_ref) +1;
      }
    }
    
    if(!($this->_objBefore->etat == CDOC_VALID && $this->_obj->etat == CDOC_TERMINE)){
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
    
    $this->createMsg = "Suivi de proc�dure cr��";
    $this->modifyMsg = "Suivi de proc�dure modifi�";
    $this->deleteMsg = "Suivi de proc�dure supprim�";
  }

  function doBind() {
    global $AppUI;
    
    $this->ajax = mbGetValueFromPost("ajax");
    $this->suppressHeaders = mbGetValueFromPost("suppressHeaders");
    $this->callBack = mbGetValueFromPost("callback");
    unset($_POST["ajax"]);
    unset($_POST["suppressHeaders"]);
    unset($_POST["callback"]);
    
    // UTF8 issue for Ajax
    if ($this->ajax) {
      foreach($_POST as $key => $value) {
        $_POST[$key] = utf8_decode($value);
      }
    }
    
    // Object binding
    if (!$this->_obj->bind( $_POST["suivi"] )) {
      $AppUI->setMsg( $this->_obj->getError(), UI_MSG_ERROR );
      if ($this->redirectError) {
        $this->redirect =& $this->redirectError;
      }
      $this->doRedirect();
    }
    
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