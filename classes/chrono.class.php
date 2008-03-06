<?php /* CLASSES $Id$ */

/**
 *  @package Mediboard
 *  @subpackage classes
 *  @author  Thomas Despoix
 *  @version $Revision: 1575 $
 */
 
// Chronometer
class Chronometer {
  var $total = 0;
  var $step  = 0;
  var $maxStep = 0;
  var $avgStep = 0;
  var $nbSteps = 0;
  
  var $report = array();
    
  function Chronometer() {
  }
  
  function microtimeFloat() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
  }
  
  function start() {
    $this->nbSteps++;
    $this->step = microtime(true); //$this->microtimeFloat();
  }
  
  function stop($key = "") {
    $this->step = microtime(true) - $this->step;
    $this->total += $this->step;
    $this->maxStep = max($this->maxStep, $this->step);
    $this->avgStep = $this->total/$this->nbSteps;

    if ($key) {
      if (!array_key_exists($key, $this->report)) {
        $this->report[$key] = new CObject;
        $report =& $this->report[$key];
        $report->nbSteps = 0;
        $report->step = 0;
        $report->total = 0;
        $report->maxStep = 0;
        $report->avgStep = 0;
      }
      
      $report =& $this->report[$key];
      $report->nbSteps++;
      $report->step = $this->step;
      $report->total += $report->step;
      $report->maxStep = max($report->maxStep, $report->step);
      $report->avgStep = $report->total/$report->nbSteps;
    } 
  }
}
?>