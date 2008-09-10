<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 31 $
 * @author Thomas Despoix
 */

class CAccessLog extends CMbObject {
  var $accesslog_id = null;
  
  // DB Fields
  var $module   = null;
  var $action   = null;
  var $period   = null;
  var $hits     = null;
  var $duration = null;
  var $request  = null;
  
  // Form fields
  var $_average_duration = null;
  var $_average_request  = null;
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->loggable = false;
    $spec->table = 'access_log';
    $spec->key   = 'accesslog_id';
    return $spec;
  }

  function getSpecs() {
  	$specs = parent::getSpecs();
    $specs["module"]   = "str";
    $specs["action"]   = "str";
    $specs["period"]   = "dateTime";
    $specs["hits"]     = "num pos";
    $specs["duration"] = "float";
    $specs["request"]  = "float";
    return $specs;
  }
  
  function updateFormFields() {
    parent::updateFormFields();
    if ($this->hits) {
      $this->_average_duration = $this->duration / $this->hits;
      $this->_average_request = $this->request / $this->hits;
    }
  }
  
  function loadAgregation($start, $end, $groupmod = 0, $module = 0) {
    $sql = "SELECT accesslog_id, module, action," .
        "\nSUM(hits) AS hits, SUM(duration) AS duration, SUM(request) AS request," .
        "\n0 AS grouping" .
        "\nFROM access_log" .
        "\nWHERE period BETWEEN '$start' AND '$end'";
    if($module && !$groupmod) {
      $sql .= "\nAND module = '$module'";
    }
    if($groupmod == 2) {
      $sql .= "\nGROUP BY grouping";
    }
    else if($groupmod == 1) {
      $sql .= "\nGROUP BY module ORDER BY module";
    } else {
      $sql .= "\nGROUP BY module, action ORDER BY module, action";
    }
    return $this->loadQueryList($sql);
  }
}
?>