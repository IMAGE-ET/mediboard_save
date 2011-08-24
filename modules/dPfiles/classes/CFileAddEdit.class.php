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
    $upload     = null;
    $multifiles = false;

    if (CValue::POST("_from_yoplet") == 1) {
    	$obj = $this->_obj;
    	$array_file_name = array();
    	
    	// On retire les backslashes d'escape
    	$file_name = stripslashes($this->request['_file_path']);
    	
    	// R�cup�ration du nom de l'image en partant de la fin de la cha�ne
    	// et en rencontrant le premier \ ou /
    	preg_match('@[\\\/]([^\\\/]*)$@i', $file_name, $array_file_name);
    	$file_name = $array_file_name[1];
    	
    	$extension = strrchr($file_name, '.');
    	$_rename = $this->request['_rename'] ? $this->request['_rename'] : 'upload';
    	$file_path = "tmp/". $this->request['_checksum'];
      
    	$obj->file_name = $_rename == 'upload' ? $file_name : $_rename . $extension;
      $obj->_old_file_path = $this->request['_file_path'];
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
      $for_identite = CValue::post("for_identite", 0);
      $rename = CValue::post("_rename");
      
      CValue::setSession("_rename", $rename);
      
      foreach($upload["error"] as $fileNumber => $etatFile){
        if (!$for_identite) {
          $rename = $rename ? $rename . strrchr($upload["name"][$fileNumber], '.') : "";
        }

        if ($upload["name"][$fileNumber]) {
          $aFiles[] = array(
            "name"             => $upload["name"][$fileNumber],
            "type"             => $upload["type"][$fileNumber],
            "tmp_name"         => $upload["tmp_name"][$fileNumber],
            "error"            => $upload["error"][$fileNumber],
            "size"             => $upload["size"][$fileNumber],
            "file_category_id" => $_file_category_id,
            "object_id"        => CValue::post("object_id"),
            "object_class"     => CValue::post("object_class"),
            "_rename"          => $rename
          );
        }
      }
      
      $merge_files = CValue::post("_merge_files");
      
      if ($merge_files) {
        CAppUI::requireLibraryFile("PDFMerger/PDFMerger");
        $pdf = new PDFMerger;
        $this->_obj = new $this->_obj->_class;
        $obj = $this->_obj;
        $file_name = "";
        $nb_converted = 0;

        foreach($aFiles as $key=>$file) {
          $converted = 0;
          if ($file["error"] == UPLOAD_ERR_NO_FILE) {
              continue;
          }
          
          if ($file["error"] != 0) {
            CAppUI::setMsg(CAppUI::tr("CFile-msg-upload-error-".$file["error"]), UI_MSG_ERROR);
            continue;
          }
          
          // Si c'est un pdf, on le rajoute sans aucun traitement
          if (substr(strrchr($file["name"], '.'),1) == "pdf") {
            $file_name .= substr($file["name"], 0, strpos($file["name"], '.'));
            $pdf->addPDF($file["tmp_name"], 'all');
            $nb_converted ++;
            $converted = 1;
          }
          // Si le fichier est convertible en pdf
          else if ($obj->isPDFconvertible($file["name"]) && $obj->convertToPDF($file["tmp_name"], $file["tmp_name"]."_converted")) {
            $pdf->addPDF($file["tmp_name"]."_converted", 'all'); 
            $file_name .= substr($file["name"], 0, strpos($file["name"], '.'));
            $nb_converted ++;
            $converted = 1;
          }
          // Sinon cr�ation d'un cfile
          else {
            $other_file = new CFile;
            $other_file->bind($file);
            $other_file->file_name = $file["name"];
            $other_file->file_type = $file["type"];
            $other_file->file_size = $file["size"];
            $other_file->fillFields();
            $other_file->private = CValue::post("private");
            
            if (false == $res = $other_file->moveTemp($file)) {
              CAppUI::setMsg("Fichier non envoy�", UI_MSG_ERROR);
              continue;
            }
            $other_file->file_owner = CAppUI::$user->_id;
  
            if ($msg = $other_file->store()) {
              CAppUI::setMsg("Fichier non enregistr�: $msg", UI_MSG_ERROR);
              continue;
            }
  
            CAppUI::setMsg("Fichier enregistr�", UI_MSG_OK);
          }
          // Pour le nom du pdf de fusion, on concat�ne les noms des fichiers
          if ($key != count($aFiles)-1 && $converted) {
            $file_name .= "-";
          }
        }
        
        // Si des fichiers ont �t� convertis et ajout�s � PDFMerger,
        // cr�ation du cfile.
        if ($nb_converted) {
          $obj->file_name = $file_name.".pdf";
          $obj->file_type = "application/pdf";
          $obj->file_owner = CAppUI::$user->_id;
          $obj->private = CValue::post("private");
          $obj->object_id = CValue::post("object_id");
          $obj->object_class = CValue::post("object_class");
          $obj->updateFormFields();
          $obj->fillFields();
          $obj->forceDir();
          $tmpname = tempnam("/tmp", "pdf_");
          $pdf->merge('file', $tmpname);
          $obj->file_size = strlen(file_get_contents($tmpname));
          $obj->moveFile($tmpname);
          //rename($tmpname, $obj->_file_path . "/" .$obj->file_real_filename);
          
          if ($msg = $obj->store()) {
            CAppUI::setMsg("Fichier non enregistr�: $msg", UI_MSG_ERROR);
          }
          else {
            CAppUI::setMsg("Fichier enregistr�", UI_MSG_OK);
          }
        }
      }
      else {
        foreach ($aFiles as $file) {
          if ($file["error"] == UPLOAD_ERR_NO_FILE) {
            continue;
          }
          
          if ($file["error"] != 0) {
            CAppUI::setMsg(CAppUI::tr("CFile-msg-upload-error-".$file["error"]), UI_MSG_ERROR);
            continue;
          }
          
          // Reinstanciate
  
          $this->_obj = new $this->_obj->_class;
          $obj = $this->_obj;
          $obj->bind($file);
          $obj->file_name = empty($file["_rename"]) ? $file["name"] : $file["_rename"];
          $obj->file_type = $file["type"];
           
          if ($obj->file_type == "application/x-download") {
            $obj->file_type = CMbPath::guessMimeType($obj->file_name);
          }
          
          $obj->file_size = $file["size"];
          $obj->fillFields();
          $obj->private   = CValue::post("private");
          if (false == $res = $obj->moveTemp($file)) {
            CAppUI::setMsg("Fichier non envoy�", UI_MSG_ERROR);
            continue;
          }
  
          // File owner on creation
          if (!$obj->file_id) {
            $obj->file_owner = CAppUI::$user->_id;
          }
  
          if ($msg = $obj->store()) {
            CAppUI::setMsg("Fichier non enregistr�: $msg", UI_MSG_ERROR);
            continue;
          }
  
          CAppUI::setMsg("Fichier enregistr�", UI_MSG_OK);
        }
      }
      // Redaction du message et renvoi
      if (CAppUI::isMsgOK() && $this->redirectStore) {
        $this->redirect =& $this->redirectStore;
      }
      
      if (!CAppUI::isMsgOK() && $this->redirectError) {
        $this->redirect =& $this->redirectError;
      }
    }
    else {
      parent::doStore();
    }
  }  
}
?>