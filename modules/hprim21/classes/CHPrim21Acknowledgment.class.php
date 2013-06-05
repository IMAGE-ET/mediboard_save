<?php
/**
 * $Id: CHPrim21Acknowledgment.class.php 16236 2012-07-26 08:24:14Z phenxdesign $
 * 
 * @package    Mediboard
 * @subpackage hprim21
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision: 16236 $
 */

/**
 * Class CHPrim21Acknowledgment 
 * Acknowledgment HPR
 */
class CHPrim21Acknowledgment {
  public $event;
  public $event_err;
  
  public $message;
  public $dom_message;
  
  public $message_control_id;
  public $ack_code;
  public $errors;
  public $object;
  
  public $_ref_exchange_hpr;
  public $_errors;
  public $_row;
    
  function __construct(CHPREvent $event = null) {
    $this->event = $event;
  }

  function handle($ack_hpr) {
    $this->message = new CHPrim21Message();
    $this->message->parse($ack_hpr);
    $this->dom_message = $this->message->toXML();
    
    return $this->dom_message;
  }
  
  function generateAcknowledgment($ack_code, $errors, $object = null) {
    $this->ack_code = $ack_code;
    $this->errors   = $errors;
    $this->object   = $object;

    $this->event->_exchange_hpr = $this->_ref_exchange_hpr;
    $this->event_err = new CHPREventERR($this->event);
    $this->event_err->build($this);
    $this->event_err->flatten();
  
    $this->event_err->msg_hpr = utf8_encode($this->event_err->msg_hpr);
    
    return $this->event_err->msg_hpr;
  }
  
  function getStatutAcknowledgment() {
    
  }
}
