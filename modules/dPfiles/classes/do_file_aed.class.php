<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision$
* @author S�bastien Fillonneau
*/

class CFileAddEdit extends CDoObjectAddEdit {
  function CFileAddEdit() {
    global $m;    
    $selKey   = intval(CValue::post("object_id", 0));
    $selClass = CValue::post("object_class"    , "");
    
    $this->CDoObjectAddEdit("CFile", "file_id");
    
    $this->redirect = "m=$m"; 
    
    if ($dialog = CValue::post("dialog")) {
      $this->redirect      .= "&a=upload_file&dialog=1";
      $this->redirectStore = "m=$m&a=upload_file&dialog=1&uploadok=1";
    }
  }

  function bindFilePart(){
    
  }

  function doStore() {
    global $AppUI;
    $upload     = null;
    $multifiles = false;

    if (CValue::POST("_from_yoplet") == 1) {
    	$obj = $this->_obj;
    	
    	$file_name = basename(str_replace('\\', '/', $this->request['_file_path']));
    	$extension = strrchr($file_name, '.');
    	$file_rename = $this->request['file_rename'] ? $this->request['file_rename'] : 'upload';
    	$file_path = "tmp/". $this->request['_checksum'];

    	$obj->file_name = /*$file_rename == 'upload' ? */$file_name /*: $file_rename . $extension*/;
    	$obj->file_size = filesize($file_path);
    	$obj->file_owner = CAppUI::$user->_id;
      $obj->fillFields();
      $obj->updateFormFields();
      $obj->file_type = CMbPath::guessMimeType($file_name);
    	if ($msg = $obj->store()) {
    	  CAppUI::setMsg($msg, UI_MSG_ERROR);
    	} else {
    		$obj->forceDir();
    	  $obj->moveFile($file_path);
    	}
    	return parent::doStore();
    }
    
    if (isset($_FILES["formfile"])) {
      $aFiles = array();
      $upload =& $_FILES["formfile"];
      $_file_category_id = CValue::post("_file_category_id");
      
      // Plusieurs fichiers
      if (is_array($upload["name"])) {
        $multifiles = true;
        foreach($upload["error"] as $fileNumber => $etatFile){
          $aFiles[] = array(
            "name"             => $upload["name"][$fileNumber],
            "type"             => $upload["type"][$fileNumber],
            "tmp_name"         => $upload["tmp_name"][$fileNumber],
            "error"            => $upload["error"][$fileNumber],
            "size"             => $upload["size"][$fileNumber],
            "file_category_id" => $_file_category_id[$fileNumber],
            "object_id"        => CValue::post("object_id"),
            "object_class"     => CValue::post("object_class"),
            "file_rename"      => CValue::post("file_rename"),
          );
        }
      }
      else{
        // 1 seul fichier
        $aFiles[] = $upload;
      }
      
      foreach ($aFiles as $file) {
        if ($file["error"] == UPLOAD_ERR_NO_FILE) {
          continue;
        }
        
        if ($file["error"] != 0) {
          CAppUI::setMsg(CAppUI::tr("CFile-msg-upload-error-".$file["error"]), UI_MSG_ERROR);
          continue;
        }
        
        // Reinstanciate
        if ($multifiles){
          $this->_obj = new $this->_obj->_class_name;
        }
        $obj = $this->_obj;
        
        $obj->bind($file);
        $obj->file_name = empty($file["file_rename"]) ? $file["name"] : $file["file_rename"];
        $obj->file_type = $file["type"];
        $obj->file_size = $file["size"];
        $obj->fillFields();
        $obj->private   = CValue::post("private");
        if (false == $res = $obj->moveTemp($file)) {
          CAppUI::setMsg("Fichier non envoy�", UI_MSG_ERROR);
          continue;
        }

        // File owner on creation
        if (!$obj->file_id) {
          $obj->file_owner = $AppUI->user_id;
        }

        if ($msg = $obj->store()) {
          CAppUI::setMsg("Fichier non enregistr�: $msg", UI_MSG_ERROR);
          continue;
        }

        CAppUI::setMsg("Fichier enregistr�", UI_MSG_OK);
      }
      
      // Redaction du message et renvoi
      if (@count($AppUI->messages[UI_MSG_OK]) && $this->redirectStore) {
        $this->redirect =& $this->redirectStore;
      }
      
      if (!@count($AppUI->messages[UI_MSG_OK]) && $this->redirectError) {
        $this->redirect =& $this->redirectError;
      }
    }
    else {
      parent::doStore();
    }
  }  
  
  function doRedirect() {
    $cat_id = intval(CValue::post("file_category_id"));
    
    if ($this->ajax) {
      $this->doCallback();
    }
    
    if ($this->redirect !== null) {
      CAppUI::redirect($this->redirect);
    }
  }
}
?>