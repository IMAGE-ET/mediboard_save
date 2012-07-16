<?php 
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage classes
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Setup abstract class
 * Install, upgrade or remove modules
 */ 
class CSetup {
  // Public vars
  var $mod_name = null;
  var $mod_version = null;
  var $mod_type = "user";
  
  /**
   * @var CSQLDataSource
   */
  var $ds = null;

  // Protected vars
  var $revisions    = array();
  var $queries      = array();
  var $functions    = array();
  var $dependencies = array();
  var $timeLimit    = array();
  var $tables       = array();
  var $datasources  = array();
  var $config_moves = array();
  
  static private $_old_pref_system = null;
  
  function __construct() {
    $this->ds = CSQLDataSource::get("std");
  }

  /**
   * Create a revision of a given name
   * @param string $revision Revision number of form x.y
   */
  function makeRevision($revision) {
     
    if (in_array($revision, $this->revisions)) {
      trigger_error("Revision '$revision' already exists", E_USER_ERROR);
    }
    
    $this->revisions[] = $revision;
    $this->queries     [$revision] = array();
    $this->functions   [$revision] = array();
    $this->dependencies[$revision] = array();
    $this->config_moves[$revision] = array();
    $this->timeLimit   [$revision] = null;
    end($this->revisions);
  }
  
  /**
   * Create an empty revision
   * @param string $revision Revision number of form x.y
   */
  function makeEmptyRevision($revision) {
    $this->makeRevision($revision);
    $this->addQuery("SELECT 0");
  }
  
  /**
   * Add a callback function to be executed
   * function must return true/false
   */
  function addFunction($function) {
    $this->functions[current($this->revisions)][] = $function;
  }
  
  /**
   * Add a data source to module for existence and up to date checking
   * @param string $dsn Name of the data sourcec
   * @param string $query Data source is considered up to date if the returns a result
   */
  function addDatasource($dsn, $query) {
    $this->datasources[$dsn] = $query;
  }

  /**
   * Check all declared datasources and retrieve them as uptodate or obosolete
   * @return array The uptodate and obsolete DSNs
   */
  function getDatasources() {
    $dsns = array();
    foreach($this->datasources as $dsn => $query) {
      if ($ds = @CSQLDataSource::get($dsn)) {
        $dsns[$ds->loadResult($query) ? "uptodate" : "obsolete"][] = $dsn;
      }
      else {
        $dsns["unavailable"][] = $dsn;
      }
    }
    
    return $dsns;
  }
  
  /**
   * Set a time limit for un actual upgrade
   * @param int $limit Limits in seconds
   */
  function setTimeLimit($limit) {
    $this->timeLimit[current($this->revisions)] = $limit;
  }
  
  /**
   * Associates an SQL query to a module revision
   * 
   * @param string $query  SQL query
   * @param bool   $ignore Ignore errors if true
   */
  function addQuery($query, $ignore_errors = false, $dsn = null) {
    // Table creation ?
    if (preg_match("/CREATE\s+TABLE\s+(\S+)/i", $query, $matches)) {
      $table = trim($matches[1], "`");
      $this->addTable($table);
    }
    // Table name changed ?
    if (preg_match("/RENAME\s+TABLE\s+(\S+)\s+TO\s+(\S+)/i", $query, $matches) || 
        preg_match("/ALTER\s+TABLE\s+(\S+)\s+RENAME\s+(\S+)/i", $query, $matches)) {
      $tableFrom = trim($matches[1], "`");
      $tableTo   = trim($matches[2], "`");
      $this->renameTable($tableFrom, $tableTo);
    }
    // Table removed ?
    if (preg_match("/DROP\s+TABLE\s+(\S+)/i", $query, $matches)) {
      $table = trim($matches[1], "`");
      $this->dropTable($table);
    }
    
    $this->queries[current($this->revisions)][] = array($query, $ignore_errors, $dsn);
  }
  
  /**
   * @FIXME: Make it pure SQL, DELETE + INSERT
   * Add a preference query to current revision definition 
   * @param string $name Name of the preference
   * @param string $default Default value of the preference
   */
  function addPrefQuery($name, $default) {
    if (self::$_old_pref_system === null) {
      $ds = CSQLDataSource::get("std");
      self::$_old_pref_system = $ds->loadField("user_preferences", "pref_name") != null;
    }
    
    // Former pure SQL system
    // Cannot check against module version or fresh install will generate errors
    // Very consuming though...
    if (self::$_old_pref_system) {
      $sqlTest = "SELECT * FROM `user_preferences` WHERE `pref_user` = '0' && `pref_name` = '$name'";
      $result = $this->ds->exec($sqlTest);
      if (!$this->ds->numRows($result)) {
        $sql = "INSERT INTO `user_preferences` ( `pref_user` , `pref_name` , `pref_value` )
          VALUES ('0', '$name', '$default');";
        $this->addQuery($sql);
      }
    }
    // Latter object oriented system
    else {
      $pref = new CPreferences;
      $pref->user_id = 0;
      $pref->key = $name;
      if (!$pref->loadMatchingObject()) {
        $pref->value = $default;
        $pref->store();
      }
    }
  }
  
  /**
   * @FIXME: Make it pure SQL
   * Delete a user preference
   * @param string $name Name of the preference
   */
  function delPrefQuery($name) {
    return; 
    // FIXME: les fonctions addPrefQuery et delPrefQuery sont EXECUTEES
    // a CHAQUE fois quon va sur la page de setup ! cf. pure SQL
    $pref = new CPreferences;
    $where = array();
    $where['key'] = " = '$name'";
    foreach ($pref->loadList($where) as $_pref) {
      if ($msg = $_pref->delete())
        CAppUI::setMsg($msg, UI_MSG_ERROR);
    }
  }
  
  /**
   * Registers a table in the module
   * @param string $table Table name
   */
  function addTable($table) {
    if (in_array($table, $this->tables)) {
      trigger_error("Table '$table' already exists", E_USER_ERROR);
    }
    $this->tables[] = $table;
  }

  /**
   * Remove a table in the module
   * @param string $table Table name
   */
  function dropTable($table) {
    CMbArray::removeValue($table, $this->tables);
  }

  /**
   * Change a table name in the module
   * @param string $tableFrom Table former name
   * @param string $tableFrom Table latter name
   */
  function renameTable($tableFrom, $tableTo) {
    $this->dropTable($tableFrom);
    $this->addTable($tableTo);
  }
    
  /**
   * Adds a revision dependency with another module
   * @param string $module
   * @param string $revision
   */
  function addDependency($module, $revision) {
    $dependency = new CObject;
    $dependency->module = $module;
    $dependency->revision = $revision;
    $this->dependencies[current($this->revisions)][] = $dependency;
  }
     
  /**
   * Launches module upgrade process
   * @param string $oldRevision revision befoire upgrade
   */
  function upgrade($oldRevision) {
    if (array_key_exists($this->mod_version, $this->queries)) {
      CAppUI::setMsg("Latest revision '%s' should not have upgrade queries", UI_MSG_ERROR, $this->mod_version);
      return;
    }

    if (!array_key_exists($oldRevision, $this->queries) && 
        !array_key_exists($oldRevision, $this->config_moves)) {
      CAppUI::setMsg("No queries or config moves for '%s' setup at revision '%s'", UI_MSG_WARNING, $this->mod_name, $oldRevision);
      return;
    }
    
    // Point to the current revision
    reset($this->revisions);
    while ($oldRevision != $currRevision = current($this->revisions)) {
      next($this->revisions);
    }

    do {
      // Check for dependencies
      foreach ($this->dependencies[$currRevision] as $dependency) {
        $module = @CModule::getInstalled($dependency->module);
        if (!$module || $module->mod_version < $dependency->revision) {
          $depFailed = true;
          CAppUI::setMsg("Failed module depency for '%s' at revision '%s'", UI_MSG_WARNING, $dependency->module, $dependency->revision);
        }
      }
        
      if (@$depFailed) {
        return $currRevision;
      }
      
      // Set Time Limit
      if ($this->timeLimit[$currRevision]){
        set_time_limit($this->timeLimit[$currRevision]);
      }

      // Query upgrading
      foreach ($this->queries[$currRevision] as $_query) {
        list($query, $ignore_errors, $dsn) = $_query;
        $ds = ($dsn ? CSQLDataSource::get($dsn) : $this->ds); 
        
        if (!$ds->exec($query)) {
          if ($ignore_errors) {
            CAppUI::setMsg("Errors ignored for revision '%s'", UI_MSG_OK, $currRevision);
            continue;
          }
          CAppUI::setMsg("Error in queries for revision '%s': see logs", UI_MSG_ERROR, $currRevision);
          return $currRevision;
        }
      }
      
      // Callback upgrading
      foreach ($this->functions[$currRevision] as $function) {
        if (!call_user_func($function, $this)) {
          CAppUI::setMsg("Error in function '%s' call back for revision '%s': see logs", UI_MSG_ERROR, $function, $currRevision);
          return $currRevision;
        }
      }
      
      if (count($this->config_moves[$currRevision])) {
        $mbConfig = new CMbConfig;
        $mbConfig->load();
        // Move conf
        foreach ($this->config_moves[$currRevision] as $config) {
          if ($mbConfig->get($config[0]) !== false) {
            $mbConfig->set($config[1], $mbConfig->get($config[0]));
          }
          //$mbConfig->set($config[0], null); // FIXME : vide les DEUX parties
        }
        $mbConfig->update($mbConfig->values);
      }

    } while ($currRevision = next($this->revisions));

    return $this->mod_version;
  }
  
  /**
   * Removes a module
   * Warning, it actually breaks module dependency
   * @return bool Job done
   */
  function remove() {
    if ($this->mod_type == "core") {
      CAppUI::setMsg("Impossible de supprimer le module '%s'", UI_MSG_ERROR, $this->mod_name);
      return false;
    }
    
    $success = true;
    foreach ($this->tables as $table) {
      $query = "DROP TABLE `$table`";
      if (!$this->ds->exec($query)) {
        $success = false;
        CAppUI::setMsg("Failed to remove table '%s'", UI_MSG_ERROR, $table);
      } 
    }
    
    return $success;
  }
  
  /**
   * Link to the configure pane
   * Should be handled in the template
   */
  function configure() {
    CAppUI::redirect("m=$this->mod_name&a=configure");
    return true;
  } 
  
  /**
   * Move the configuration setting for a given path in a new configuration
   * @param $old_conf string Tokenized path, eg "module class var";
   * @param $new_conf string Tokenized path, eg "module class var";
   */
  function moveConf($old_path, $new_path) {
    $this->config_moves[current($this->revisions)][] = array($old_path, $new_path);
  }
  
  /**
   * Rename a field in the user log
   * @param $object_class object_class of the user_log
   * @param $from The field to rename
   * @param $to The new name
   */
  function getFieldRenameQueries($object_class, $from, $to) {
    $query =
      "UPDATE `user_log` 
       SET   
         `fields` = '$to', 
         `extra`  = REPLACE(`extra`, '\"$from\":', '\"$to\":')
       WHERE 
         `object_class` = '$object_class' AND 
         `fields` = '$from' AND 
         `type` IN('store', 'merge')";
    
    $this->addQuery($query);
    
    $query =
      "UPDATE `user_log` 
       SET   
         `fields` = REPLACE(`fields`, ' $from ', ' $to '), 
         `fields` = REPLACE(`fields`, '$from ' , '$to '), 
         `fields` = REPLACE(`fields`, ' $from' , ' $to'), 
         `extra`  = REPLACE(`extra`, '\"$from\":', '\"$to\":')
       WHERE 
         `object_class` = '$object_class' AND 
         `fields` LIKE '%$from%' AND 
         `type` IN('store', 'merge')";
    
    $this->addQuery($query);
  }
}
?>