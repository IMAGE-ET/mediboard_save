<?php

/**
 * Represents a ERR message structure (see chapter 2.14.1) HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Classe CHPREventERR
 * Represents a ERR message structure (see chapter 2.14.1)
 */
class CHPREventERR extends CHPREvent {
  function __construct(CHPREvent $trigger_event) { 
    $this->event_type  = "ERR";
    $this->version     = $trigger_event->message->version;
    
    $this->msg_codes   = array ( 
      array(
        $trigger_event->type, $trigger_event->type_liaison
      )
    );

    $this->_exchange_hpr = $trigger_event->_exchange_hpr;
    $this->_receiver     = $trigger_event->_exchange_hpr->_ref_receiver;
    $this->_sender       = $trigger_event->_exchange_hpr->_ref_sender;
  }
  
  function build($object) {
    // Cr�ation du message HPR
    $this->message          = new CHPrim21Message();
    $this->message->version = $this->version;
    $this->message->name    = $this->event_type;
    
    // Message Header 
    $this->addH();
    
    $i = 0;
    // Errors
    foreach ($object->errors as $_error) {
      $object->_error = $_error;
      $object->_row   = ++$i;
      
      $this->addERR($object);
    }
    
    // Validation error
    
    
    // Message Footer 
    $this->addL();
  }
  
  function createSegment($name, CHL7v2SegmentGroup $parent) {
    $class = "CHPRSegment$name";
    
    if (class_exists($class)) {
      $segment = new $class($parent);
    }
    else {
      $segment = new self($parent);
    }
    
    $segment->name = substr($name, 0, 3);
    
    return $segment;
  }
  
  /*
   * H - Represents an HPR H message segment (Message Header) 
   */
  function addH() {
    $H = $this->createSegment("H", $this->message);
    $H->build($this);
  }
  
  /*
   * ERR - Represents an HPR ERR message segment (Error)
   */
  function addERR(CHPrim21Acknowledgment $acknowledgment, $error = null) {
    $ERR = $this->createSegment("ERR", $this->message);
    $ERR->acknowledgment = $acknowledgment;
    $ERR->error = $error ? $error : $acknowledgment->_error;
    $ERR->build($this);
  }
  
  /*
   * L - Represents an HPR L message segment (Message Footer) 
   */
  function addL() {
    $L = $this->createSegment("L", $this->message);
    $L->build($this);
  }
  
  function flatten() {
    $this->msg_hpr = $this->message->flatten();
    $this->message->validate();
  }
}

?>