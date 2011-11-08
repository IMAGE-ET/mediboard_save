<?php

/**
 * Represents an HL7 MRG message segment (Merge Patient Information) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentMRG 
 * MRG - Represents an HL7 MRG message segment (Merge Patient Information)
 */

class CHL7v2SegmentMRG extends CHL7v2Segment {
  var $name             = "MRG";
  
  /**
   * @var CPatient
   */
  var $patient_eliminee = null;
  
  function build(CHL7v2Event $event) {
    parent::build($event);
    
    $patient_eliminee  = $this->patient_eliminee;
    $group             = $event->_receiver->_ref_group;
    
    $data = array();
    
    // MRG-1: Prior Patient Identifier List (CX) (repeating)
    $data[] = $this->getPersonIdentifiers($patient_eliminee, $group);
    
    // MRG-2: Prior Alternate Patient ID (CX) (optional repeating)
    $data[] = null;
    
    // MRG-3: Prior Patient Account Number (CX) (optional)
    $data[] = null;
    
    // MRG-4: Prior Patient ID (CX) (optional)
    $data[] = null;
    
    // MRG-5: Prior Visit Number (CX) (optional)
    $data[] = null;
    
    // MRG-6: Prior Alternate Visit ID (CX) (optional)
    $data[] = null;
    
    // MRG-7: Prior Patient Name (XPN) (optional repeating)
    $data[] = $this->getXPN($patient_eliminee);;
    
    $this->fill($data);
  }
  
}

?>