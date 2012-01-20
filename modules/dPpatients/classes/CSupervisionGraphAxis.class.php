<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * A supervision graph Y axis
 */
class CSupervisionGraphAxis extends CMbObject {
  var $supervision_graph_axis_id = null;
  
  var $supervision_graph_id      = null;
  var $title                     = null;
  var $limit_low                 = null;
  var $limit_high                = null;
  var $display                   = null;
  var $show_points               = null;
  var $symbol                    = null;
  
  var $_ref_series               = null;
  
  static $default_yaxis = array(
    "position" => "left", 
    "labelWidth" => 20, 
    "ticks" => 6, 
    "reserveSpace" => true,
    "label" => "",
    "symbolChar" => "",
  );
  
  function getSymbolChar() {
    $this->completeField("symbol");
    
    return CMbArray::get(array(
      "circle"   => "&#x25CB;",
      "cross"    => "x",
      "diamond"  => "&#x25CA;",
      "square"   => "&#x25A1;",
      "triangle" => "&#x25B3;",
    ), $this->symbol);
  }
  
  function getAxisForFlot($count_yaxes){
    $axis_data = array(
      "symbolChar" => $this->getSymbolChar(),
      "label"      => $this->title,
    ) + self::$default_yaxis;
    
    if ($count_yaxes) {
      $axis_data["alignTicksWithAxis"] = 1;
    }
    
    if ($this->limit_low != null) {
      $axis_data["min"] = floatval($this->limit_low);
    }
    
    if ($this->limit_high != null) {
      $axis_data["max"] = floatval($this->limit_high);
    }
    
    return $axis_data;
  }
  
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "supervision_graph_axis";
    $spec->key   = "supervision_graph_axis_id";
    return $spec;
  }
  
  function getProps() {
    $props = parent::getProps();
    $props["supervision_graph_id"] = "ref notNull class|CSupervisionGraph";
    $props["title"]                = "str notNull";
    $props["limit_low"]            = "float"; // null => auto
    $props["limit_high"]           = "float"; // null => auto
    $props["display"]              = "enum list|points|lines"; // |bars
    $props["show_points"]          = "bool notNull default|0";
    $props["symbol"]               = "enum notNull list|circle|square|diamond|cross|triangle";
    return $props;
  }
  
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["series"] = "CSupervisionGraphSeries supervision_graph_axis_id";
    return $backProps;
  }
  
  /**
   * @return CSupervisionGraph
   */
  function loadRefGraph($cache = true) {
    return $this->_ref_graph = $this->loadFwdRef("supervision_graph_id", $cache);
  }
  
  function loadRefsSeries() {
    return $this->_ref_series = $this->loadBackRefs("series");
  }
  
  function updateFormFields(){
    parent::updateFormFields();
    
    $this->_view = $this->title;
  }
}
