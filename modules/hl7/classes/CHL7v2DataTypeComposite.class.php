<?php /* $Id:$ */

/**
 * @package Mediboard
 * @subpackage hl7
 * @version $Revision: 10041 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

class CHL7v2DataTypeComposite extends CHL7v2DataType {
  var $components = array();
  var $description = null;
  
  protected function __construct($datatype, $version) {
    parent::__construct($datatype, $version);
    
    $specs = $this->getSpecs();
    $this->description = (string)$specs->description;
    
    foreach($specs->elements->field as $field) {
      $this->components[] = CHL7v2DataType::load((string)$field->datatype, $this->version);
    }
  }
  
  function getRegExpMB() {
    //
  }
  
  function getRegExpHL7() {
    //
  }
  
  function toHL7($components, CHLv2Field $field){
    $hl7 = array();
    
    foreach($components as $k => $component) {
      if (!array_key_exists($k, $this->components)) {
        break;
      }
      
      $hl7[] = $this->components[$k]->toHL7($component, $field);
    }
    
    return $hl7;
  }
  
  function validate($components_data, CHL7v2Field $field) {
    // Sometimes, we have a string here (OBR-32-1-2)
    if (!is_array($components_data)) {
      $components_data = array($components_data);
    }
    
    foreach($components_data as $k => $component_data) {
      if (!array_key_exists($k, $this->components)) {
        break;
      }
      
      if (!$this->components[$k]->validate($component_data, $field)) {
        //$field->error(CHL7v2Exception::INVALID_DATA_FORMAT, $this->type, $field);
        return false;
      }
    }
    
    return true;
  }
}


/* 
formulaire de fiche de liaison qui suit le patient (etapes de brancardage)

 */

