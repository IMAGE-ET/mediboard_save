<?php /* $Id: CMbXMLDocument.class.php 13439 2011-10-10 13:29:15Z flaviencrochard $ */

/**
 * @package Mediboard
 * @subpackage classes
 * @version $Revision: 13439 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

class CMbObjectImport {
  static $all = array();
  
  var $class;
  var $instance;
  
  var $values;
  var $ids;
  
  var $refs;
  var $collections;
  
  var $similar;
  
  function __construct($class, $values, $ids) {
    $this->class  = $class;
    $this->values = $values;
    $this->ids    = $ids;
    
    $this->instance = new $class;
    $this->similar = $this->instance->getSimilar($values);
    
    $this->mapToObject();
    
    foreach($ids as $fwd => $id) {
      mbTrace($this->getObjectGuidByFwdRef($this->instance, $fwd));
    }
    
    self::$all[$ids["id"]] = $this;
  }
  
  function getObjectGuidByFwdRef(CMbObject $object, $fwd) {
    // Primary key
    if($fwd === "id" || $fwd === $object->_spec->key) return $object;
    
    $spec = $object->_specs[$fwd]; // We assume it's alway a CRefSpec
    
    $class = $spec->meta ? $object->{$spec->meta} : $spec->class;
    
    if (!$class)  {
      return null;
    }
    
    $id = $object->$fwd;
    return "$class-$id";
  }
  
  function mapToObject(){
    $obj = $this->instance;
    
    foreach($this->values as $field => $value) {
      $obj->$field = $value;
    }
    
    foreach($this->ids as $field => $value) {
      $obj->$field = $value;
    }
  }
}

class CMbXMLObjectExport extends CMbXMLDocument {
  var $object_class = null;
  var $object_id = null;
  
  var $objects_values = array();
  
  function load($file){
    parent::load($file);
    
    $root = $this->documentElement;
    
    list($this->object_class, $this->object_id) = explode("-", $root->nodeName);
    
    $objectNodes = $root->childNodes;
    $objects = array();
    
    foreach($objectNodes as $node) {
      $values = $this->getFields($node);
      $refs = $this->getRefs($node);
      $objects[$node->getAttribute("id")] = new CMbObjectImport($node->nodeName, $values, $refs);
    }
    
    $this->objects_values = $objects;
  }
  
  function getFields(DOMNode $node) {
    $fields = array();
    
    foreach($node->childNodes as $_node) {
      $fields[$_node->nodeName] = $_node->nodeValue;
    }
    
    return $fields;
  }
  
  function getRefs(DOMNode $node) {
    $refs = array();
    $attributes = $node->attributes;
    
    for($i = 0; $i < $attributes->length; $i++) {
      $_attr = $attributes->item($i);
      $refs[$_attr->nodeName] = $_attr->nodeValue;
    }
    
    return $refs;
  }
}
