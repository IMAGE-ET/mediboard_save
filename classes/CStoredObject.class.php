<?php /* $Id: mbobject.class.php 12740 2011-07-23 08:15:51Z mytto $ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 12740 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireSystemClass("CModelObject");
CAppUI::requireSystemClass("request");

/**
 * @abstract Mediboard ORM persistance layer 
 * - Persistance: storage, navigation, querying, checking, merging, seeking, cache, userlog 
 * - Configuration: permissions, object configs 
 * - Classification: modules
 */
class CStoredObject extends CModelObject {
  static $useObjectCache = true;
  static $objectCount    = 0;
  static $objectCounts   = array();
  static $objectCache    = array();
  static $cachableCounts = array();

  /**
   * @var CCanDo
   */
  var $_can           = null;
  
  var $_canRead       = null; // read permission for the object
  var $_canEdit       = null; // write permission for the object
  var $_external      = null; // true if object is has remote ids
  var $_locked        = null; // true if object is locked
  
  /**
   * References
   */
  var $_back           = null; // Back references collections
  var $_count          = null; // Back references counts
  var $_fwd            = null; // Forward references
  var $_history        = null; // Array representation of the object's evolution
  
  /**
   * Logging
   */
  var $_ref_logs       = null; // history of the object
  var $_ref_first_log  = null;
  var $_ref_last_log   = null;
  
  /**
   * @var CMbObject The object in database
   */
  var $_old            = null;
  
  // Behaviour fields
  var $_merging           = null;
  var $_purge             = null;
  var $_forwardRefMerging = null;
  
  /**
   * Check whether object is persistant (ie has a specified table) 
   * @return bool
   */
  function hasTable() {
    return $this->_spec->table;
  }
  
  /**
   * Check whether object table is installed
   * @return bool Result
   */
  function isInstalled() {
    return $this->_spec->ds->loadTable($this->_spec->table);    
  }
  
  /** 
   * Load an object by its idendifier
   * @param integer $id [optional] The object's identifier
   * @return CMbObject The loaded object if found, false otherwise
   */
  function load($id = null) {
    if ($id) {
      $this->_id = $id;
    }

    if (!$this->_id || !$this->_spec->table || !$this->_spec->key) {
      return false;
    }
    
    $sql = "SELECT * FROM `{$this->_spec->table}` WHERE `{$this->_spec->key}` = '$this->_id'";
    
    $object = $this->_spec->ds->loadObject($sql, $this);
    
    /* Not envisageable in standard load.
     * Way too much resource consuming
     * 
    if (!$object && CModule::getInstalled("dPsante400")) {
      $idex = new CIdSante400;
      $idex->object_class = $this->_class_name;
      $idex->id400 = $this->_id;
      $idex->tag = "merged";
      if ($idex->loadMatchingObject()) {
        $this->load($idex->object_id);
        return $this;
      }
    }
    */
    
    if (!$object) {
      $this->_id = null;
      return false;
    }

    $this->checkConfidential();
    $this->registerCache();
    $this->updateFormFields();
    
    return $this;
  } 

  /**
   * Build and load an object with a given GUID
   * @param guid $guid
   * @param bool $cached Use cache 
   * @return CMbObject Loaded object, null if inconsistent Guid 
   */
  static function loadFromGuid($guid, $cached = false) {
    list($class, $id) = explode('-', $guid);
    if ($class) {
      $object = new $class;
      
      if ($id && $id !== "none") {
        return $cached ? $object->getCached($id) : $object->load($id);
      }
      
      return $object;
    }
  }
  
  /**
   * Register the object into cache
   * @return void
   */
  protected final function registerCache() {
    if (!self::$useObjectCache) return;
    
    self::$objectCount++;
    
    $class_name = $this->_class_name;

    // Statistiques sur chargement d'objets
    if (!isset(self::$objectCounts[$class_name])) {
      self::$objectCounts[$class_name] = 0;
    }
    self::$objectCounts[$class_name]++;
    
    // Statistiques sur cache d'objets
    if (isset(self::$objectCache[$class_name][$this->_id])) {
      if (!isset(self::$cachableCounts[$class_name])) {
        self::$cachableCounts[$class_name] = 0;
      }
      self::$cachableCounts[$class_name]++;
    }
  
    self::$objectCache[$this->_class_name][$this->_id] =& $this;
  }
  
  /**
   * Clears the internal CMbObject cache
   * @return void
   */
  public function clearCache(){
    self::$objectCount = 0;
    self::$cachableCounts = array();
    self::$objectCache = array();
  }
  
  /**
   * Retrieve an already registered object from cache if available,
   * performs a standard load otherwise
   * @param integer $id The actual object identifier
   * @return CMbObject the retrieved object
   */
  function getCached($id) {
    if (isset(self::$objectCache[$this->_class_name][$id])) {
      return self::$objectCache[$this->_class_name][$id];
    }
    
    $this->load($id);
    return $this;
  }
  
  /**
   * Load the object database version
   */
  function loadOldObject() {
    if (!$this->_old) {
      $this->_old = new $this->_class_name;
      $this->_old->load($this->_id);
    }
  }

  /**
   * Nullify modified
   * @return int number of fields modified
   */
  function nullifyAlteredFields() {
    $count = 0;
    foreach($this->getDBFields() as $_field => $_value) {
      if($this->fieldAltered($_field)) {
        $this->$_field = null;
        $count++;
      }
    }
    return $count;
  }
  
  /**
   * Check wether a field has been modified from a non empty value
   * @param field string Field name
   * @return boolean
   */
  function fieldAltered($field) {
    return $this->fieldModified($field) && $this->_old->$field;
  }
  
  /**
   * Check wether a field has been modified or not
   * @param field string Field name
   * @param value mixed Check if modified to given value.
   * @return boolean
   */
  function fieldModified($field, $value = null) {
    // Field is not valued or Nothing in base
    if ($this->$field === null || !$this->_id) {
      return false;
    }    
    
    // Load DB version
    $this->loadOldObject();
    if (!$this->_old->_id) {
      return false;
    }
    
    $spec = $this->_specs[$field];

    // Not formally deterministic case for floats
    if ($spec instanceof CFloatSpec) {
      return !CFloatSpec::equals($this->$field, $this->_old->$field, $spec);
    }
    
    // Check against a specific value
    if ($value !== null && $this->$field != $value) {
      return false;
    }
    
    // Has it finally been modified ?
    return $this->$field != $this->_old->$field;
  }
  
  /**
   * Check wether an object has been modified (that is at least one of its fields
   * @return boolean
   */
  function objectModified() {    
    foreach ($this->getDBFields() as $propName => $propValue) {
      if ($this->fieldModified($propName)) {
        return true;
      }
    }
    
    return false;
  }
  
  /**
   * Check wether an object has just been created (no older object)
   * @param field string Field name
   * @param value mixed Check if modified to given value.
   * @return boolean
   */
  function objectCreated() {
    // Load DB version
    $this->loadOldObject();
    
    return  $this->_old->_id;
  }
  
  /**
   * Complete fields with base value if missing
   * @param [...] string Field names or an array of field names
   */
  function completeField() {
    if (!$this->_id) return;
    
    $fields = func_get_args();
    
    if (isset($fields[0]) && is_array($fields[0])) {
      $fields = $fields[0];
    }
    
    foreach ($fields as $field) {
      // Field is valued
      if ($this->$field !== null) {
        continue;
      }
      
      $this->loadOldObject();
      $this->$field = $this->_old->$field;
    }
  }
  
  /**
   * Load active module
   * @param string $name Name of the module
   * @return CModule
   */
  function loadRefModule($name) {
    return $this->_ref_module = CModule::getActive($name);
  }

  /**
   * Get the configuration of object class for a given conf path
   * @param string $path
   * @return string
   */
  function conf($path) {
    $mod_name = $this->_ref_module->mod_name;
    return CAppUI::conf("$mod_name $this->_class_name $path");
  }
  
  /**
   * Permission generic check
   * @param $permType Const enum Type of permission : PERM_READ|PERM_EDIT|PERM_DENY
   * @return boolean
   */
  function getPerm($permType) {
    return CPermObject::getPermObject($this, $permType);
  }
  
  /**
   * Gets the can-read boolean permission
   * DEPRECATED
   * @todo Should not be used, use canDo()->read instead
   * @return CCanDo 
   */ 
  function canRead() {
    return $this->_canRead = $this->getPerm(PERM_READ);
  }
  
  /**
   * Gets the can-edit boolean permission
   * DEPRECATED
   * @todo Should not be used, use canDo()->edit instead
   * @return CCanDo 
   */ 
    function canEdit() {
    return $this->_canEdit = $this->getPerm(PERM_EDIT);
  }

  /*
   * Gets the can-do object
   * @return CCanDo 
   */ 
  function canDo() {
    $canDo = new CCanDo;
    $canDo->read  = $this->canRead();
    $canDo->edit  = $this->canEdit();
    return $this->_can = $canDo;
  }
  
  /**
   * Permission wise load list alternative, with limit simulatio when necessary
   * @param $permType 
   * @param $where
   * @param $order
   * @param $limit
   * @param $group
   * @param $leftjoin
   * @return unknown_type
   */
  function loadListWithPerms($permType = PERM_READ, $where = null, $order = null, $limit = null, $group = null, $leftjoin = null) {
    // Filter with permission
    if (!$permType) {
      $this->_totalWithPerms = $this->countList($where, null, null, $group, $leftjoin);
      return $this->loadList($where, $order, $limit, $group, $leftjoin);
    }

    // Load with no limit
    $list = $this->loadList($where, $order, null, $group, $leftjoin);
    self::filterByPerm($list, $permType);
    $this->_totalWithPerms = count($list);
    
    // We simulate the MySQL LIMIT
    if ($limit) {
      $list = CRequest::artificialLimit($list, $limit);
    }
    
    return $list;
  }
  
  /**
   * Filters an object collection according to given permission
   * @param $objects array Objects to be filtered
   * @param $permType int  One of PERM_READ, PERM_EDIT
   * @return array Collection of filtered objects
   */
  static function filterByPerm(&$objects = array/*<CMbObject>*/(), $permType = PERM_READ) {
    $total = count($objects);
    foreach ($objects as $id => $object) {
      if (!$object->getPerm($permType)) {
        unset($objects[$id]);
      }
    }
    return $total - count($objects);
  }
  
  /**
   * Load all objects for given identifiers
   * @params array $ids list of identifiers
   * @return array list of objects
   */
  function loadAll($ids) {
    $where[$this->_spec->key] = CSQLDataSource::prepareIn($ids);
    return $this->loadList($where);
  }
  
  /**
   * Loads the first object matching defined properties
   * @param array|string $order Order SQL statement
   * @param array|string $group Group by SQL statement
   * @param array        $ljoin Left join SQL statement collection
   * @return integer The found object's ID
   */
  function loadMatchingObject($order = null, $group = null, $ljoin = null) {
    $request = new CRequest;
    $request->addLJoin($ljoin);
    $request->addGroup($group);
    $request->addOrder($order);

    $this->updateDBFields();
    $db_fields = $this->getDBFields();
    foreach($db_fields as $key => $value) {
      if ($value !== null) {
        $request->addWhereClause($key, "= '$value'");
      }
    }
    
    $this->loadObject($request->where, $request->order, $request->group, $request->ljoin);
    return $this->_id;
  }
  
  /**
   * Loads the list of objects matching the $this properties
   * @param array|string $order Order SQL statement
   * @param string       $limit Limit SQL statement
   * @param array|string $group Group by SQL statement
   * @param array        $ljoin Left join SQL statement collection
   * @return array The list of objects
   */
  function loadMatchingList($order = null, $limit = null, $group = null, $ljoin = null) {
    $request = new CRequest;
    $request->addLJoin($ljoin);
    $request->addGroup($group);
    $request->addOrder($order);
    $request->setLimit($limit);

    $this->updateDBFields();
    $db_fields = $this->getDBFields();
    foreach($db_fields as $key => $value) {
      if ($value !== null) {        
        $request->addWhereClause($key, "= '$value'");
      }
    }
    
    return $this->loadList($request->where, $request->order, $request->limit, $request->group, $request->ljoin);
  }
  
  /**
   * Size of the list of objects matching the $this properties
   * @param array|string $order Order SQL statement
   * @param string       $limit Limit SQL statement
   * @param array|string $group Group by SQL statement
   * @param array        $ljoin Left join SQL statement collection
   * @return integer The count
   */
  function countMatchingList($order = null, $limit = null, $group = null, $ljoin = null) {
    $request = new CRequest;
    $request->addLJoin($ljoin);
    $request->addGroup($group);
    $request->addOrder($order);
    $request->setLimit($limit);

    $this->updateDBFields();
    $db_fields = $this->getDBFields();
    foreach($db_fields as $key => $value) {
      if ($value !== null) {
        $request->addWhereClause($key, "= '$value'");
      }
    }
    return $this->countList($request->where, $request->order, $request->limit, $request->group, $request->ljoin);
  }
  
  /**
   * Loads the first object matching the query
   * @param array|string $order Order SQL statement
   * @param string       $limit Limit SQL statement
   * @param array|string $group Group by SQL statement
   * @param array        $ljoin Left join SQL statement collection
   * @return boolean True if the object was found
   */
  function loadObject($where = null, $order = null, $group = null, $ljoin = null) {
    $list = $this->loadList($where, $order, '0,1', $group, $ljoin);

    if ($list)
    foreach($list as $object) {
      $db_fields = $object->getDBFields();
      foreach($db_fields as $key => $value) {
        $this->$key = $value;
      }
      $this->updateFormFields();
      return true;
    }
    return false;
  }
  
  /**
   * Object list by a request constructor
   * @param array        $where Where SQL statement
   * @param array|string $order Order SQL statement
   * @param string       $limit Limit SQL statement
   * @param array|string $group Group by SQL statement
   * @param array        $ljoin Left join SQL statement collection
   * @param boolean      $index Add the forceindex SQL statement
   * @return self[] List of found objects, null if module is not installed
   */
  function loadList($where = null, $order = null, $limit = null, $group = null, $ljoin = null, $index = null, $found_rows = false) {
    if (!$this->_ref_module) {
      return null;
    }
    
    $request = new CRequest();
    $request->addForceIndex($index);
    $request->addLJoin($ljoin);
    $request->addWhere($where);
    $request->addGroup($group);
    $request->addOrder($order);
    $request->setLimit($limit);
    
    $query_list = $this->loadQueryList($request->getRequest($this, $found_rows));
    if ($found_rows) {
      $this->_found_rows = $this->_spec->ds->foundRows();
    }
    return $query_list;
  }
  
  /**
   * Object list for a given group
   * @param array|string $order Order SQL statement
   * @param string       $limit Limit SQL statement
   * @param array|string $group Group by SQL statement
   * @param array        $ljoin Left join SQL statement collection
   * @param boolean      $index Add the forceindex SQL statement
   * @return self[] List of found objects, null if module is not installed
   */
  function loadGroupList($where = array(), $order = null, $limit = null, $groupby = null, $ljoin = array()) {
    if (property_exists($this, "group_id")) {
      // Filtre sur l'�tablissement
      $g = CGroups::loadCurrent();
      $where["group_id"] = "= '$g->_id'";
    }
    return $this->loadList($where, $order, $limit, $groupby, $ljoin);
  }

  /**
   * Object list for given statements
   * @param array        $where Array of where clauses
   * @param array|string $order Order SQL statement
   * @param string       $limit MySQL limit clause
   * @param array|string $group Group by SQL statement
   * @param array        $ljoin Array of left join clauses
   * @return self[] List of found objects, null if module is not installed
   */
  function loadIds($where = null, $order = null, $limit = null, $group = null, $ljoin = null) {
    if (!$this->_ref_module) {
      return null;
    }
    
    $request = new CRequest();
    $request->addLJoin($ljoin);
    $request->addWhere($where);
    $request->addGroup($group);
    $request->addOrder($order);
    $request->setLimit($limit);

    $ds = $this->_spec->ds;
    return $ds->loadColumn($request->getIdsRequest($this));
  }
  
  /**
   * Object count for given statements
   * @todo Remove useless order and limit statements
   * @param array        $where Array of where clauses
   * @param array|string $order Order SQL statement
   * @param string       $limit MySQL limit clause
   * @param array|string $order Order SQL statement
   * @param array        $ljoin Array of left join clauses
   * @param string       $index Force the use of specified index
   * @return int The found objects count, null if module is not installed
   */
  function countList($where = null, $order = null, $limit = null, $group = null, $ljoin = null, $index = null) {
    if (!$this->_ref_module) {
      return null;
    }
    
    $request = new CRequest();
    $request->addForceIndex($index);
    $request->addLJoin($ljoin);
    $request->addWhere($where);
    $request->addGroup($group);
    $request->addOrder($order);
    $request->setLimit($limit);

    $ds = $this->_spec->ds;
    return $ds->loadResult($request->getCountRequest($this));
  }
  
  /**
   * Object count varialnt using builtin found-rows database feature
   * @todo Remove useless order and limit statements
   * @param array        $where Array of where clauses
   * @param array|string $order Order SQL statement
   * @param string       $limit MySQL limit clause
   * @param array|string $order Order SQL statement
   * @param array        $ljoin Array of left join clauses
   * @param string       $index Force the use of specified index
   * @return int The found objects count, null if module is not installed
   */
  function countRows($where = null, $order = null, $limit = null, $group = null, $ljoin = null, $index = null) {
    if (!$this->_ref_module) {
      return null;
    }
    
    $request = new CRequest();
    $request->addForceIndex($index);
    $request->addLJoin($ljoin);
    $request->addWhere($where);
    $request->addGroup($group);
    $request->addOrder($order);
    $request->setLimit($limit);

    $ds = $this->_spec->ds;
    return $ds->foundRows($request->getCountRequest($this, null, true));
  }
  
  /*
   * Object count of a multiple list by a request constructor using group-by statement
   * @param array        $where Array of where clauses
   * @param array|string $order Order SQL statement
   * @param string       $limit MySQL limit clause
   * @param array|string $order Order SQL statement
   * @param array        $ljoin Array of left join clauses
   * @param string       $index Force the use of specified index
   */
  function countMultipleList($where = null, $order = null, $limit = null, $group = null, $ljoin = null, $fields = array()) {
    if (!$this->_ref_module) {
      return null;
    }
    
    $request = new CRequest();
    $request->addLJoin($ljoin);
    $request->addWhere($where);
    $request->addGroup($group);
    $request->addOrder($order);
    $request->setLimit($limit);

    $ds = $this->_spec->ds;
    return $ds->loadList($request->getCountRequest($this, $fields));
  }
  
  /**
   * Object list by a request object
   * @param CRequest $request Request
   * @return self[] List of found objects, null if module is not installed
   */
  function loadListByReq(CRequest $request) {
    if (!$this->_ref_module) {
      return null;
    }
    
    return $this->loadQueryList($request->getRequest($this));
  }  
  
  /**
   * return an array of objects from a SQL SELECT query
   * @todo to optimize request, only select object oids in $query
   * @return self[] List of found objects, null if module is not installed
   */
  function loadQueryList($query) {
    $ds = $this->_spec->ds;
    $res = $ds->exec($query);
    $list = array();
    
    // @todo should replace fetchAssoc, instanciation and bind
    // while ($newObject = $ds->fetchObject($res, $this->_class_name)) { 
   
    while ($row = $ds->fetchAssoc($res)) {
      $newObject = new $this->_class_name;
      $newObject->bind($row, false);
      
      $newObject->checkConfidential();
      $newObject->updateFormFields();
      $newObject->registerCache();
      $list[$newObject->_id] = $newObject;
    }

    $ds->freeResult($res);
    return $list;
  }
  
  /**
   * References global loader
   * DEPRECATED: out of control resouce consumption
   * @return id Object id
   */
  function loadRefs() {
    if ($this->_id){  
      $this->loadRefsBack();
      $this->loadRefsFwd();
    }
    
    return $this->_id;
  }

  /**
   * Back references global loader
   * DEPRECATED: out of control resouce consumption
   * @return id Object id
   */
  function loadRefsBack() {
  }

  /**
   * Forward references global loader
   * DEPRECATED: out of control resouce consumption
   * @return id Object id
   */
  function loadRefsFwd() {
  }
  
  /**
   * Repair all non checking properties when possible
   * @return null|array if the object is ok an array of message for repaired fields
   */
  function repair() {
    $repaired = array();
    $properties = get_object_vars($this);
    foreach ($this->getValues() as $name => $value) {
      if ($value !== null) {
        if ($msg = $this->checkProperty($name)) {
          $repaired[$name] = $msg;
          $spec = $this->_specs[$name];
          if (!$spec->notNull) {
            $this->$name = "";
          }
        }
      }
    }
    return $repaired;
  }

  /**
   * Check all properties according to specification
   * @return string Store-like message
   */
  function check() {
    $debug = CAppUI::conf("debug");
    
    $msg = "";
    
    // Property level checking
    foreach ($this->_props as $propName => $propSpec) {
      if ($propName[0] !== '_') {
        if (!property_exists($this, $propName)) {
          trigger_error("La sp�cification cible la propri�t� '$propName' inexistante dans la classe '$this->_class_name'", E_USER_WARNING);
        } 
        else {
          $propValue =& $this->$propName;
          if(($propValue !== null) || (!$this->_id)) {
            $msgProp = $this->checkProperty($propName);
            
            $value = CMbString::truncate($propValue);
            $debugInfo = $debug ? "(val:\"$value\", spec:\"$propSpec\")" : "(valeur: \"$value\")";
            $fieldName = CAppUI::tr("$this->_class_name-$propName");
            $msg .= $msgProp ? " &bull; <strong title='$propName'>$fieldName</strong> : $msgProp $debugInfo <br/>" : null;
          }
        }
      }
    }
    
    if ($this->_merging) {
       return $msg;
    }

    // Class level unique checking
    // @todo Move this checking up to CStoredObject (mind the _merging escape)
    foreach ($this->_spec->uniques as $unique => $propNames) {
      $other = new $this->_class_name;
      
      foreach ($propNames as $propName) {
        $this->completeField($propName);
        $other->$propName = $value = addslashes($this->$propName);
        $values[] = "'$value'";
      }
      
      $other->loadMatchingObject();
  
      if ($other->_id && $this->_id != $other->_id) {
        return CAppUI::tr("$this->_class_name-failed-$unique") .
          " : " . implode(", ",$values);
      }
    }
    
    // Class-level xor checking
    foreach ($this->_spec->xor as $xor => $propNames) {
      $n = 0;
      $fields = array();
      foreach($propNames as $propName) {
        $this->completeField($propName);
        $fields[] = CAppUI::tr("$this->_class_name-$propName");
        if ($this->$propName) {
          $n++;
        }
      }
  
      if ($n != 1) {
        return CAppUI::tr("$this->_class_name-xorFailed-$xor").
          ": ".implode(", ", $fields).")";
      }
    }
    
    return $msg;
  }

  /**
   * Escape values for SQL queries
   * @return void
   */
  function escapeValues() {
    $values = $this->getValues();
    foreach ($values as $propName => $propValue) {
      if ($propValue) {
        $this->$propName = addslashes($propValue);
      }
    }
  }

  /**
   * Unescape Value for SQL queries
   * @return void
   */
  function unescapeValues() {
    $values = $this->getValues();
    foreach ($values as $propName => $propValue) {
      if ($propValue) {
        $this->$propName = stripslashes($propValue);
      }
    }
  }
  
  /**
   * Prepare the user log before object persistence
   * @return CUserLog null if not loggable
   */
  protected function prepareLog() {
    // If the object is not loggable
    if (!$this->_spec->loggable || $this->_purge) {
      return;
    }

    // Find changed fields
    $fields = array();
    $db_fields = $this->getDBFields();
    
    foreach ($db_fields as $propName => $propValue) {
      if ($this->fieldModified($propName)) {
        $fields[] = $propName;
      }
    }
    
    $object_id = $this->_id;
    $old = $this->_old;
    
    $type = "store";
    $extra = null;
    
    if ($old->_id == null) {
      $type = "create";
      $fields = array();
    }
    
    if ($this->_merging) {
      $type = "merge";
    }
      
    if ($this->_id == null) {
      $type = "delete";
      $object_id = $old->_id;
      $extra = $old->_view;
      $fields = array();
    }

    if (!count($fields) && $type === "store") {
      return;
    }
   
    if ($type === "store" || $type === "merge") {
      $old_values = array();
      foreach($fields as $_field) {
        $_spec = $this->_specs[$_field];
        if ($_spec instanceof CTextSpec ||
            $_spec instanceof CHtmlSpec ||
            $_spec instanceof CXmlSpec ||
            $_spec instanceof CPhpSpec) {
          continue;
        }
        $old_values[$_field] = utf8_encode($old->$_field);
      }
      $extra = json_encode($old_values);
    }
    
    $address = get_remote_address();
    
    $log = new CUserLog;
    $log->user_id = CAppUI::$instance->user_id;
    $log->object_id = $object_id;
    $log->object_class = $this->_class_name;
    $log->type = $type;
    $log->_fields = $fields;
    $log->date = mbDateTime();

    // Champs potentiellement absents
    if (CModule::getInstalled("system")->mod_version > "1.0.19") {
      $log->ip_address = $address["remote"] ? inet_pton($address["remote"]) : null;
      $log->extra = $extra;
    }
    
    return $this->_ref_last_log = $log;
  }
  
  /**
   * Prepare the user log before object persistence (store or delete)
   * @return void
   */
  private function doLog() {
    // Aucun log � produire (non loggable, pas de modifications, etc.)
    if (!$this->_ref_last_log) {
      return;
    }
    
    $this->_ref_last_log->store();
  }

  /**
   * Load logs
   * @return unknown_type
   */
  function loadLogs() {
    $this->_ref_logs = $this->loadBackRefs("logs", "date DESC", 100);

    foreach($this->_ref_logs as &$_log) {
      $_log->loadRefsFwd();
    }
    
    // the first is at the end because of the date order !
    $this->_ref_first_log = end($this->_ref_logs);
    $this->_ref_last_log  = reset($this->_ref_logs);
  }
  
  /**
   * Load object state along the time, according to user logs
   */
  function loadHistory() {
    $this->_history = array();
    $this->loadLogs();
    $clone = $this->getDBFields();
    
    foreach($this->_ref_logs as $_log) {
      $this->_history[$_log->_id] = $clone;
      
      $_log->getOldValues();
      foreach($_log->_old_values as $_old_field => $_old_value) {
        $clone[$_old_field] = $_old_value;
      }
    }
  }
    
  /**
   * Load logs concerning a given field
   * @param string $fieldName
   * @param bool   $strict
   * @param int    $limit
   * @param bool $require_extra_data 
   * @return unknown_type
   */
  function loadLogsForField($fieldName = null, $strict = false, $limit = null, $require_extra_data = false){ 
    $where = array(); 
    $where["object_id"]    = " = '$this->_id'";
    $where["object_class"] = " = '$this->_class_name'";
    
    if ($require_extra_data) {
      $where[] = "`extra` IS NOT NULL AND `extra` != '[]' AND `extra` != '{}'";
    }
    
    $log = new CUserLog();
    
    if ($strict) {
      $fields = $fieldName;
      
      if (!is_array($fieldName)) {
        $fields = array($fieldName);
      }
      
      $whereOr = array("`type` = 'create'");
      
      foreach($fields as $_field) {
        $whereOr[] = "
        `fields` = '$_field' OR 
        `fields` LIKE '$_field %' OR 
        `fields` LIKE '% $_field %' OR 
        `fields` LIKE '% $_field'";
      }
      
      $where[] = implode(" OR ", $whereOr);
    }
    else {
      $where["fields"] = " LIKE '%$fieldName%'";
    }
    
    return $log->loadList($where, "`date` DESC", $limit);
  }
  
  /**
   * Load last log concerning a given field
   * @param $fieldName
   * @param $strict
   * @return unknown_type
   */
  function loadLastLogForField($fieldName = null, $strict = false){
    $log = new CUserLog;
    $logs = $this->loadLogsForField($fieldName, $strict, 1);
    $first_log = reset($logs);
    
    if ($first_log) {
      $first_log->loadRefsFwd();
      return $first_log;
    }
    
    return $log;
  }
  
  /**
   * Check wether object has a log more recent than given hours
   * 
   * @param $nb_hours Number of hours
   * @return int
   */
  function hasRecentLog($nb_hours = 1) {
    $recent = mbDateTime("- $nb_hours HOURS");
    $where["object_id"   ] = "= '$this->_id'";
    $where["object_class"] = "= '$this->_class_name'";
    $where["date"] = "> '$recent'";
    $log = new CUserLog();
    return $log->countList($where);
  }
  
  /**
   * Returns the object's latest log
   * 
   * @return CUserLog
   */
  function loadLastLog() {
    $last_log = new CUserLog;
    $last_log->setObject($this);
    $last_log->loadMatchingObject("date DESC");
    return $this->_ref_last_log = $last_log;
  }
  
  /**
   * Returns the object's first log
   * 
   * @return CUserLog
   */
  function loadFirstLog() {
    $last_log = new CUserLog;
    $last_log->setObject($this);
    $last_log->loadMatchingObject("date ASC");
    return $this->_ref_first_log = $last_log;
  }

  /**
   * Returns a field's value at the specified date
   * 
   * @param string $date  ISO Date
   * @param string $field Field name
   * @return string
   */
  function getValueAtDate($date, $field) {
    return CUserLog::getObjectValueAtDate($this, $date, $field);
  }
  
  /**
   * Inserts a new row if id is zero or updates an existing row in the database table
   * 
   * @return null|string null if successful otherwise returns and error message
   */
  function store() {
    // Properties checking
    $this->updateDBFields();

    $this->loadOldObject();
    
    if (CAppUI::conf("readonly")) {
      return CAppUI::tr($this->_class_name) . 
        CAppUI::tr("CMbObject-msg-store-failed") .
        CAppUI::tr("Mode-readonly-msg");
      }
    
    if ($msg = $this->check()) {
      return CAppUI::tr($this->_class_name) . 
        CAppUI::tr("CMbObject-msg-check-failed") .
        CAppUI::tr($msg);
    }
    
    // Trigger before event
    $this->notify("BeforeStore");

    $spec = $this->_spec;
    
    // DB query
    if ($this->_old->_id) {
      $ret = $spec->ds->updateObject($spec->table, $this, $spec->key, $spec->nullifyEmptyStrings);
    } else {
      $keyToUpdate = $spec->incremented ? $spec->key : null;
      $ret = $spec->ds->insertObject($spec->table, $this, $keyToUpdate);
    }

    if (!$ret) {
      return CAppUI::tr($this->_class_name) . 
        CAppUI::tr("CMbObject-msg-store-failed") .
        $spec->ds->error();
    }
    
    // Pr�paration du log, doit �tre fait AVANT $this->load()
    $this->prepareLog();
    
    // Load the object to get all properties
    $this->load();
    
    // Enregistrement du log une fois le store termin�
    $this->doLog();
        
    // Trigger event
    $this->notify("AfterStore");

    $this->_old = null;
    return null;
  }

  
  /**
   * Merge an array of objects
   * @param array An array of CMbObject to merge
   * @param bool $fast Tell wether to use SQL (fast) or PHP (slow but checked and logged) algorithm
   * @return CMbObject
   */
  function merge($objects = array/*<CMbObject>*/(), $fast = false) {
    $alternative_mode = ($this->_id != null);
    
    // Modes and object count check
    if ($alternative_mode && count($objects) > 1) {
      return "mergeAlternativeTooManyObjects";
    }
    if (!$alternative_mode && count($objects) < 2) {
      return "mergeTooFewObjects";
    }
    
    // Trigger before event
    $this->notify("BeforeMerge");
    
    if (!$this->_id && $msg = $this->store()) {
      return $msg;
    }
    
    foreach ($objects as $object) {
      $this->_merging[$object->_id] = $object;
    }
    
    // If external IDs are available, we save old objects' id as external IDs
    // This must not be done after the objects deletion !
    if (CModule::getInstalled("dPsante400")) {
      foreach ($objects as $object) {
        $idex = new CIdSante400;
        $idex->tag = "merged";
        $idex->setObject($this);
        $idex->id400 = $object->_id;
        $idex->last_update = mbDateTime();
        $idex->store();
      }
    }
    
    foreach ($objects as &$object) {
      $msg = $fast ? 
        $this->fastTransferBackRefsFrom($object) :
        $this->transferBackRefsFrom($object);
        
      if ($msg) return $msg;
      if ($msg = $object->delete()) return $msg;
    }
    
    // Trigger after event
    $this->notify("AfterMerge");
    
    return $this->store();
  }
  
  /**
   * Merges an array of objects
   * @param array An array of CMbObject to merge
   * @return string Store-like message
   */
  function checkMerge($objects = array/*<CMbObject>*/()) {
    $object_class = null;
    foreach ($objects as $object) {
      if (!$object instanceof CMbObject) {
        return 'mergeNotCMbObject';
      }
      if (!$object->_id) {
        return 'mergeNoId';
      }
      if (!$object_class) {
        $object_class = $object->_class_name;
      }
      else if ($object->_class_name !== $object_class) {
        return 'mergeDifferentType';
      }
    }
  }
  

  /**
   * Count number back refreferecing object
   * @param string $backName name the of the back references to count
   * @return int the count null if back references module is not installed
   * @todo Add the missing arguments (the same as loadbackRefs)
   */
  function countBackRefs($backName) {
    if (!$backSpec = $this->makeBackSpec($backName)) {
      return null;
    }
    $backObject = new $backSpec->class;
    $backField = $backSpec->field;
    
    // Cas du module non install�
    if (!$backObject->_ref_module) {
      return null;
    }

    // Empty object
    if (!$this->_id || !$backObject->_spec->table || !$backObject->_spec->key) {
      return $this->_count[$backName] = 0;
    }
    
    $query = "SELECT COUNT({$backObject->_spec->key}) 
      FROM `{$backObject->_spec->table}`
      WHERE `$backField` = '$this->_id'";

    // Cas des meta objects
    $backSpec =& $backObject->_specs[$backField];
    $backMeta = $backSpec->meta;
    if ($backMeta) {
      $query .= "\nAND `$backMeta` = '$this->_class_name'";
    }
    
    // Comptage des backrefs
    return $this->_count[$backName] = $this->_spec->ds->loadResult($query); 
  }

  /**
   * Load named back reference collection
   * @param string       $backName Name of the collection
   * @param array        $where    Array of where clauses
   * @param array|string $order    Order SQL statement
   * @param string       $limit    MySQL limit clause
   * @param array|string $group    Group by SQL statement
   * @param array        $ljoin    Array of left join clauses
   * @return CMbObject[] the collection
   */
  function loadBackRefs($backName, $order = null, $limit = null, $group = null, $ljoin = null) {
    if (!$backSpec = $this->makeBackSpec($backName)) {
      return null;
    }

    // Cas du module non install�
    $backObject = new $backSpec->class;
    if (!$backObject->_ref_module) {
      return null;
    }

    $backField = $backSpec->field;
    $fwdSpec =& $backObject->_specs[$backField];
    $backMeta = $fwdSpec->meta;
    
    // Empty object
    if (!$this->_id) {
      return array();
    }

    // V�rification de la possibilit� de supprimer chaque backref
    $backObject->$backField = $this->_id;

    // Cas des meta objects
    if ($backMeta) {
      $backObject->$backMeta = $this->_class_name;
    }
    
    return $this->_back[$backName] = $backObject->loadMatchingList($order, $limit, $group, $ljoin);
  }

  /**
   * Load named back reference collection IDs
   * @param string       $backName Name of the collection
   * @param array        $where    Array of where clauses
   * @param array|string $order    Order SQL statement
   * @param string       $limit    MySQL limit clause
   * @param array|string $group    Group by SQL statement
   * @param array        $ljoin    Array of left join clauses
   * @return array the IDs collection
   */
  function loadBackIds($backName, $order = null, $limit = null, $group = null, $ljoin = null) {
    if (!$backSpec = $this->makeBackSpec($backName)) {
      return null;
    }

    // Cas du module non install�
    $backObject = new $backSpec->class;
    if (!$backObject->_ref_module) {
      return null;
    }

    $backField = $backSpec->field;
    $fwdSpec =& $backObject->_specs[$backField];
    $backMeta = $fwdSpec->meta;
    
    // Cas des meta objects
    if ($backMeta) {
      trigger_error("meta case anavailable", E_USER_ERROR);
    }
    
    // Empty object
    if (!$this->_id) {
      return array();
    }

    // V�rification de la possibilit� de supprimer chaque backref
    $where[$backField] = " = '$this->_id'";
    
    return $backObject->loadIds($where, $order, $limit, $group, $ljoin);
  }

  /**
   * Load the unique back reference for given collection name
   * Will check for uniqueness
   * @param string $backName The collection name
   * @return CMbObject Unique back reference if exist, concrete type empty object otherwise 
   */
  function loadUniqueBackRef($backName) {
    if (null === $backRefs = $this->loadBackRefs($backName)) {
      return null;
    }

    $count = count($backRefs);
    if ($count > 1) {
      trigger_error("'$backName' back reference should be unique (actually $count) for object of class '$this->_view'", E_USER_WARNING);
    }
    
    if (!$count) {
      $backSpec = $this->_backSpecs[$backName];
      return new $backSpec->class;
    }
    
    return reset($backRefs);
  }
  
  /**
   * Load and count all back references collections 
   * @param $limit string Limit DB query option
   * @return void
   */
  function loadAllBackRefs($limit = null) {
    foreach ($this->_backProps as $backName => $backProp) {
      $this->loadBackRefs($backName, null, $limit);
      $this->countBackRefs($backName);
    }
  }
  
  /**
   * Transfer all back refs from given object of same class using unchecked, unlogged SQL queries
   * @param CMbObject $object
   * @return string store-like error message if failed, null if successful
   */
  function fastTransferBackRefsFrom(CMbObject &$object) {
    if (!$this->_id) {
      return;
    }

    $this->makeAllBackSpecs();
    foreach ($this->_backSpecs as $backName => $backSpec) {
      $backObject = new $backSpec->class;
      $backField = $backSpec->field;

      // Cas du module non install�
      if (!$backObject->_ref_module) {
        continue;
      }
  
      // Unstored object
      if (!$backObject->_spec->table || !$backObject->_spec->key) {
        continue;
      }
      
      $query = "UPDATE `{$backObject->_spec->table}`
        SET `$backField` = '$this->_id'
        WHERE `$backField` = '$object->_id'";
  
      // Cas des meta objects
      $fwdSpec =& $backObject->_specs[$backField];
      $backMeta = $fwdSpec->meta;      
      if ($backMeta) {
        $query .= "\nAND `$backMeta` = '$object->_class_name'";
      }
      
      $this->_spec->ds->exec($query);
    }    
  }
  
  /**
   * Transfer all back refs from given object of same class
   * @param CMbObject $object
   * @return string store-like error message if failed, null if successful
   */
  function transferBackRefsFrom(CMbObject &$object) {
    if (!$object->_id) {
      trigger_error("transferNoId");
    }
    if ($object->_class_name !== $this->_class_name) {
      trigger_error("An object from type '$object->_class_name' can't be merge with an object from type '$this->_class_name'", E_USER_ERROR);
    }
    
    $object->loadAllBackRefs();
    foreach ($object->_back as $backName => $backObjects) {
      if (count($backObjects)) {
        $backSpec = $this->_backSpecs[$backName];
        $backObject = new $backSpec->class;
        $backField = $backSpec->field;
        $fwdSpec =& $backObject->_specs[$backField];
        $backMeta = $fwdSpec->meta;      
        
        // Change back field and store back objects
        foreach ($backObjects as $backObject) {
          // Use a dummy tranferer object to prevent checks on all values
          $transferer = new $backObject->_class_name;
          $transferer->_id = $backObject->_id;
          $transferer->$backField = $this->_id;
          $transferer->_forwardRefMerging = true;
          
          // Cas des meta objects
          if ($backMeta) {
            $transferer->$backMeta = $this->_class_name;
          }
          
          if ($msg = $transferer->store()) {
            return $msg;
          }
        }
      }
    }
  }
  
  /**
   * Load named forward reference
   * @param string $field Field name
   * @param bool   $cache Use object cache when possible 
   * @return CMbObject concrete loaded object 
   */
  function loadFwdRef($field, $cached = false) {
    if (isset($this->_fwd[$field]) && $this->_fwd[$field]->_id) {
      return $this->_fwd[$field];
    }

    $spec = $this->_specs[$field];
    if ($spec instanceof CRefSpec) {
      $class = $spec->meta ? $this->{$spec->meta} : $spec->class;
      
      if (!$class)  {
        return $this->_fwd[$field] = null;
      }
            
      $fwd = new $class;
      
      if ($cached) {
        $fwd = $fwd->getCached($this->$field);
      }
      else {
        $fwd->load($this->$field);
      }
      
      return $this->_fwd[$field] = $fwd;
    }
  }

  /**
   * Mass load mechanism for forward references of an object collection
   * @param CMbObject[] Array of objects
   * @return CMbObject[] Loaded collection 
   */
  static function massLoadFwdRef($objects, $field) {
    if (!count($objects)) {
      return array();
    }

    $object = reset($objects);
    $spec = $object->_specs[$field];
    
    if (!$spec instanceof CRefSpec) {
      trigger_error("Can't mass load not ref '$field' for object class '$object->_class_name'", E_USER_WARNING);
      return;
    }

    if ($spec->meta) {
      trigger_error("Can't mass load (yet!) ref '$field' with meta field '$spec->meta' for object class '$object->_class_name'", E_USER_WARNING);
      return;
    }
    
    // Trim real values
    $fwd_ids = CMbArray::pluck($objects, $field);
    $fwd_ids = array_unique($fwd_ids);
    CMbArray::removeValue("", $fwd_ids);
    if (!count($fwd_ids)) {
      return array();
    }

    $fwd = new $spec->class;
    
    $where[$fwd->_spec->key] = CSQLDataSource::prepareIn($fwd_ids);
    return $fwd->loadList($where);
  }

  /**
   * Load all forward references
   * @return void
   */
  function loadAllFwdRefs() {
    foreach ($this->_specs as $field => $spec) {
      $this->loadFwdRef($field);
    }
  }
    
  /**
   * Check whether the object can be deleted.
   * Default behaviour counts for back reference without cascade 
   * @return string|null null if ok, error message otherwise
   */
  function canDeleteEx() {
    // Empty object
    if (!$this->_id) {
      return CAppUI::tr("noObjectToDelete") . " " . CAppUI::tr($this->_class_name);
    }
    
    // Counting backrefs
    $issues = array();
    $this->makeAllBackSpecs();
    foreach ($this->_backSpecs as $backName => &$backSpec) {
      $backObject = new $backSpec->class;
      $backField = $backSpec->field;
      $fwdSpec =& $backObject->_specs[$backField];
      $backMeta = $fwdSpec->meta;

      // Cas du module non install�
      if (!$backObject->_ref_module) {
        continue;
      }
      
      // Cas de la suppression en cascade
      if ($fwdSpec->cascade) {
        
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
          $issues[] = CAppUI::tr("CMbObject-msg-cascade-issues")
            . " " . $cascadeIssuesCount 
            . "/" . count($cascadeObjects) 
            . " " . CAppUI::tr("$fwdSpec->class-back-$backName");
        }
        
        continue;
      }
      
      // V�rification du nombre de backRefs
      if (!$fwdSpec->unlink) {
        if ($backCount = $this->countBackRefs($backName)) {
          $issues[] = $backCount 
            . " " . CAppUI::tr("$fwdSpec->class-back-$backName");
        }
      }
    };
    $msg = count($issues) ?
      CAppUI::tr("CMbObject-msg-nodelete-backrefs") . ": " . implode(", ", $issues) :
      null;
    
    return $msg;
  }

  /**
   * Default delete method
   * @return null|string null if successful, an error message otherwise
   */
  function delete() {
    // Pr�paration du log
    $this->loadOldObject();
    
    // Delete checking
    if (!$this->_purge) {
      if ($msg = $this->canDeleteEx()) {
        return $msg;
      }
    }

    // Deleting backSpecs
    foreach ($this->_backSpecs as $backName => $backSpec) {
      $backObject = new $backSpec->class;
      $backField = $backSpec->field;
      $fwdSpec =& $backObject->_specs[$backField];
      $backMeta = $fwdSpec->meta;
      
      /* Cas du module non install�, 
       * Cas de l'interdiction de suppression, 
       * Cas de l'interdiction de la non liaison des backRefs */
      if (!$backObject->_ref_module || !$fwdSpec->cascade || $fwdSpec->unlink) {
        continue; 
      }
      
      $backObject->$backField = $this->_id;
      
      // Cas des meta objects
      if ($backMeta) {
        $backObject->$backMeta = $this->_class_name;
      }

      foreach ($backObject->loadMatchingList() as $object) {
        if ($msg = $object->delete()) {
          return $msg;
        }
      }
    }
    
    // Actually delete record
    $sql = "DELETE FROM {$this->_spec->table} WHERE {$this->_spec->key} = '$this->_id'";
    
    if (!$this->_spec->ds->exec($sql)) {
      return $this->_spec->ds->error();
    }
   
    // Deletion successful
    $this->_id = null;
   
    // Enregistrement du log une fois le delete termin�
    $this->prepareLog();
    $this->doLog();
        
    // Event Handlers
    $this->onAfterDelete();

    return $this->_old = null;
  }

  /**
   * Purge an entire object, including recursive back references
   * @return string Store-like message
   */
  function purge() {
    $this->loadAllBackRefs();
    foreach ($this->_back as $backName => $backRefs) {
      foreach ($backRefs as $backRef) {
        $backSpec = $this->_backSpecs[$backName];
        if ($backSpec->_notNull || $backSpec->_purgeable || $backSpec->_cascade) {
           if ($msg = $backRef->purge()) {
             return $msg;
           }
        }
        else {
          $backRef->{$backSpec->field} = "";
           if ($msg = $backRef->store()) {
             return $msg;
           }
        }
        CAppUI::setMsg("$backRef->_class_name-msg-delete", UI_MSG_ALERT);
      }
    }

    // Make sure delete won't log
    $this->_purge = "1";
    return $this->delete();
  }

  /**
   * Retrieve seekable specs from object
   *
   */
  function getSeekables() {
    $seekables = array();
    foreach ($this->_specs as $field => $spec) {
      if (isset($spec->seekable)) {
        $seekables[$field] = $spec;
      }
    }
    return $seekables;
  }

  /**
   *  Generic seek method
   *  @todo Change the order of the arguments so that it matches the loadList method
   *  @return the first 100 records which fits the keywords
   */
  function seek($keywords, $where = array(), $limit = 100, $countTotal = false, $ljoin = null, $order = null) {
    if (!is_array($keywords)) {
      $regex = '/"([^"]+)"/';
      
      $keywords = str_replace('\\"', '"', $keywords);
      
      if (preg_match_all($regex, $keywords, $matches)) { // Find quoted strings
        $keywords = preg_replace($regex, "", $keywords); // ... and remove them
      }
      
      $keywords = explode(" ", $keywords);
      
      // If there are quoted strings
      if (isset($matches[1])) {
        $quoted_strings = $matches[1];
        foreach($quoted_strings as &$_quoted) {
          $_quoted = str_replace(" ", "_", $_quoted);
        }
        $keywords = array_merge($quoted_strings, $keywords);
      }
      
      $keywords = array_filter($keywords);
    }
    
    $seekables = $this->getSeekables();
    
    $query = "FROM `{$this->_spec->table}` ";
    
    if ($ljoin && count($ljoin)) {
      foreach ($ljoin as $table => $condition) {
        $query .= "\nLEFT JOIN `$table` ON $condition";
      }
    }
    
    $noWhere = true;
    
    $query .= " WHERE 1";
    
    // Add specific where clauses
    if ($where && count($where)) {
      $noWhere = false;
      foreach ($where as $col => $value) {
        if (is_string($col)) {
          $col = str_replace('.', '`.`', $col);
        }
        if (is_numeric($col)) {
          $query .= " AND $value";
        }
        else {
          $query .= " AND `$col` $value";
        }
      }
    }
    
    // Add seek clauses
    if (count($keywords) && count($seekables)) {
      foreach($keywords as $keyword) {
        $query .= "\nAND (0";
        foreach($seekables as $field => $spec) {
          // Note: a swith won't work du to boolean trus value
          if ($spec->seekable === "equal") {
            $query .= "\nOR `{$this->_spec->table}`.`$field` = '$keyword'";
          }
          if ($spec->seekable === "begin") {
            $query .= "\nOR `{$this->_spec->table}`.`$field` LIKE '$keyword%'";
          }
          if ($spec->seekable === "end") {
            $query .= "\nOR `{$this->_spec->table}`.`$field` LIKE '%$keyword'";
          }
          if ($spec->seekable === true) {
            if ($spec instanceof CRefSpec) {
              $class = $spec->class;
              
              if (isset($spec->meta)) {
                $class = $this->{$spec->meta};
              }
              
              $object = new $class;
              $objects = $object->seek($keywords);
              
              if (count($objects)) {
                $ids = implode(',', array_keys($objects));
                $query .= "\nOR `{$this->_spec->table}`.`$field` IN ($ids)";
              }
            }
            else {
              $query .= "\nOR `{$this->_spec->table}`.`$field` LIKE '%$keyword%'";
            }
          }
        }
        
        $query .= "\n)";
      }
    } 
    else if ($noWhere){
      $query .= "\nAND 0";
    }
    
    $this->_totalSeek = null;
    
    if ($countTotal) {
      $ds = $this->_spec->ds;
      $result = $ds->query("SELECT COUNT(*) AS _total $query");
      $line = $ds->fetchAssoc($result);
      $this->_totalSeek = $line["_total"];
    }
    
    $query .= "\nORDER BY";
    if ($order) {
      $query .= "\n $order";
    } 
    else {
      foreach($seekables as $field => $spec) {
        $query .= "\n`$field`,";
      }
      $query .= "\n `{$this->_spec->table}`.`{$this->_spec->key}`";
    }
    
    if ($limit)
      $query .= "\n LIMIT $limit";

    return $this->loadQueryList("SELECT `{$this->_spec->table}`.* $query");
  }
  
  /**
   * Returns a list of objects for autocompleted fields
   * 
   * @param string $keywords
   * @param array $where [optional]
   * @param string $limit [optional]
   * @return array
   */
  function getAutocompleteList($keywords, $where = null, $limit = null) {
    return $this->seek($keywords, $where, $limit);
  }
  
}
