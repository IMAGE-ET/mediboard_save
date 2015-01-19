<?php

/**
 * Receiver HL7 v.3
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CReceiverHL7v3
 * Receiver HL7 v.3
 */

class CReceiverHL7v3 extends CInteropReceiver {
  // DB Table key

  /** @var null */
  public $receiver_hl7v3_id;

  /** @var null */
  public $_i18n_code;

  /**
   * Initialize object specification
   *
   * @return CMbObjectSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'receiver_hl7v3';
    $spec->key   = 'receiver_hl7v3_id';
    $spec->messages = array(
      "PRPA" => array ("CPRPA"),
      "XDSb" => array ("CXDSb"),
      "SVS"  => array ("CSVS"),
    );
    
    return $spec;
  }

  /**
   * Get backward reference specifications
   *
   * @return array Array of form "collection-name" => "class join-field"
   */
  function getBackProps() {
    $backProps = parent::getBackProps();
    $backProps['echanges']       = "CExchangeHL7v3 receiver_id";
    $backProps['object_configs'] = "CReceiverHL7v3Config object_id";

    return $backProps;
  }

  /**
   * Update the form (derived) fields plain fields
   *
   * @return void
   */
  function updateFormFields() {
    parent::updateFormFields();

  }

  /**
   * Get event message
   *
   * @param string $profil Profil name
   *
   * @return mixed
   */
  function getEventMessage($profil) {
    if (!array_key_exists($profil, $this->_spec->messages)) {
      return;
    }

    return reset($this->_spec->messages[$profil]);
  }

  /**
   * Send event
   *
   * @param CHL7v3Event                            $evenement      Event type
   * @param CMbObject|CXDSQueryRegistryStoredQuery $mbObject       Object
   * @param array                                  $headers        Headers
   * @param boolean                                $soapVar        XML message ?
   * @param bool                                   $message_return No Send the message
   *
   * @throws Exception
   * @return null|string
   */
  function sendEvent($evenement, CMbObject $mbObject, $headers, $soapVar = false, $message_return = false) {
    $evenement->_receiver = $this;

    if (!$this->isMessageSupported(get_class($evenement))) {
      return false;
    }

    $this->loadConfigValues();
    $evenement->build($mbObject);

    $exchange = $evenement->_exchange_hl7v3;

    if (!$exchange->message_valide) {
      return null;
    }

    if (!$this->synchronous) {
      return null;
    }

    if ($message_return) {
      return $evenement->message;
    }

    $source = CExchangeSource::get("$this->_guid-C{$evenement->event_type}");

    if (!$source->_id || !$source->active) {
      return null;
    }

    $msg = $evenement->message;
    if ($soapVar) {
      $msg = preg_replace("#^<\?xml[^>]*>#", "", $msg);
      $msg = new SoapVar($msg, XSD_ANYXML);
    }

    if ($headers) {
      $source->_headerbody = $headers;
    }

    $source->setData($msg, null, $exchange);
    try {
      $event_name = isset($evenement->_event_name) ? $evenement->_event_name : null;
      $source->send($event_name);
    }
    catch (Exception $e) {
      throw $e;
    }

    $exchange->date_echange = CMbDT::dateTime();

    $ack_data = $source->getACQ();
    if (!$ack_data) {
      $exchange->store();
      return null;
    }

    if (!$ack = self::createAcknowledgment($evenement->event_type, $ack_data)) {
      $exchange->store();
      return null;
    }

    $exchange->date_echange        = CMbDT::dateTime();
    $exchange->statut_acquittement = $ack->getStatutAcknowledgment();
    $exchange->acquittement_valide = $ack->dom->schemafilename ?
                                        $ack->dom->schemaValidate() ? 1 : 0
                                        : 1;
    $exchange->_acquittement       = $ack_data;
    $exchange->store();

    $ack->object          = $mbObject;
    $ack->_receiver       = $this;
    $ack->_exchange_hl7v3 = $exchange;

    return $ack;
  }

  /**
   * Create the acknowledgment
   *
   * @param String $event_type evenment type
   * @param String $ack_data   acknowledgment message
   *
   * @return CHL7v3AcknowledgmentPRPA|CHL7v3AcknowledgmentXDSb
   */
  static function createAcknowledgment($event_type, $ack_data) {
    $class_name = "C$event_type";
    return $class_name::getAcknowledgment($ack_data);
  }
}