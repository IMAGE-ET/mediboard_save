<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

/**
 * A supervision graph Y axis
 */
class CSupervisionGraphAxis extends CMbObject {
  public $supervision_graph_axis_id;
  
  public $supervision_graph_id;
  public $title;
  public $limit_low;
  public $limit_high;
  public $display;
  public $show_points;
  public $symbol;

  /** @var CSupervisionGraphSeries[] */
  public $_ref_series;

  /** @var CSupervisionGraphAxisValueLabel[] */
  public $_ref_labels;

  /** @var CSupervisionGraph */
  public $_ref_graph;
  
  static $default_yaxis = array(
    "position"     => "left",
    "labelWidth"   => 60, // FIXME
    "ticks"        => 6,
    "reserveSpace" => true,
    "label"        => "",
    "symbolChar"   => "",
    "axis_id"      => null,
  );

  static $_symbol_chars = array(
    "circle"   => "&#x25CB;",
    "cross"    => "x",
    "diamond"  => "&#x25CA;",
    "square"   => "&#x25A1;",
    "triangle" => "&#x25B3;",
  );

  /**
   * @see parent::getSpec()
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = "supervision_graph_axis";
    $spec->key   = "supervision_graph_axis_id";
    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();
    $props["supervision_graph_id"] = "ref notNull class|CSupervisionGraph cascade";
    $props["title"]                = "str notNull";
    $props["limit_low"]            = "float"; // null => auto
    $props["limit_high"]           = "float"; // null => auto
    $props["display"]              = "enum list|points|lines|bars|stack|bandwidth";
    $props["show_points"]          = "bool notNull default|1";
    $props["symbol"]               = "enum notNull list|circle|square|diamond|cross|triangle";
    return $props;
  }

  /**
   * Get an HTML representation of the symbol char
   *
   * @return string The HTML symbol char
   */
  function getSymbolChar() {
    $this->completeField("symbol", "show_points", "display");

    if (!$this->show_points && $this->display != "points") {
      return null;
    }

    return CMbArray::get(self::$_symbol_chars, $this->symbol);
  }

  /**
   * Get Flot-formatted axis data
   *
   * @param int $count_yaxes Number of axes to build
   *
   * @return array
   */
  function getAxisForFlot($count_yaxes){
    $axis_data = array(
      "symbolChar" => $this->getSymbolChar(),
      "label"      => $this->title,
      "min"        => null,
      "max"        => null,
      "axis_id"    => $this->_id,
    ) + self::$default_yaxis;

    $labels = $this->loadRefsLabels();
    if (count($labels) > 0) {
      $ticks = array();
      foreach ($labels as $_label) {
        $ticks[] = array(floatval($_label->value), utf8_encode($_label->title));
      }
      $axis_data["ticks"] = $ticks;
    }
    else {
      $height = $this->loadRefGraph()->height;
      $axis_data["ticks"] = round($height / 25);
    }

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

  /**
   * @see parent::getBackProps()
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps["series"] = "CSupervisionGraphSeries supervision_graph_axis_id";
    $backProps["labels"] = "CSupervisionGraphAxisValueLabel supervision_graph_axis_id";
    return $backProps;
  }

  /**
   * Load the graph
   *
   * @param bool $cache Use object cache
   *
   * @return CSupervisionGraph
   */
  function loadRefGraph($cache = true) {
    return $this->_ref_graph = $this->loadFwdRef("supervision_graph_id", $cache);
  }

  /**
   * Load series
   *
   * @return CSupervisionGraphSeries[]
   */
  function loadRefsSeries() {
    return $this->_ref_series = $this->loadBackRefs("series");
  }

  /**
   * Load value labels
   *
   * @return CSupervisionGraphAxisValueLabel[]
   */
  function loadRefsLabels() {
    return $this->_ref_labels = $this->loadBackRefs("labels");
  }

  /**
   * @see parent::updateFormFields()
   */
  function updateFormFields(){
    parent::updateFormFields();
    
    $this->_view = $this->title;
  }
}
