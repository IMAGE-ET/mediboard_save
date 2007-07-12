<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @version $Revision: 1793 $
 *  @author Thomas Despoix
*/

/**
 * Class CMbObject 
 * @abstract Adds Mediboard abstraction layer functionality
 */

global $mbObjectCount, $mbCacheObjectCount;
$mbObjectCount = 0;
$mbCacheObjectCount = 0;

require_once("./classes/request.class.php");
require_once("./classes/mbFieldSpecFact.class.php");
 
class CMbObject {
  
  /**
   * Global properties
   */
  
  var $_objectsTable = null; // list of loaded objects
  
  var $_class_name    = null; // class name of the object
  var $_tbl           = null; // table name
  var $_tbl_key       = null; // primary key name
  var $_error         = null; // error message
  var $_id            = null; // universal shortcut for the object id
  var $_view          = null; // universal view of the object
  var $_shortview     = null; // universal shortview for the object
  var $_canRead       = null; // read permission for the object
  var $_canEdit       = null; // write permission for the object
  var $_external      = null; // true if object is has remote ids
  var $_locked        = null; // true if object is locked

  var $_view_template          = null; // view template path
  var $_complete_view_template = null; // complete view template path

  /**
   * Properties  specification
   */

  var $_helped_fields = array(); // Champs concern� par les aides a la saisie
  var $_aides         = array(); // aides � la saisie
  var $_props         = array(); // properties specifications as string
  var $_specs         = array(); // properties specifications as objects
  var $_backRefs      = array(); // Back reference specification as string
  var $_backSpecs     = array(); // Back reference specification as objects
  var $_enums         = array(); // enums fields elements
  var $_enumsTrans    = array(); // enums fields translated elements
  var $_seek          = array(); // seekable fields
  var $_nb_files_docs = null;
  
  
  /**
   * References
   */
  
  var $_back          = null; // All back references
  var $_ref_module    = null; // Parent module
  var $_ref_logs      = null; // history of the object
  var $_ref_first_log = null;
  var $_ref_last_log  = null;
  var $_ref_notes     = array(); // Notes
  var $_ref_documents = array(); // Documents
  var $_ref_files     = array(); // Fichiers
  var $_objBefore     = null;
  var $_obj           = null;
  var $_logable       = null;
  /**
   * Constructor
   */
 
  function CMbObject($table, $key) {
    global $mbObjectCount;
    $mbObjectCount++;
    
    $this->_tbl     = $table;
    $this->_tbl_key = $key;
    $this->_id      =& $this->$key;
    
    static $class         = null;
    static $objectsTable  = array();
    static $props         = null;
    static $backRefs      = null;
    static $backSpecs     = array();
    static $specsObj      = null;
    static $seeks         = null;
    static $enums         = null;
    static $enumsTrans    = null;
    static $helped_fields = null;

    static $static = false;
    if (!$static) {
      $class = get_class($this);
      $this->_class_name =& $class;
      $this->_objectsTable =& $objectsTable;
      $props = $this->getSpecs();
      $this->_props =& $props;
      $backRefs = $this->getBackRefs();
      $this->_backRefs =& $backRefs;
      $specsObj = $this->getSpecsObj();
      $this->_specs =& $specsObj;
      $seeks = $this->getSeeks();
      $this->_seek =& $seeks;
      $enums = $this->getEnums();
      $this->_enums =& $enums;
      $enumsTrans = $this->getEnumsTrans();
      $this->_enumsTrans =& $enumsTrans;
      $helped_fields = $this->getHelpedFields();
      $this->_helped_fields =& $helped_fields;
      
      $static = true;
    }
    
    $this->_class_name    =& $class;
    $this->_objectsTable  =& $objectsTable;
    $this->_props         =& $props;
    $this->_backRefs      =& $backRefs;
    $this->_backSpecs     =& $backSpecs;
    $this->_specs         =& $specsObj;
    $this->_seek          =& $seeks;
    $this->_enums         =& $enums;
    $this->_enumsTrans    =& $enumsTrans;
    $this->_helped_fields =& $helped_fields;
    
    $this->_logable = true;
  }
  
  /**
   * Set/get functions
   */
  function getError() {
    return $this->_error;
  }
  
  /**
   * Chargement des notes sur l'objet
   */
  function loadRefsNotes($perm = PERM_READ) {
    $this->_ref_notes = new CNote();
    if($this->_id){
      $this->_ref_notes = $this->_ref_notes->loadNotesForObject($this, $perm);
      return count($this->_ref_notes);
    }else {
      return 0;
    }
  }

  /**
   * Chargement des Fichiers et Documents
   */
  function loadRefsFiles() {
    $file = new CFile();
    if ($file->_ref_module && $this->_id) {
      $this->_ref_files = $file->loadFilesForObject($this);
      return count($this->_ref_files);
    }

    return;
  }

  function loadRefsDocs() {
    $document = new CCompteRendu();
    if ($document->_ref_module && $this->_id) {
      $document->object_class = $this->_class_name;
      $document->object_id    = $this->_id;
      $order = "nom";
      $this->_ref_documents = $document->loadMatchingList($order);
      return count($this->_ref_documents);
    }
    
    return;
  }
  
  function loadRefsFilesAndDocs() {
  	$nb_files = $this->loadRefsFiles();
    $nb_docs  = $this->loadRefsDocs();
    $this->_nb_files_docs = $nb_docs + $nb_files;
  }
  
  function getNumDocs() {
    return $this->countBackRefs("documents");
  }
  
  function getNumFiles(){
    return $this->countBackRefs("files");
  }
  
  function getNumDocsAndFiles($permType = null){
    if(!$permType){
      $this->_nb_files_docs = $this->getNumFiles() + $this->getNumDocs();
    }else{
      $this->_nb_files_docs = $this->getNumDocsAndFilesWithPerm($permType);
    }
    return $this->_nb_files_docs;
  }
  
  function getNumDocsAndFilesWithPerm($permType = PERM_READ){
    $this->loadRefsFiles();
    if ($this->_ref_files) {
      foreach ($this->_ref_files as $file_id => &$file){
        if(!$file->getPerm($permType)){
          unset($this->_ref_files[$file_id]);
        }
      }
    }
    
    $this->loadRefsDocs();
    if ($this->_ref_documents) {
      foreach ($this->_ref_documents as $doc_id=>&$doc){
        if(!$doc->getPerm($permType)){
          unset($this->_ref_documents[$doc_id]);
        }
      }
    }
    
    return count($this->_ref_files) + count($this->_ref_documents);
  }
  
  /**
   * Permission generic check
   * return true or false
   */

  function getPerm($permType) {
    return(CPermObject::getPermObject($this, $permType));
  }
  
  function canRead() {
    $this->_canRead = $this->getPerm(PERM_READ);
    return $this->_canRead;
  }
  
  function canEdit() {
    $this->_canEdit = $this->getPerm(PERM_EDIT);
    return $this->_canEdit;
  }

  function canDo(){
    $canDo = new CCanDo;
    $canDo->read  = $this->canRead();
    $canDo->edit  = $this->canEdit();
    
    return $canDo;
  }
  
  function loadListWithPerms($permType = PERM_READ, $where = null, $order = null, $limit = null, $group = null, $leftjoin = null) {
    $list = $this->loadList($where, $order, $limit, $group, $leftjoin);
    
    // Filter with permission
    if ($permType) {
      foreach ($list as $key=>$element){
        if(!$element->getPerm($permType)){
          unset($list[$key]);
        }
      }
    }

    return $list;
  }
  
  /**
   * Bind an object with an array
   */

  function bind($hash, $doStripSlashes = true) {
    if (!is_array($hash)) {
      $this->_error = get_class($this)."::bind failed.";
      return false;
    } else {
      bindHashToObject($hash, $this, $doStripSlashes);
      return true;
    }
  }
  
  /**
   * Object(s) Loaders
   */

  // One object by ID
  function load($oid = null, $strip = true) {
    if ($oid) {
      $this->_id = intval($oid);
    }

    if (!$this->_id) {
      return false;
    }
    
    $sql = "SELECT * FROM `$this->_tbl` WHERE `$this->_tbl_key` = '$this->_id'";
    $object = db_loadObject($sql, $this);

    if (!$object) {
      $this->_id = null;
      return false;
    }

    //$this->_objectsTable[$oid] = $this;
    $this->checkConfidential();
    $this->updateFormFields();
    return $this;
  }
  
  /**
   * Loads the first object matching defined properties
   */
  function loadMatchingObject($order = null, $group = null, $leftjoin = null) {
    $request = new CRequest;
    $request->addLJoin($leftjoin);
    $request->addGroup($group);
    $request->addOrder($order);

    $this->updateDBFields();
    foreach($this->getProps() as $key => $value) {
      if ($value !== null) {
        $request->addWhereClause($key, "= '$value'");
      }
    }
    
    $this->loadObject($request->where, $request->order, $request->group, $request->ljoin);
  }
  
  /**
   * Loads the list which objects match defined properties
   */
  function loadMatchingList($order = null, $limit = null, $group = null, $leftjoin = null) {
    $request = new CRequest;
    $request->addLJoin($leftjoin);
    $request->addGroup($group);
    $request->addOrder($order);
    $request->setLimit($limit);

    $this->updateDBFields();
    foreach($this->getProps() as $key => $value) {
      if ($value !== null) {
        $request->addWhereClause($key, "= '$value'");
      }
    }
    return $this->loadList($request->where, $request->order, $request->limit, $request->group, $request->ljoin);
  }
  
  /**
   * Loads the first object matching the query
   */
  function loadObject($where = null, $order = null, $group = null, $leftjoin = null) {
    $request = new CRequest;
    $request->addLJoin($leftjoin);
    $request->addWhere($where);
    $request->addGroup($group);
    $request->addOrder($order);
    $request->setLimit("0,1");
    $list =& $this->loadList($request->where, $request->order, $request->limit, $request->group, $request->ljoin);

    foreach ($list as $object) {
      foreach($object->getProps() as $key => $value) {
        $this->$key = $value;
      }
      $this->updateFormFields();
      return true;
    }
    return false;
  }
  
  /**
   * Object list by a request constructor
   */
  function loadList($where = null, $order = null, $limit = null, $group = null, $leftjoin = null) {
    $request = new CRequest();
    $request->addLJoin($leftjoin);
    $request->addWhere($where);
    $request->addGroup($group);
    $request->addOrder($order);
    $request->setLimit($limit);
    $result = db_loadObjectList($request->getRequest($this), $this);
    return $result;
  }
  
  function loadListByReq($request) {
    $result = db_loadObjectList($request->getRequest($this), $this);
    return $result;
  }

  /**
   *  Clone the current record
   *  @return object  The new record object or null if error
   */

  function cloneObject() {
    $_key = $this->_tbl_key;
    
    $newObj = $this;
    // blanking the primary key to ensure that's a new record
    $newObj->$_key = "";
    
    return $newObj;
  }
  
  /**
   * Update the form fields from the DB fields
   */

  function updateFormFields() {
    $k                    = $this->_tbl_key;
    $this->_view          = $this->_tbl . " #" . $this->$k;
    $this->_shortview     = "#" . $this->$k;
    $this->_view_template     = "{$this->_ref_module->mod_name}/templates/{$this->_class_name}_view.tpl";
    $this->_complete_view_template = "{$this->_ref_module->mod_name}/templates/{$this->_class_name}_complete.tpl";
  }
  
  /**
   * Load object view information 
   */
  
  function loadView() {
    $this->loadRefsFwd();
  }
  
  /**
   * Load complete object view information 
   */
  
  function loadComplete() {
    $this->loadRefs();
  }
  
  
  /**
   * References loaders
   */

  function loadRefs() {
    if ($this->_id){  
      $this->loadRefsBack();
      $this->loadRefsFwd();
    }
    
    return $this->_id;
  }

  function loadRefsBack() {
    $this->loadExternal();
  }

  function loadRefsFwd() {
  }
  
  function loadExternal() {
    $this->_external = $this->countBackRefs("identifiants");
  }

  /**
   * Nullify object properties that evaluate to false
   */
  function nullifyEmptyFields() {
    foreach ($this->getProps() as $propName => $propValue) {
      if (!$propValue) {
        $this->$propName = null;
      }
    }
  }
  
  /**
   * Extends object properties with target object (of the same class) properties
   */
  function extendsWith($mbObject) {
    $targetClass = get_class($mbObject);
    $thisClass = get_class($this);
    if ($targetClass != $thisClass) {
      trigger_error(printf("Target object has not the same class (%s) as this (%s)", $targetClass, $thisClass), E_USER_WARNING);
      return;
    }
    
    foreach ($mbObject->getProps() as $propName => $propValue) {
      if ($propValue !== null) {
        $this->$propName = $propValue;
      }
    }
  }
  
  function loadRefModule($name) {
    $this->_ref_module = CModule::getInstalled($name);
  }

  /**
   *  Generic check method
   *  @return null if the object is ok a message if not
   */

  function repair() {
    global $dPconfig;
    $properties = get_object_vars($this);
    foreach($this->_props as $propName => $propSpec) {
      if (!array_key_exists($propName, $properties)) {
        trigger_error("La sp�cification cible la propri�t� '$propName' inexistante dans la classe '$this->_class_name'", E_USER_WARNING);
        continue;
      } 

      $propValue =& $this->$propName;
      if ($propValue !== null) {
        if ($msg = $this->checkProperty($propName)) {
          if (!$this->lookupSpec("notNull", $propSpec)) {
            $propValue = "";
          }
        }
      }
    }
  }

  /**
   *  Generic check method
   *  @return null if the object is ok a message if not
   */

  function check() {
    global $dPconfig;
    $msg = null;
    $properties = get_object_vars($this);
    
    foreach($this->_props as $propName => $propSpec) {
      if(!array_key_exists($propName, $properties)) {
        trigger_error("La sp�cification cible la propri�t� '$propName' inexistante dans la classe '$this->_class_name'", E_USER_WARNING);
      } else {
        $propValue =& $this->$propName;
        if(($propValue !== null) || (!$this->_id)) {
          $msgProp = $this->checkProperty($propName);
          
          $debugInfo = $dPconfig["debug"] ? "(val:'$propValue', spec:'$propSpec')" : "";
          $msg .= $msgProp ? "<br/> => $propName : $msgProp $debugInfo" : null;
        }
      }
    }
    
    return $msg;
  }
  
  /**
   * Update the form fields from the DB fields
   */

  function updateDBFields() {
  }
  
  
  function log($type,$_objBefore) {
  	global $AppUI;
    $fields = array();
    
    
    foreach($this->getProps() as $propName => $propValue) {
    	if ($propValue !== null) {
        $propValueBefore = $this->_objBefore->$propName;
        if ($propValueBefore != $propValue) {
          $fields[] = $propName;
        }
      }
    }
    
    $object_id = $this->_id;
    
    $type = "store";
    if ($this->_objBefore->_id == null) {
      $type = "create";
      $fields = array();
    }
    
    if ($this->_id == null) {
      $type = "delete";
      $object_id = $this->_objBefore->_id;
      $fields = array();
    }

    if (!count($fields) && $type == "store") {
      return;
    }
    
    // Si object non logable
    if(!$this->_logable){
    	return;
    }

    $log = new CUserLog;
    $log->user_id = $AppUI->user_id;
    $log->object_id = $object_id;
    $log->object_class = $this->_class_name;
    $log->type = $type;
    $log->_fields = $fields;
    $log->date = mbDateTime();
    $log->store();  
  }
  
  
  
  /**
   *  Inserts a new row if id is zero or updates an existing row in the database table
   *  @return null|string null if successful otherwise returns and error message
   */
  function store($checkobject = true) {
    global $AppUI;
    
    // Properties checking
    $this->updateDBFields();

    // Si l'object existe alors, on affecte � objBefore sa valeur
    if($this->_id){ 
      $_obj_class = $this->_class_name;
      $this->_objBefore = new $_obj_class();
      $this->_objBefore->load($this->_id);
    } else {
    // Sinon, on cree un nouveau objBefore du type de l'objet 
      $_obj_class = $this->_class_name;
      $this->_objBefore = new $_obj_class();
    } 
     
    if ($checkobject) {
      if ($msg = $this->check()) {
        return $AppUI->_(get_class($this)) . 
          $AppUI->_("::store-check failed:") .
          $AppUI->_($msg);
      }
    }
   
    // The object may not exist in database anymore : re-insert it
    $k = $this->_tbl_key;
    $query = db_prepare("SELECT * FROM `$this->_tbl` WHERE `$k` = %", $this->_id);
    if ($this->_id) {
        if (!db_loadResult($query)) {
        $this->_id = null;
        
        // Recheck for missing values when object was deleted
        if ($msg = $this->check()) {
          return $AppUI->_(get_class($this)) . " " .
            $AppUI->_("::deleted not recreated");
        }
      }
    }
  
    // DB query
    if ($this->_id) {
      $ret = db_updateObject($this->_tbl, $this, $k);
      
    } else {
      $ret = db_insertObject($this->_tbl, $this, $k);
    }
    

    if (!$ret) {
      return get_class($this)."::store failed <br />" . db_error();
    } 

    // Load the object to get all properties
    unset($this->_objectsTable[$this->_id]);
    $this->load();
    
    //Creation du log une fois le store termin�
    $this->log("store",$this->_objBefore);
    
    return null;
  }

  
  /**
   * Count number back refreferecing object
   * @param string $backName name the of the back references to count
   * @return int the count null if back references module is not installed
   */
  function countBackRefs($backName) {
    $backRef = $this->_backRefs[$backName];
    $backRefParts = split(" ", $backRef);
    $backClass = $backRefParts[0];
    $backField = $backRefParts[1];
    $backObject = new $backClass;

    // Cas du module non install�
    if (!$backObject->_ref_module) {
      return;
    }
    
    $query = "SELECT COUNT($backObject->_tbl_key) " .
      "\nFROM `$backObject->_tbl` " .
      "\nWHERE `$backField` = '$this->_id'";

    // Cas des meta objects
    $backSpec =& $backObject->_specs[$backField];
    $backMeta = $backSpec->meta;      
    if ($backMeta) {
      $query .= "\nAND `$backMeta` = '$this->_class_name'";
    }
    
    // Comptage des backrefs
    return db_loadResult($query);
  }

  /**
   * Load named back references
   * @return null
   */
  function loadBackRefs($backName = null, $order = null, $limit = null) {
    // Empty object
    if (!$this->_id) {
      return;
    }
    
    $this->makeBackSpec($backName);
    
    // Sp�cifications
    $backSpec = $this->_backSpecs[$backName];
    $backObject = new $backSpec->class;
    $backField = $backSpec->field;
    $fwdSpec =& $backObject->_specs[$backField];
    $backMeta = $fwdSpec->meta;      

    // Cas du module non install�
    if (!$backObject->_ref_module) {
      continue;
    }
    
    // V�rification de la possibilit� de supprimer chaque backref
    $backObject->$backField = $this->_id;

    // Cas des meta objects
    if ($backMeta) {
      $backObject->$backMeta = $this->_class_name;
    }
    
    return $this->_back[$backSpec->name] = $backObject->loadMatchingList($order, $limit);
  }
  
  /**
   * Load all back references
   * @return null
   */
  function loadAllBackRefs() {
    foreach ($this->_backRefs as $backName => $backRef) {
      $this->loadBackRefs($backName);
    }
  }

  /**
   * Check whether the object can be deleted.
   * Default behaviour counts for back reference without cascade 
   * @return null if ok error message otherwise
   */
  function canDeleteEx() {
    global $AppUI;

    // Empty object
    if (!$this->_id) {
      return $AppUI->_("noObjectToDelete") . " " . $AppUI->_($this->_class_name);
    }
    
    // Counting backrefs
    $issues = array();
    foreach ($this->_backRefs as $backName => $backRef) {
      $backRefParts = split(" ", $backRef);
      $backClass = $backRefParts[0];
      $backField = $backRefParts[1];
      $backObject = new $backClass;
      $backSpec =& $backObject->_specs[$backField];
      $backMeta = $backSpec->meta;      

      // Cas du module non install�
      if (!$backObject->_ref_module) {
        continue;
      }
      
      // Cas de la suppression en cascade
      if ($backSpec->cascade) {
        
        // V�rification de la possibilit� de supprimer chaque backref
        $backObject->$backField = $this->_id;

        // Cas des meta objects
        if ($backMeta) {
          $backObject->$backMeta = $this->_class_name;
        }
        
        $cascadeIssuesCount = 0;
        $cascadeObjects = $backObject->loadMatchingList();
        foreach ($cascadeObjects as $cascadeObject) {
          if ($msg = $cascadeObject->canDeleteEx()) {
            $cascadeIssuesCount++;
          }
        }
        
        if ($cascadeIssuesCount) {
          $issues[] = $AppUI->_("cascadeIssues")
            . " " . $cascadeIssuesCount 
            . "/" . count($cascadeObjects) 
            . " " . $AppUI->_("$backSpec->class-back-$backName");
        }
        
        continue;
      }
      
      // V�rification du nombre de backRefs
      if (!$backSpec->unlink) {
        if ($backCount = $this->countBackRefs($backName)) {
          $issues[] = $backCount 
            . " " . $AppUI->_("$backSpec->class-back-$backName");
        }
      }
    };
    $msg = count($issues) ?
      $AppUI->_("noDeleteRecord") . ": " . implode(", ", $issues) :
      null;
    
    return $msg;
  }

  /**
   * Default delete method
   * @return null|string null if successful otherwise returns and error message
   */
  function delete($oid = null) {

    // Chargement de _objBefore 
    $_obj_class = $this->_class_name;
    $this->_objBefore = new $_obj_class();
    $this->_objBefore->load($this->_id);
    
    
    if ($oid) {
      $this->_id = intval($oid);
    }
    
    if ($msg = $this->canDeleteEx()) {
      return $msg;
    }

    // Deleting backRefs
    foreach ($this->_backRefs as $backName => $backRef) {
      $backRefParts = split(" ", $backRef);
      $backClass = $backRefParts[0];
      $backField = $backRefParts[1];
      $backObject = new $backClass;
      $backSpec =& $backObject->_specs[$backField];
      $backMeta = $backSpec->meta;      
      
      // Cas du module non install�
      if (!$backObject->_ref_module) {
        continue; 
      }
      
      // Cas de l'interdiction de suppression
      if (!$backSpec->cascade) {
        continue;
      }
      
      // Cas de l'interdiction de la non liaison des backRefs
      if ($backSpec->unlink) {
        continue; 
      }
      
      $backObject->$backField = $this->_id;
      
      // Cas des meta objects
      if ($backMeta) {
        $backObject->$backMeta = $this->_class_name;
      }

      foreach ($backObject->loadMatchingList() as $object) {
        $object->delete();
      }
    }
    
    
    // Actually delete record
    $sql = "DELETE FROM $this->_tbl WHERE $this->_tbl_key = '$this->_id'";
    if (!db_exec($sql)) {
      return db_error();
    }
         
    // Deletion successful
    $this->_id = null;
   
    //Creation du log une fois le delete termin�
    $this->log("delete",$this->_objBefore);
    
    return null;
  }
  

  /**
   *  Generic seek method
   *  @return the first 100 records which fits the keywords
   */
  function seek($keywords) {
    $sql = "SELECT * FROM `$this->_tbl` WHERE 1";
    if(count($keywords) and count($this->_seek)) {
      foreach($keywords as $key) {
        $sql .= "\nAND (0";
        foreach($this->_seek as $keySeek => $spec) {
          $listSpec = array();
          $listSpec = explode("|", $spec);
          switch($listSpec[0]) {
            case "equal":
              $sql .= "\nOR `$keySeek` = '$key'";
              break;
            case "like":
              $sql .= "\nOR `$keySeek` LIKE '%$key%'";
              break;
            case "likeBegin":
              $sql .= "\nOR `$keySeek` LIKE '$key%'";
              break;
            case "likeEnd":
              $sql .= "\nOR `$keySeek` LIKE '%$key'";
              break;
            case "ref":
              $refObj = new $listSpec[1];
              $refObj = $refObj->seek($keywords);
              if(count($refObj)) {
                $listIds = implode(",", array_keys($refObj));
                $sql .= "\nOR `$keySeek` IN ($listIds)";
              }
              break;
          }
        }
        $sql .= "\n)";
      }
    } else {
      $sql .= "\nAND 0";
    }
    $sql .= "\nORDER BY";
    foreach($this->_seek as $keySeek => $spec) {
      $sql .= "\n$keySeek,";
    }
    $sql .= "\n $this->_tbl_key";
    $sql .=" LIMIT 0,100";
    return db_loadObjectList($sql, $this);
  }
  
  /**
   * Return the object properties in an array
   */
  function getProps() {
    $result = array();
    foreach(get_object_vars($this) as $key => $value) {
      if ($key[0] != "_") {
        $result[$key] = $value;
      }
    }
    return $result;
  }
  
  /**
   * Get properties specifications
   */
  function getSeeks() {
    return array();
  }

  /**
   * Get seek specifications
   */
  function getSpecs() {
    return array();
  }
  
  /**
   * Get backward reference specifications
   * "collection-name" => "class join-field"
   */
  function getBackRefs() {
    return array (
      "identifiants" => "CIdSante400 object_id",
      "notes"        => "CNote object_id",
      "files"        => "CFile object_id",
      "documents"    => "CCompteRendu object_id",
      "permissions"  => "CPermObject object_id",
      "logs"         => "CUserLog object_id",
    );
  }
  
  /**
   * Liste des champs d'aides � la saisie
   */
  function getHelpedFields() {
    return array();
  }
  
  
  /**
   * Converts string back specifications to objet specifications
   */
  function makeBackSpec($backName) {
    if (array_key_exists($backName, $this->_backSpecs)) {
      return;
    }
    
    $this->_backSpecs[$backName] = new CMbBackSpec($backName, $this->_backRefs[$backName]);
  }

  /**
   * Converts string specifications to objet specifications
   */
  function getSpecsObj($props = null){
    if($props == null){
      $specs =& $this->_props;
      $props = get_object_vars($this);
    }else{
      $specs = $props;
    }
    
    $spec = array();
    foreach($props as $k => $v){
      $spec[$k] = CMbFieldSpecFact::getSpec($this, $k, @$specs[$k]);
    }
    return $spec;
  }

  /**
   * Build Enums variant returning values
   */
  function getEnums() {
    global $AppUI;
    $enums = array();
    foreach ($this->_props as $propName => $propSpec) {
      $specEnum = $this->lookupSpec("enum", $propSpec);
      $specFragments = $this->lookupSpec("list", $propSpec);
      if($specEnum && $specFragments){
        $enums[$propName] = $specFragments;
      }elseif($this->lookupSpec("bool", $propSpec)){
        $enums[$propName][] = 0;
        $enums[$propName][] = 1;
      }
    }
    return $enums;
  }
  
  /**
   * Build Enums variant returning values
   */
  function getEnumsTrans() {
    global $AppUI;
    $enumsTrans = array();
    foreach ($this->_enums as $propName => $enumValues) {
      $enumsTrans[$propName] = array_flip($enumValues);
      foreach($enumsTrans[$propName] as $key => $item) {
        $enumsTrans[$propName][$key] = $AppUI->_(get_class($this).".$propName.$key");
      }
      asort($enumsTrans[$propName]);
    }
    
    return $enumsTrans;
  }
  
  function buildEnums() {
    global $AppUI;
    foreach ($this->_props as $propName => $propSpec) {
      $specEnum = $this->lookupSpec("enum", $propSpec);
      $specFragments = $this->lookupSpec("list", $propSpec);
      if($specEnum && $specFragments){
        $this->_enums[$propName] = $specFragments;
        $this->_enumsTrans[$propName] = array_flip($specFragments);
        foreach($this->_enumsTrans[$propName] as $key => $item) {
          $this->_enumsTrans[$propName][$key] = $AppUI->_($key);
        }
        asort($this->_enumsTrans[$propName]);
      }
    }
  }
  
  /**
   * Functions to check the object's properties
   */
  function lookupSpec($specFragment, $propSpec){
    $aSpecFragments = explode(" ", $propSpec);
    foreach($aSpecFragments as $spec){
      $aFrag = explode("|", $spec);
      $fragmentPosition = array_search($specFragment,$aFrag);
      if($fragmentPosition !== false){
        array_splice($aFrag, $fragmentPosition, 1);
        if(count($aFrag)){
          return $aFrag;
        }else{
          return true;
        }
      }
    }
    return false;
  }
  
  function checkProperty($propName) {
    $specObj = $this->_specs[$propName];
    $msg = null;
    if($msgError = $specObj->checkPropertyValue($this)){
      $msg .= $msgError;
    }
    return $msg;
  }
  
  function checkConfidential($specs = null) {
    global $dPconfig;
    if($dPconfig["hide_confidential"]) {
      if($specs == null){
        $specs = $this->_specs;
      }
      foreach ($specs as $propName => $propSpec) {
        $propValue =& $this->$propName;
        if ($propValue !== null && $this->_specs[$propName]) {
          $this->_specs[$propName]->checkConfidential($this);
        }
      }
    }
  }

  function loadAides($user_id) {
    foreach ($this->_helped_fields as $field => $prop) {
      $this->_aides[$field] = array();
      $this->_aides[$field]["no_enum"] = null;
      
      if($prop){
        $entryEnums = $this->_enumsTrans[$prop];
        // Cr�ation des entr�es pour les enums
        $this->_aides[$field]["no_enum"] = null;
        foreach($entryEnums as $valueEnum){
          $this->_aides[$field][$valueEnum] = null;
         }
      }
    }
    
    // Chargement de l'utilisateur courant
    $currUser = new CMediusers();
    $currUser->load($user_id);
    
    // Pr�paration du chargement des aides
    $where = array();
    $where["user_id"] = db_prepare("= %", $user_id);
    $where["class"]   = db_prepare("= %", $this->_class_name);
    $order = "name";
    
    // Chargement des Aides de l'utilisateur
    $aides = new CAideSaisie();
    $aides = $aides->loadList($where,$order); 
    $this->orderAides($aides, "Aides du praticien");
    unset($where["user_id"]);
    
    // Chargement des Aides de la fonction de l'utilisateur
    $where["function_id"] = db_prepare("= %", $currUser->function_id);
    $aides = new CAideSaisie();
    $aides = $aides->loadList($where,$order);  
    $this->orderAides($aides, "Aides du cabinet");
  }
  
  function orderAides($aides, $title){
    global $AppUI;
    
    foreach ($aides as $aide) {
      $curr_aide =& $this->_aides[$aide->field];
      
      // Verification de l'existance des cl� dans le tableaux
      $linkField = @$this->_helped_fields[$aide->field];
      if($linkField){
        $entryEnums = $this->_enumsTrans[$linkField];
      }
    
      // Ajout de l'aide � la liste g�n�rale
      $curr_aide["no_enum"][$title][$aide->text] = $aide->name; 
      
      if(!$aide->depend_value && $linkField){
        // depend de toute les entr�es
        foreach($entryEnums as $valueEnum){
          $curr_aide[$valueEnum][$title][$aide->text] = $aide->name;
        }
      }
      
      if($aide->depend_value){
        $curr_aide[$AppUI->_($aide->class.".".$linkField.".".$aide->depend_value)][$title][$aide->text] = $aide->name;
      }
    }
  }

  /**
   * Get specifically denied records from a table/module based on a user
   * @param int User id number
   * @return array
   */

  function getDeniedRecords($uid) {
    $uid = intval($uid);
    $uid || exit ("FATAL ERROR<br />" . get_class($this) . "::getDeniedRecords failed, user id = 0");

    // get read denied projects
    $deny = array();
    $sql = "
    SELECT $this->_tbl_key
    FROM $this->_tbl, permissions
    WHERE permission_user = $uid
      AND permission_grant_on = '$this->_tbl'
      AND permission_item = $this->_tbl_key
      AND permission_value = 0
    ";
    return db_loadColumn($sql);
  }

  /**
   * Returns a list of records exposed to the user
   * @param int User id number
   * @param string Optional fields to be returned by the query, default is all
   * @param string Optional sort order for the query
   * @param string Optional name of field to index the returned array
   * @param array Optional array of additional sql parameters (from and where supported)
   * @return array
   */

  // returns a list of records exposed to the user
  function getAllowedRecords($uid, $fields = "*", $orderby = "", $index = null, $extra = null) {
    $uid = intval($uid);
    $uid || exit ("FATAL ERROR<br />" . get_class($this) . "::getAllowedRecords failed");
    $deny = $this->getDeniedRecords($uid);

    $sql = "SELECT $fields"
      . "\nFROM $this->_tbl, permissions";

    if (@$extra["from"]) {
      $sql .= "," . $extra["from"];
    }
    
    $sql .= "\nWHERE permission_user = $uid"
      . "\n AND permission_value <> 0"
      . "\n AND ("
      . "\n   (permission_grant_on = 'all')"
      . "\n   OR (permission_grant_on = '$this->_tbl' AND permission_item = -1)"
      . "\n   OR (permission_grant_on = '$this->_tbl' AND permission_item = $this->_tbl_key)"
      . "\n )"
      . (count($deny) > 0 ? "\n\tAND $this->_tbl_key NOT IN (" . implode(",", $deny) . ")" : "");
    
    if (@$extra["where"]) {
      $sql .= "\n\t" . $extra["where"];
    }

    $sql .= ($orderby ? "\nORDER BY $orderby" : "");

    return db_loadHashList($sql, $index);
  }
  
  function loadLogs($type = null){
    $class = get_class($this);
    $key = $this->_tbl_key;
    $obj_id = $this->$key;
    $where = array();
    
    if($obj_id !== "" && $obj_id !== null)
      $where["object_id"] = "= '$obj_id'";
    if($class)
      $where["object_class"] = "= '$class'";
    if($type)
      $where["type"] = "= '$type'";
    $order = "date ASC";
    $list = new CUserLog;
    $list = $list->loadList($where, $order, "0, 100");
    foreach($list as $key => $value) {
      $list[$key]->loadRefsFwd();
    }   
    $this->_ref_logs  = $list;
    $this->_ref_first_log = reset($list);
    $this->_ref_last_log  = end($list);
  }

/**
 * This function register this object to a templateManager object
 */
    // Register object and references
    function fillTemplate(&$template){
    }
    
    // Register only the fields of this object
    function fillLimitedTemplate(&$template){
    }
}

function htmlReplace($find, $replace, &$source) {
  $matches = array();
  $nbFound = preg_match_all("/$find/", $source, $matches);
  $source = preg_replace("/$find/", $replace, $source);
  return $nbFound;
}

function purgeHtmlText($regexps, &$source) {
  $total = 0;
  foreach ($regexps as $find => $replace) {
    $total += htmlReplace($find, $replace, $source); 
  }

  return $total;
}




?>