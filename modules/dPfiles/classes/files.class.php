<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

class CFile extends CDocumentItem {
	static $directory = null;
	
  // DB Table key
  var $file_id = null;
  
  // DB Fields
  var $file_real_filename = null;
  var $file_name          = null;
  var $file_type          = null;
  var $file_owner         = null;
  var $file_date          = null;
  var $file_size          = null;
  var $private            = null;
  var $rotation           = null;
  
  // Form fields
  var $_extensioned   = null;
  var $_file_size     = null;
  var $_sub_dir       = null;
  var $_absolute_dir  = null;
  var $_file_path     = null;
  var $_nb_pages      = null;
  var $_old_file_path = null;
  // Behavior fields
  var $_rotate      = null;
	var $_rename      = null; // @todo A utiliser dans le do_aed

  // References
  var $_ref_file_owner = null;

  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'files_mediboard';
    $spec->key   = 'file_id';
    $spec->measureable = true;
    return $spec;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["documents_ged_suivi"] = "CDocGedSuivi file_id";
    return $backProps;
  }
  
  function getProps() {
  	$specs = parent::getProps();
    $specs["file_date"]          = "dateTime notNull";
    $specs["file_size"]          = "num pos";
    $specs["file_real_filename"] = "str notNull show|0";
    $specs["file_owner"]         = "ref notNull class|CMediusers";
    $specs["file_type"]          = "str";
    $specs["file_name"]          = "str notNull show|0";
    $specs["private"]            = "bool notNull default|0";
    $specs["rotation"]           = "enum list|0|90|180|270";
    // Form Fields
    $specs["_sub_dir"]      = "str";
    $specs["_absolute_dir"] = "str";
    $specs["_file_path"]    = "str";
    $specs["_file_size"]    = "str show|1";
    $specs["_old_file_path"]= "str";
		// Behavior fields
    $specs["_rotate"]       = "enum list|left|right";
    $specs["_rename"]       = "str";
    return $specs;
  }
  
  function forceDir() {
    // Check global directory
    if (!CMbPath::forceDir(self::$directory)) {
      trigger_error("Files directory is not writable : " . self::$directory, E_USER_WARNING);
      return false;
    }
    
    // Checks complete file directory
    CMbPath::forceDir($this->_absolute_dir);
  }
  
  function getContent() {
//    mbExport($this->_file_path, "File Name");
//    mbExport(stat($this->_file_path), "File stat");
//    mbExport(strlen(file_get_contents($this->_file_path)), "File Data length");
//    mbTrace(filesize($this->_file_path), "File size");
    return file_get_contents($this->_file_path);
  }
  
  function putContent($filedata) {
    if (!$this->file_real_filename) {
      return false;
    }
    
    $this->updateFormFields();
    
    if ($this->forceDir() === false) {
      return false;
    }
    
    return file_put_contents($this->_file_path, $filedata);
  }
  
  function loadRefsFwd() {
  	parent::loadRefsFwd();
    $this->_ref_file_owner = $this->loadFwdRef("file_owner");
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    
    $this->_extensioned = $this->file_name;
    $this->_file_size = CMbString::toDecaBinary($this->file_size);
    
    $this->completeField("object_id");

    // Computes complete file path
    if ($this->object_id) {
      $this->completeField("file_real_filename");
      $this->_sub_dir      = "$this->object_class/" . intval($this->object_id / 1000);
      $this->_absolute_dir = self::$directory . "/$this->_sub_dir/$this->object_id";
      $this->_file_path    = "$this->_absolute_dir/$this->file_real_filename";
    }
    
    $this->_shortview = $this->_view = str_replace("_", " ", $this->file_name);
  }
  
  function getPerm($permType) {
    // Delegate on target object
		$this->loadTargetObject();
    return $this->_ref_object->_id ?
      $this->_ref_object->getPerm($permType) :
      false;
  }
  
  function fillFields(){
    if (!$this->_id) {
      if (!$this->file_date)          $this->file_date = mbDateTime();
      if (!$this->file_real_filename) $this->file_real_filename = uniqid(rand());
    }
  }
  
  function store() {
    if ($this->_id && ($this->fieldModified("object_id") || $this->fieldModified("object_class"))) {
      $this->_old->updateFormFields();
      $this->moveFile($this->_old->_file_path);
    }
    if (!$this->_id) {
    	
    	$file = new CFile;
    	$last_point = strrpos($this->file_name, '.');
    	
    	$where["file_name"] = " = '{$this->file_name}'";
    	$files = $file->loadlist($where);
    	
    	if (count($files)) {
	    	$files = array(0 => "0");
	    	$indice = '';

	    	while (count($files) != 0) {
	    		$where["file_name"] = " LIKE '" . substr($this->file_name, 0, $last_point) .$indice . substr($this->file_name, $last_point) ."'";
	    	  $files = $file->loadList($where);
	    	  if (count($files) == 0) break;
	    	  $indice == '' ? $indice = 1 :	$indice++;
	    	}
	    	$this->file_name = substr($this->file_name, 0, $last_point) . 
	    	                   $indice . substr($this->file_name, $last_point);
    	}
    }
    if (!$this->_id && $this->rotation === null) {
      $this->loadNbPages();
      $this->rotation = $this->rotation === null ? 0 : $this->rotation;
    }

    if ($this->_rotate !== null) {
        $this->completeField("rotation");
    }
    if ($this->_rotate == "left") {
      $this->rotation += 90;
    }
    if ($this->_rotate == "right") {
      $this->rotation -= 90;
    }
    $this->rotation %= 360;
    if ($this->rotation < 0) {
      $this->rotation += 360;
    }
    
    $this->rotation = $this->rotation % 360;
    if ($this->rotation < 0) { $this->rotation += 360; }
    return parent::store();
  }
  
  function delete() {
    $file = new CFile;
    $files = $file->loadFilesForObject($this);
    foreach($files as $_file) {
      $_file->delete();
    }
    if ($msg = parent::delete()) {
      return $msg;
    }

    // Actually remove the file
    @unlink($this->_file_path);

    // Delete any index entries
    $query = "DELETE FROM files_index_mediboard WHERE file_id = '$this->_id'";
    if (!$this->_spec->ds->exec($query)) {
      return $this->_spec->ds->error();
    }
  }

  /**
   * move a file from a temporary (uploaded) location to the file system
   * @return boolean job-done
   */ 
  function moveTemp($file) {
    return $this->moveFile($file, true);
  }
  
  /**
   * Move a file from a location to the file system or from the PHP temp directory
   * @return boolean job-done
   */
  function moveFile($file, $uploaded = false) {
    if (!$uploaded && !is_file($file))
      return false;
      
    $this->updateFormFields();
    
    if ($this->forceDir() === false) {
      return false;
    }
  
    // Actually move any file
    if ($uploaded)
      return move_uploaded_file($file["tmp_name"], $this->_file_path);
    else
			return rename($file, $this->_file_path);
  }
    
  static function loadFilesForObject(CMbObject $object){
    $file = new CFile();
    $file->setObject($object);
    $files = $file->loadMatchingList();
    
    foreach($files as $_file) {
      if (!$_file->canRead()){
        unset($files[$_file->_id]);
      }
    }
    return $files;
  }
  
  static function loadNbFilesByCategory(CMbObject $object){
    // Liste des Category pour les fichiers de cet objet
    $listCategory = CFilesCategory::listCatClass($object->_class_name);
    
    // Cr�ation du tableau de cat�gorie initialis� � 0
    $affichageNbFile = array(
      array(
        "name" => CAppUI::tr("CFilesCategory.none"),
        "nb"   => 0
      )
    );
    
    foreach($listCategory as $keyCat => $currCat){
      $affichageNbFile[$keyCat] = array(
        "name" => $currCat->nom,
        "nb"   => 0
      );
    }
   
    // S'il objet valide
    if($object->_id){
      foreach($object->_ref_files as $keyFile => $curr_file){
        if($curr_file->file_category_id)
          $affichageNbFile[$curr_file->file_category_id]["nb"]++;
        else
          $affichageNbFile[0]["nb"]++;
      }
    }
    return $affichageNbFile;
  }
  
  function oldImageMagick() {
    exec("convert --version", $ret);
    preg_match("/ImageMagick ([0-9\.-]+)/", $ret[0], $matches);
    return $matches[1] < "6.5.8";
  }
  
  function loadNbPages(){
    if(strpos($this->file_type, "pdf") !== false && file_exists($this->_file_path)){
      // Fichier PDF Tentative de r�cup�ration
      $string_recherche = "/Count";
      $dataFile = file_get_contents($this->_file_path);
      $nb_count = substr_count($dataFile, $string_recherche);

      if ($this->oldImageMagick() && preg_match("/\/Rotate ([0-9]+)/", $dataFile, $matches)){
        $this->rotation = 360 - $matches[1];
      }
      
//      return $this->_nb_pages = preg_match_all("/\/Page\W/", $dataFile, $matches);
      
      if(strpos($dataFile, "%PDF-1.4") !== false && $nb_count >= 2){
        // Fichier PDF 1.4 avec plusieurs occurence
        $splitFile = preg_split("/obj\r<</", $dataFile);
        
        foreach($splitFile as $splitval){
          if(!$this->_nb_pages){
            $splitval = str_replace(array("\r", "\n"), "", $splitval);
            $position_fin = stripos($splitval, ">>");
            if($position_fin !== false){
              $splitval = substr($splitval, 0, $position_fin);
              if(strpos($splitval, "/Title") === false
                 && strpos($splitval, "/Parent") === false
                 && strpos($splitval, "/Pages") !== false
                 && strpos($splitval, $string_recherche) !== false){
                // Nombre de page ici
                $position_count = strripos($splitval, $string_recherche) + strlen($string_recherche);
                $nombre_temp = explode (" ", trim(substr($splitval,$position_count,strlen($splitval)-$position_count)) , 2 );
                $this->_nb_pages = intval(trim($nombre_temp[0]));
              }
            }
          }
        }
      }
      elseif(strpos($dataFile, "%PDF-1.3") !== false || strpos($dataFile, "%PDF-1.6") !== false || $nb_count == 1){
        // Fichier PDF 1.3 ou 1 seule occurence
        $position_count = strripos($dataFile, $string_recherche) + strlen($string_recherche);
        $nombre_temp = explode (" ", trim(substr($dataFile,$position_count, strlen($dataFile)-$position_count)), 2);
        $this->_nb_pages = intval(trim($nombre_temp[0]));
      }
    }
  }
  
  static function loadDocItemsByObject(CMbObject $object) {
    global $can;
    if (!$object->_ref_files){
      $object->loadRefsFiles();
    }
    if (!$object->_ref_documents){
      $object->loadRefsDocs();
    }
    
    //Cr�ation du tableau des cat�gorie pour l'affichage
    $affichageFile = array(
      array(
        "name" => CAppUI::tr("CFilesCategory.none"),
        "items" => array(),
      )
    );
    
    foreach (CFilesCategory::listCatClass($object->_class_name) as $_cat) {
      $affichageFile[$_cat->_id] = array(
        "name" => $_cat->nom,
        "items" => array(),
      );
    }

    //Ajout des fichiers dans le tableau
    foreach ($object->_ref_files as $keyFile=>&$_file) {
    	$cat_id = $_file->file_category_id ? $_file->file_category_id : 0;
      $affichageFile[$cat_id]["items"]["$_file->file_name-$_file->_guid"] =& $_file;
      if (!isset($affichageFile[$cat_id]["name"]))
        $affichageFile[$cat_id]["name"] = '';
    }
    
    //Ajout des document dans le tableau
    foreach ($object->_ref_documents as $keyDoc=>&$_doc) {
    	$cat_id = $_doc->file_category_id ? $_doc->file_category_id : 0;
      $affichageFile[$cat_id]["items"]["$_doc->nom-$_doc->_guid"] =& $_doc;
      if (!isset($affichageFile[$cat_id]["name"]))
        $affichageFile[$cat_id]["name"] = '';
    }
    
    // Classement des Fichiers et des document par Ordre alphab�tique
    foreach($affichageFile as $keyFile => $currFile){
      ksort($affichageFile[$keyFile]["items"]);
    }

    return $affichageFile;
  }
  
  function handleSend() {
  	$this->completeField("file_name");
    $this->completeField("file_real_filename");
    $this->completeField("file_type");
    $this->completeField("file_date");
    $this->updateFormFields();
    
    return parent::handleSend();
  }
	
  function file_empty() {
    file_put_contents($this->_file_path, '');
  }
  
  function convertToPDF($ext) {
    $file = new CFile();
    $file->setObject($this);
    $file->private = $this->private;
    $file->file_name  = $this->file_name . ".pdf";
    $file->file_type  = "application/pdf";
    $file->file_owner = CAppUI::$user->_id;
    $file->fillFields();
    $file->updateFormFields();
    $file->forceDir();
    $save_name = $this->_file_path;
    rename($save_name, $save_name . $ext);
    $file->store();

    // V�rifier si openoffice est lanc�
    exec("sh shell/ooo_state.sh",$res);
    
    if ($res[0] == 0 ){
//      exec(CAppUI::conf("dPfiles CFile openoffice_path") . "/soffice -accept=\"socket,host=localhost,port=8100;urp;StarOffice.ServiceManager\" -no-logo -headless -nofirststartwizard -no-restore >> /dev/null", $ret);
      usleep(600000);
    }

    $res = exec("python ./modules/dPfiles/script/doctopdf.py {$this->_file_path}" . $ext ." {$file->_file_path}", $ret);
//    mbTrace($this->_file_path,'',1);
    rename($save_name . $ext, $save_name);
    return $res;
  }
  
}

// We have to replace the backslashes with slashes because of PHPthumb on Windows
CFile::$directory = str_replace('\\', '/', realpath(CAppUI::conf("dPfiles CFile upload_directory")));

?>