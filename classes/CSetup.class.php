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
  public $mod_name;
  public $mod_version;
  public $mod_type = "user";
  
  /** @var CSQLDataSource */
  public $ds;

  // Protected vars
  public $messages     = array();
  public $revisions    = array();
  public $queries      = array();
  public $preferences  = array();
  public $functions    = array();
  public $dependencies = array();
  public $timeLimit    = array();
  public $tables       = array();
  public $datasources  = array();
  public $config_moves = array();
  
  static private $_old_pref_system = null;

  /**
   * Setup constructor, initializes the datasource
   */
  function __construct() {
    $this->ds = CSQLDataSource::get("std");
  }

  /**
   * Create a revision of a given name
   * 
   * @param string $revision Revision number of form x.y
   * 
   * @return void
   */
  function makeRevision($revision) {
     
    if (in_array($revision, $this->revisions)) {
      CModelObject::error("Revision-revision%s-already-exists", $revision);
    }
    
    $this->revisions[] = $revision;
    $this->queries     [$revision] = array();
    $this->preferences [$revision] = array();
    $this->functions   [$revision] = array();
    $this->dependencies[$revision] = array();
    $this->config_moves[$revision] = array();
    $this->timeLimit   [$revision] = null;
    end($this->revisions);
  }

  /**
   * Add a message for a specified revision
   *
   * @param string $message the message
   *
   * @return array ([$version] => [$message])
   */
  function addUpdateMessage($message) {
    return $this->messages[end($this->revisions)] = $message;
  }
  
  /**
   * Create an empty revision
   * 
   * @param string $revision Revision number of form x.y
   * 
   * @return void
   */
  function makeEmptyRevision($revision) {
    $this->makeRevision($revision);
    $this->addQuery("SELECT 0");
  }

  /**
   * Add a callback method to be executed
   * The method must return true/false
   *
   * @param string $method_name The methode to execute (from $this)
   *
   * @return void
   */
  function addMethod($method_name) {
    if (!is_string($method_name)) {
      trigger_error("You must give a method name", E_USER_WARNING);
      return;
    }

    $callable = array($this, $method_name);
    if (!is_callable($callable)) {
      $method = get_class($this).'->'.$method_name;
      trigger_error("'$method' is not callable", E_USER_WARNING);
      return;
    }

    $this->functions[current($this->revisions)][] = $callable;
  }
  
  /**
   * Add a data source to module for existence and up to date checking
   * 
   * @param string $dsn   Name of the data source
   * @param string $query Data source is considered up to date if the returns a result
   * 
   * @return void
   */
  function addDatasource($dsn, $query) {
    $this->datasources[$dsn][] = $query;
  }

  /**
   * Check all declared data sources and retrieve them as uptodate or obsolete
   * 
   * @return array The up to date and obsolete DSNs
   */
  function getDatasources() {
    $dsns = array();
    foreach ($this->datasources as $dsn => $_queries) {
      if ($ds = @CSQLDataSource::get($dsn)) {
        foreach ($_queries as $_query) {
          $dsns[$ds->loadResult($_query) ? "uptodate" : "obsolete"][] = array ($dsn, $_query);
        }
      }
      else {
        $dsns["unavailable"][] = array ($dsn, "");
      }
    }
    
    return $dsns;
  }
  
  /**
   * Set a time limit for an actual upgrade
   * 
   * @param integer $limit Limits in seconds
   * 
   * @return void
   */
  function setTimeLimit($limit) {
    $this->timeLimit[current($this->revisions)] = $limit;
  }
  
  /**
   * Associates an SQL query to a module revision
   * 
   * @param string $query         SQL query
   * @param bool   $ignore_errors Ignore errors if true
   * @param string $dsn           Data source name
   * 
   * @return void
   */
  function addQuery($query, $ignore_errors = false, $dsn = null) {
    // Table creation ?
    if (preg_match("/CREATE\s+TABLE\s+(\S+)/i", $query, $matches)) {
      $table = trim($matches[1], "`");
      $this->addTable($table);
    }
    
    // Table name changed ?
    if (
        preg_match("/RENAME\s+TABLE\s+(\S+)\s+TO\s+(\S+)/i", $query, $matches) || 
        preg_match("/ALTER\s+TABLE\s+(\S+)\s+RENAME\s+(\S+)/i", $query, $matches)
    ) {
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
   * Add a preference query to current revision definition 
   * 
   * @param string $name    Name of the preference
   * @param string $default Default value of the preference
   * 
   * @return void
   */
  function addPrefQuery($name, $default) {
    $this->preferences[current($this->revisions)][] = array($name, $default);
  }
  
  /**
   * Delete a user preference
   * 
   * @param string $name Name of the preference
   * 
   * @return void
   */
  function delPrefQuery($name) {
    return;
    
    // FIXME: les fonctions addPrefQuery et delPrefQuery sont EXECUTEES
    // a CHAQUE fois quon va sur la page de setup ! cf. pure SQL
    $pref = new CPreferences;
    $where = array();
    $where['key'] = " = '$name'";
    foreach ($pref->loadList($where) as $_pref) {
      if ($msg = $_pref->delete()) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
      }
    }
  }
  
  /**
   * Registers a table in the module
   * 
   * @param string $table Table name
   * 
   * @return void
   */
  function addTable($table) {
    if (in_array($table, $this->tables)) {
      CModelObject::error("Table-table%s-already-exists", $table);
    }
    $this->tables[] = $table;
  }

  /**
   * Remove a table in the module
   * 
   * @param string $table Table name
   * 
   * @return void
   */
  function dropTable($table) {
    CMbArray::removeValue($table, $this->tables);
  }

  /**
   * Change a table name in the module
   * 
   * @param string $tableFrom Table former name
   * @param string $tableTo   Table latter name
   * 
   * @return void
   */
  function renameTable($tableFrom, $tableTo) {
    $this->dropTable($tableFrom);
    $this->addTable($tableTo);
  }
    
  /**
   * Adds a revision dependency with another module
   * 
   * @param string $module   The dependency name
   * @param string $revision The dependency revision
   * 
   * @return void
   */
  function addDependency($module, $revision) {
    $dependency = new CObject;
    $dependency->module = $module;
    $dependency->revision = $revision;
    $this->dependencies[current($this->revisions)][] = $dependency;
  }
     
  /**
   * Adds default configuration, based on old configurations
   * 
   * @param string $new_path New config path
   * @param string $old_path Current config path, if different 
   * 
   * @return void
   */
  function addDefaultConfig($new_path, $old_path = null) {
    if (!$old_path) {
      $old_path = $new_path;
    }
    
    $config_value = @CAppUI::conf($old_path);
    
    if ($config_value === null) {
      return;
    }
    
    $query = "INSERT INTO `configuration` (`feature`, `value`) VALUES (?1, ?2)";
    $query = $this->ds->prepare($query, $new_path, $config_value);
    $this->addQuery($query);
  }
  
  /**
   * Adds default configuration from a configuration by service
   * 
   * @param string $path
   * @param string $name
   * 
   * @return void
   */
  function addDefaultConfigForGroups($path, $name) {
    $config = CConfigService::getConfigGroupForAllGroups($name);
    
    foreach ($config as $group_id => $value) {
      $query = "INSERT INTO `configuration` (`feature`, `value`, `object_id`, `object_class`)
        VALUES ('$path', '$value', '$group_id', 'CGroups')";
      $this->addQuery($query);
    }
    
    $query = "DELETE FROM `config_service` WHERE `name` = '$name'";
    $this->addQuery($query);
  }

  /**
   * Tells if we are still in the old preferences system
   *
   * @param bool $core_upgrade True if core upgrading (after initial install)
   *
   * @return bool
   */
  static function isOldPrefSystem($core_upgrade = false){
    if (self::$_old_pref_system === null || $core_upgrade) {
      $ds = CSQLDataSource::get("std");
      self::$_old_pref_system = $ds->loadField("user_preferences", "pref_name") != null;
    }
    
    return self::$_old_pref_system;
  }

  /**
   * Launches module upgrade process
   *
   * @param string $oldRevision  Revision before upgrade
   * @param bool   $core_upgrade True if it's a core module upgrade
   *
   * @return string|null New revision, null on error
   */
  function upgrade($oldRevision, $core_upgrade = false) {
    /*if (array_key_exists($this->mod_version, $this->queries)) {
      CAppUI::setMsg("Latest revision '%s' should not have upgrade queries", UI_MSG_ERROR, $this->mod_version);
      return;
    }*/

    if (
        !array_key_exists($oldRevision, $this->queries) &&
        !array_key_exists($oldRevision, $this->config_moves) &&
        !array_key_exists($oldRevision, $this->functions)
    ) {
      CAppUI::setMsg(
        "No queries, functions or config moves for '%s' setup at revision '%s'",
        UI_MSG_WARNING,
        $this->mod_name,
        $oldRevision
      );
      return null;
    }
    
    // Point to the current revision
    reset($this->revisions);
    while ($oldRevision != $currRevision = current($this->revisions)) {
      next($this->revisions);
    }

    $depFailed = false;
    
    do {
      // Check for dependencies
      foreach ($this->dependencies[$currRevision] as $dependency) {
        $module = @CModule::getInstalled($dependency->module);
        if (!$module || $module->mod_version < $dependency->revision) {
          $depFailed = true;
          CAppUI::setMsg(
            "Failed module depency for '%s' at revision '%s'",
            UI_MSG_WARNING,
            $dependency->module,
            $dependency->revision
          );
        }
      }
        
      if ($depFailed) {
        return $currRevision;
      }
      
      // Set Time Limit
      if ($this->timeLimit[$currRevision]) {
        CApp::setTimeLimit($this->timeLimit[$currRevision]);
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
        if (!call_user_func($function)) {
          $function_name = get_class($function[0])."->".$function[1];
          CAppUI::setMsg("Error in function '%s' call back for revision '%s': see logs", UI_MSG_ERROR, $function_name, $currRevision);
          return $currRevision;
        }
      }
      
      // Preferences
      foreach ($this->preferences[$currRevision] as $_pref) {
        list($_name, $_default) = $_pref;
       
        // Former pure SQL system
        // Cannot check against module version or fresh install will generate errors
        if (self::isOldPrefSystem($core_upgrade)) {
          $query = "SELECT * FROM `user_preferences` WHERE `pref_user` = '0' AND `pref_name` = '$_name'";
          $result = $this->ds->exec($query);
          
          if (!$this->ds->numRows($result)) {
            $query = "INSERT INTO `user_preferences` ( `pref_user` , `pref_name` , `pref_value` )
              VALUES ('0', '$_name', '$_default');";
            $this->ds->exec($query);
          }
        }
        // Latter object oriented system
        else {
          $pref = new CPreferences;
          
          $where = array();
          $where["user_id"] = " IS NULL";
          $where["key"] = " = '$_name'";
         
          if (!$pref->loadObject($where)) {
            $pref->key = $_name;
            $pref->value = $_default;
            $pref->store();
          }
        }
      }
      
      // Config moves
      if (count($this->config_moves[$currRevision])) {
        foreach ($this->config_moves[$currRevision] as $config) {
          CAppUI::setConf($config[1], CAppUI::conf($config[0]));
        }
      }

    } while ($currRevision = next($this->revisions));

    return $this->mod_version;
  }
  
  /**
   * Removes a module
   * Warning, it actually breaks module dependency
   * 
   * @return boolean Job done
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
   * Link to the configure pane. Should be handled in the template
   * 
   * @return void
   */
  function configure() {
    CAppUI::redirect("m=$this->mod_name&a=configure");
    return true;
  } 
  
  /**
   * Move the configuration setting for a given path in a new configuration
   * 
   * @param string $old_path Tokenized path, eg "module class var";
   * @param string $new_path Tokenized path, eg "module class var";
   * 
   * @return void
   */
  function moveConf($old_path, $new_path) {
    $this->config_moves[current($this->revisions)][] = array($old_path, $new_path);
  }
  
  /**
   * Rename a field in the user log
   * 
   * @param string $object_class object_class value of the user_log
   * @param string $from         The field to rename
   * @param string $to           The new name
   * 
   * @return void
   */
  function getFieldRenameQueries($object_class, $from, $to) {
    // CUserLog
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

    // CExClassHostField
    $query =
      "UPDATE `ex_class_host_field`
       SET
         `field` = '$to'
       WHERE
         `host_class` = '$object_class' AND
         `field` = '$from'";
    $this->addQuery($query);

    // CExClassConstraint
    $query =
      "UPDATE `ex_class_constraint`
       SET
         `field` = REPLACE(`field`, '.$object_class-$from', '.$object_class-$to')
       WHERE
         `field` LIKE '%.$object_class-$from%'";
    $this->addQuery($query);

    $query =
      "UPDATE `ex_class_constraint`
       LEFT JOIN `ex_class_event` ON `ex_class_event`.`ex_class_event_id` = `ex_class_constraint`.`ex_class_event_id`
       SET
         `field` = '$to'
       WHERE
         `field` = '$from' AND
         `ex_class_event`.`host_class` = '$object_class'";
    $this->addQuery($query);

    $query =
      "UPDATE `ex_class_constraint`
       LEFT JOIN `ex_class_event` ON `ex_class_event`.`ex_class_event_id` = `ex_class_constraint`.`ex_class_event_id`
       SET
         `field` = REPLACE(`field`, '$from.', '$to.')
       WHERE
         `field` LIKE '$from.%' AND
         `ex_class_event`.`host_class` = '$object_class'";
    $this->addQuery($query);
  }
}
