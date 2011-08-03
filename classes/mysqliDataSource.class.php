<?php /* $Id: mysqlDataSource.class.php 12478 2011-06-20 08:10:44Z lryo $ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 12478 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CMySQLiDataSource extends CMySQLDataSource {
	/**
	 * @var MySQLi
	 */
	var $link;
    
  function connect($host, $name, $user, $pass) {
    if (!class_exists("MySQLi")) {
      trigger_error( "FATAL ERROR: MySQLi support not available.  Please check your configuration.", E_USER_ERROR );
      return;
    }
    
		$this->link = new MySQLi($host, $user, $pass, $name);
		
    if ($error = $this->link->connect_error) { 
      trigger_error( "FATAL ERROR: Connection to MySQL server failed ($error)", E_USER_ERROR );
      return;
    }
     /*
    if ($name) {
      if ($this->link->select_db($name)) {
        trigger_error( "FATAL ERROR: Database not found ($name)", E_USER_ERROR );
        return;
      }
    }*/

    return $this->link;
  }

  function error() {
    return $this->link->error;
  }

  function errno() {
    return $this->link->errno;
  }

  function insertId() {
    return $this->link->insert_id;
  }

  function query($query) {
    return $this->link->query($query);
  }

  function freeResult($result) {
    $result->free();
  }

  function numRows($result) {
    return $result->num_rows;
  }

  function affectedRows() {
    return $this->link->affected_rows();
  }
  
  function fetchRow($result) {
    return $result->fetch_row();
  }

  function fetchAssoc($result) {
    return $result->fetch_assoc();
  }

  function fetchArray($result) {
    return $result->fetch_array();
  }

  function fetchObject($result, $class_name = null, $params = array()) {
    if (empty($class_name))
      return $result->fetch_object();
      
    if (empty($params))
      return $result->fetch_object($class_name);
    
    return $result->fetch_object($class_name, $params);
  }

  function escape($value) {
    return $this->link->escape_string($value);
  }

  function version() {
    return $this->link->server_info;
  }
}

?>