<?php

/**
 * $Id$
 *  
 * @category System
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

/**
 * Error log
 */
class CErrorLog extends CStoredObject {
  public $error_log_id;

  public $user_id;
  public $server_ip;
  public $datetime;
  public $request_uid;
  public $error_type;
  public $text;
  public $file_name;
  public $line_number;

  public $stacktrace_id;
  public $param_GET_id;
  public $param_POST_id;
  public $session_data_id;

  public $signature_hash;

  public $_stacktrace;
  public $_stacktrace_output;
  public $_param_GET;
  public $_param_POST;
  public $_session_data;

  public $_category;
  public $_url;
  public $_datetime_min;
  public $_datetime_max;

  public $_similar_count;
  public $_similar_ids = array();
  public $_similar_user_ids = array();
  public $_similar_server_ips = array();

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table  = "error_log";
    $spec->key    = "error_log_id";
    $spec->loggable = false;
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["user_id"]     = "ref class|CUser unlink";
    $props["server_ip"]   = "str";
    $props["datetime"]    = "dateTime notNull";
    $props["request_uid"] = "str";
    $props["error_type"]  = "enum list|" . implode("|", CError::$_types);
    $props["text"]        = "text";
    $props["file_name"]   = "str";
    $props["line_number"] = "num";

    $props["stacktrace_id"]   = "ref class|CErrorLogData";
    $props["param_GET_id"]    = "ref class|CErrorLogData";
    $props["param_POST_id"]   = "ref class|CErrorLogData";
    $props["session_data_id"] = "ref class|CErrorLogData";

    $props["signature_hash"] = "str";

    $props["_datetime_min"] = "dateTime";
    $props["_datetime_max"] = "dateTime";

    $props["_similar_count"] = "num";
    return $props;
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields(){
    parent::updateFormFields();

    $this->completeField("error_type");

    $_types = array_flip(CError::$_types);

    if (isset($_types[$this->error_type])) {
      $_num_type = $_types[$this->error_type];
      $this->_category = CError::$_categories[$_num_type];
    }
  }

  /**
   * Completely load the object for display
   *
   * @see CMbObject::loadComplete
   * @return void
   */
  function loadComplete() {
    $this->completeField("stacktrace_id", "param_GET_id", "param_POST_id", "session_data_id");

    if ($this->stacktrace_id) {
      $this->_stacktrace = $this->getDataValue("stacktrace_id");
      $this->_stacktrace_output = array();

      foreach ($this->_stacktrace as $_trace) {
        $function = isset($_trace["class"]) ? $_trace["class"] . ":" : "";
        $function.= $_trace["function"] . "()";

        $_output = array(
          "function" => null,
          "file"     => null,
          "line"     => null,
        );
        $_output["function"] = $function;

        if (isset($_trace["file"])) {
          $_output["file"] = $_trace["file"];
        }

        if (isset($_trace["line"])) {
          $_output["line"] = $_trace["line"];
        }

        $this->_stacktrace_output[] = $_output;
      }
    }

    if ($this->param_GET_id) {
      $this->_param_GET = $this->getDataValue("param_GET_id");
      $this->_url = "?".http_build_query($this->_param_GET, true, "&");
    }

    if ($this->param_POST_id) {
      $this->_param_POST = $this->getDataValue("param_POST_id");
    }

    if ($this->session_data_id) {
      $this->_session_data = $this->getDataValue("session_data_id");
    }
  }

  /**
   * Get data decoded value
   *
   * @param string $field Field
   *
   * @return array
   */
  function getDataValue($field) {
    return json_decode($this->getDataObject($field)->value, true);
  }

  /**
   * Get data object
   *
   * @param string $field Field name
   *
   * @return CErrorLogData
   */
  function getDataObject($field) {
    return $this->loadFwdRef($field, true);
  }

  /**
   * Inserts an error log into database
   *
   * @param int    $user_id     User ID
   * @param string $server_ip   Server IP
   * @param string $datetime    Datetime
   * @param string $request_uid Request unique ID
   * @param string $error_type  Error type
   * @param string $text        Error message
   * @param string $file_name   File name
   * @param int    $line_number Line number
   * @param array  $data        Data (stacktrace, GET, POST and session)
   *
   * @return void
   *
   * @throws Exception
   */
  static function insert(
      $user_id, $server_ip,
      $datetime, $request_uid, $error_type, $text,
      $file_name, $line_number,
      $data
  ) {
    global $m, $action, $dosql;

    if (empty($action) && isset($dosql)) {
      $action = $dosql;
    }

    // Never trace error logging for readability
    $trace = CSQLDataSource::$trace;
    CSQLDataSource::$trace = false;

    $ds = CSQLDataSource::get("std");

    if (!$ds || !$ds->loadTable("error_log")) {
      throw new Exception("No datasource available");
    }


    foreach ($data as $_field => $_value) {
      if (empty($_value)) {
        $data[$_field] = null;
        continue;
      }

      $data[$_field] = CErrorLogData::insert(json_encode($_value));
    }

    $signature = array(
      "text"   => $text,
      "module" => isset($m) ? $m : null,
      "action" => isset($action) ? $action : null
    );
    $signature_hash = md5(json_encode($signature));

    $query = "INSERT INTO"." `error_log` (
      `user_id`, `server_ip`,
      `datetime`, `request_uid`, `error_type`, `text`,
      `file_name`, `line_number`,
      `stacktrace_id`, `param_GET_id`, `param_POST_id`, `session_data_id`,
      `signature_hash`
    ) VALUES (%1, %2, %3, %4, %5, %6, %7, %8, %9, %10, %11, %12, %13)";

    $query = $ds->prepare(
      $query,
      $user_id, $server_ip,
      $datetime, $request_uid, $error_type, $text,
      $file_name, $line_number,
      $data["stacktrace"], $data["param_GET"], $data["param_POST"], $data["session_data"],
      $signature_hash
    );

    $result = @$ds->exec($query);
    CSQLDataSource::$trace = $trace;

    if (!$result) {
      throw new Exception("Exec failed");
    }
  }

  /**
   * Cleanup orphan log data entries
   *
   * @return void
   */
  function cleanupLogData(){
    $ds = $this->getDS();

    // A little cleanup ....
    $ds->exec(
      "DELETE `error_log_data`
       FROM `error_log_data`
       LEFT JOIN error_log ON (
         error_log_data.error_log_data_id = error_log.stacktrace_id OR
         error_log_data.error_log_data_id = error_log.param_GET_id OR
         error_log_data.error_log_data_id = error_log.param_POST_id OR
         error_log_data.error_log_data_id = error_log.session_data_id
       )
       WHERE error_log_id IS NULL"
    );
  }

  /**
   * Delete multiple error logs
   *
   * @param int[] $ids List of error log IDs
   *
   * @return int Number of deleted rows
   */
  function deleteMulti($ids) {
    $ids = array_map("intval", $ids);

    $spec = $this->_spec;
    $ds = $this->getDS();

    $query = "DELETE FROM $spec->table WHERE $spec->key ";
    $result = $ds->exec($query.$ds->prepareIn($ids));

    if (!$result) {
      return 0;
    }

    return $ds->affectedRows();
  }
}
