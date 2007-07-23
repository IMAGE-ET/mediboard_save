<?php /* $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @author  Romain Ollivier
 *  @version $Revision: $
 */
 
/**
 * Setup abstract class
 * Install, upgrade or remove modules
 */ 
class CSetup {
  // Public vars
  var $mod_name = null;
  var $mod_version = null;
  var $ds = null;
  var $mod_type = "user";

  // Protected vars
  var $revisions = array();
  var $queries = array();
  var $functions = array();
  var $dependencies = array();
  var $timeLimit = array();
  var $tables = array();
  
  function __construct() {
  	$this->ds = CSQLDataSource::get("std");
  }

  /**
   * Creates a revision of a given name
   */
  function makeRevision($revision) {
  	 
    if (in_array($revision, $this->revisions)) {
      trigger_error("Revision '$revision' already exists", E_USER_ERROR);
    }
    
    $this->revisions[] = $revision;
    $this->queries[$revision] = array();
    $this->functions[$revision] = array();
    $this->dependencies[$revision] = array();
    $this->timeLimit[$revision] = null;
    end($this->revisions);
  }
  
  /**
   * Add a callback function to be executed
   * function must return true/false
   */
  function addFunctions($function) {
    $this->functions[current($this->revisions)][] = $function;
  }
  
  
  function setTimeLimit($limit){
    $this->timeLimit[current($this->revisions)] = $limit;
  }
  
  /**
   * Associates an SQL query to a module revision
   */
  function addQuery($query) {
    // Table creation ?
    if (preg_match("/CREATE\s+TABLE\s+(\S+)/i", $query, $matches)) {
      $table = trim($matches[1], "`");
      $this->addTable($table);
    }
    
    $this->queries[current($this->revisions)][] = $query;
  }

  /**
   * Registers a table in the module
   */
  function addTable($table) {
    if (in_array($table, $this->tables)) {
      trigger_error("Table '$table' already exists", E_USER_ERROR);
    }

    $this->tables[] = $table;
  }
    
  /**
   * Adds a revision dependency with another module
   */
  function addDependency($module, $revision) {
    $dependency = new CObject;
    $dependency->module = $module;
    $dependency->revision = $revision;
    $this->dependencies[current($this->revisions)][] = $dependency;
  }
     
  /**
   * Launches module upgrade process
   */
  function upgrade($oldRevision) {
    global $AppUI;

    if (array_key_exists($this->mod_version, $this->queries)) {
      $AppUI->setMsg("Latest revision '$this->mod_version' should not have upgrade queries", UI_MSG_ERROR);
      return;
    }

    if (!array_key_exists($oldRevision, $this->queries)) {
      $AppUI->setMsg("No queries for '$this->mod_name' setup at revision '$oldRevision'", UI_MSG_ERROR);
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
          $AppUI->setMsg("Failed module depency for '$dependency->module' at revision '$dependency->revision'", UI_MSG_WARNING, true);
        }
      }
        
      if (@$depFailed) {
        return $currRevision;
      }
      
      // Set Time Limit
      if($this->timeLimit[$currRevision]){
        set_time_limit($this->timeLimit[$currRevision]);
      }

      // Query upgrading
      foreach ($this->queries[$currRevision] as $query) {
        if (!$this->ds->exec($query)) {
          $AppUI->setMsg("Error in queries for revision '$currRevision': see logs.", UI_MSG_ERROR);
          return $currRevision;
        }
      }
      
      // Callback upgrading
      foreach ($this->functions[$currRevision] as $function) {
        if (!call_user_func($function)) {
          $AppUI->setMsg("Error in function '$function' call back for revision '$currRevision': see logs.", UI_MSG_ERROR);
          return $currRevision;
        }
      }

    } while ($currRevision = next($this->revisions));

    return $this->mod_version;
  }
  
  /**
   * Removes a module
   * Warning, it actually breaks module dependency
   */
  function remove() {
    global $AppUI;
    $success = true;
    foreach ($this->tables as $table) {
      $query = "DROP TABLE `$table`";
      if (!$this->ds->exec($query)) {
        $success = false;
        $AppUI->setMsg("Failed to remove table '$table'", UI_MSG_ERROR, true);
      } 
    }
    
    return $success;
  }
  
  /**
   * Link to the configure pane
   * Should be handled in the template
   */
  function configure() {
    global $AppUI;
    $AppUI->redirect("m=$this->mod_name&a=configure" );
    return true;
  }  
}
?>