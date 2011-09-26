<?php

/**
 * SOAP Handler EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CEAISoapHandler
 * EAI SOAP Handler
 */

CAppUI::requireModuleClass("webservices", "CSoapHandler");

class CEAISoapHandler extends CSoapHandler {
  static $paramSpecs = array(
    "event" => array ( 
      "message" => "string"),
  );
  
  function event($message, $actor_id = null) {
    $actor = null;
    
    $sender_soap = new CSenderSOAP();
    if (!$actor_id) {
      $sender_soap->load($actor_id);
    } else {
      $sender_soap->user_id = CUser::get()->_id;
      $sender_soap->loadMatchingObject();
    }
    
    if ($sender_soap->_id) {
      $actor = $sender_soap;
    }

    // Dispatch EAI 
    if (!$acq = CEAIDispatcher::dispatch($message, $actor)) {
      return utf8_encode(CEAIDispatcher::$xml_error);
    }

    return $acq;
  }
}

CEAISoapHandler::$paramSpecs += CSoapHandler::$paramSpecs;

?>