<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hl7", "CHL7v2Entity");

class CHL7v2Component extends CHL7v2Entity {
  /**
   * @var CHL7v2Component
   */
  var $parent = null;
  
  var $children = array();
  
  /**
   * @var string
   */
  var $data = null;
  
  /**
   * Separator used in $this->parse()
   * @var string
   */
  var $separator = null;
  
  /**
   * Component's max length
   * @var integer
   */
  var $length = null;
  
  /**
   * Data type
   * @var string
   */
  var $datatype = null;
  
  /**
   * Description
   * @var string
   */
  var $description = null;
  
  /**
   * Position of self in its parent
   * @var integer
   */
  var $self_pos = null;
  
  /**
   * @var CHL7v2DataType
   */
  var $props = null;
  
  function __construct(CHL7v2Entity $parent, CHL7v2SimpleXMLElement $specs, $self_pos, $separators) {
    parent::__construct();
    
    $this->parent = $parent;
    
    // Separators stack
    $this->separator = array_shift($separators);
    $this->separators = $separators;
    
    // Intrinsic properties 
    $this->length      = (int)$specs->length;
    $this->datatype    = (string)$specs->datatype;
    $this->description = (string)$specs->description;
    $this->self_pos    = $self_pos;
    
    $this->props = CHL7v2DataType::load($this->datatype, $this->getVersion());
  }
  
  /**
   * Parse a field item into components
   * 
   * @param string $data
   * @return void
   */
  function parse($data) {
    $keep_original = $this->getField()->keep();
    
    // Is composite
    if (isset($this->separator[0]) && $this->props instanceof CHL7v2DataTypeComposite) {
      $parts = CHL7v2::split($this->separator[0], $data, $keep_original);
      
      $component_specs = $this->getSpecs()->getItems();
      foreach($component_specs as $i => $_component_spec) {
        if (array_key_exists($i, $parts)) {
          $_comp = new CHL7v2Component($this, $_component_spec, $i, $this->separators);
          $_comp->parse($parts[$i]);
        
          $this->children[] = $_comp;
        }
        elseif($_component_spec->isRequired()) {
          $this->error("bah", $this->getPath(), $this->getField());
        }
      }
    }
    
    // Scalar type (NM, ST, ID, etc)
    else {
      $this->data = $data;
    
      if (!$keep_original) {
        $this->data = $this->getMessage()->unescape($this->data);
      }
    }
  }
  
  /**
   * Fill a field item with data
   * 
   * @param array $components
   * @return void
   */
  function fill($data) {
    // Is composite
    if ($this->props instanceof CHL7v2DataTypeComposite) {
      if (!is_array($data)) {
        $data = array($data);
      }
      
      $component_specs = $this->getSpecs()->getItems();
      foreach($component_specs as $i => $_component_spec) {
        if (array_key_exists($i, $data)) {
          $_comp = new CHL7v2Component($this, $_component_spec, $i, $this->separators);
          $_comp->fill($data[$i]);
        
          $this->children[] = $_comp;
        }
        elseif($_component_spec->isRequired()) {
          $this->error("bah fill", $this->getPath(), $this->getField());
        }
      }
    }
    
    // Scalar type (NM, ST, ID, etc)
    else {
      $this->data = $this->props->toHL7($data, $this->getField());
    }
  }
  
  /**
   * @return CHL7v2DataType
   */
  function getSpecs(){
    return $this->getSchema(self::PREFIX_COMPOSITE_NAME, $this->datatype);
  }
  
  /**
   * Validate data in the field
   * @return bool
   */
  function validate(){
    return true;
    $field = $this->getField();
    $specs = $this->props;
    
    if (!$specs->validate($this->children, $field)) {
      $field->error(CHL7v2Exception::INVALID_DATA_FORMAT, var_export($this->components, true), $field);
      return false;
    }
    
    return true;
  }
  
  /**
   * @return CHL7v2Field
   */
  function getField(){
    return $this->parent->getField();
  }
  
  /**
   * @return CHL7v2Segment
   */
  function getSegment(){
    return $this->parent->getSegment();
  }
  
  /**
   * @return CHL7v2Message
   */
  function getMessage(){
    return $this->parent->getMessage();
  }
  
  /**
   * @return string
   */
  function getVersion(){
    return $this->getMessage()->getVersion();
  }
  
  function getPath(){
    $path = $this->parent->getPath();
    $path[] = $this->self_pos+1;
    return $path;
  }
  
  function __toString(){
    $field = $this->getField();
    
    if ($this->props instanceof CHL7v2DataTypeComposite) {
      $sep = $this->separator[0];
      
      if (CHL7v2Message::$decorateToString) {
        $sep = "<span class='{$this->separator[2]}'>$sep</span>";
      }
      
      $str = implode($sep, $this->children);
    }
    else {
      if ($field->keep()){
        $str = $this->data;
      }
      else {
        $str = $this->getMessage()->escape($this->data);
      }
    }
      
    if (CHL7v2Message::$decorateToString) {
      $title = $field->owner_segment->name.".".implode(".", $this->getPath())/*.".".($i+1).".".($j+1)*/." - ".$this->datatype." - ".$this->description;
      $str = "<span class='entity {$this->separator[1]}' id='{$this->separator[1]}-$this->self_pos' data-title='$title'>$str</span>";
    }
      
    return $str;
  }
}