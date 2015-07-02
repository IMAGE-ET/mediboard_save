<?php

/**
 * Receiver HL7v2
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CReceiverHL7v2
 * Receiver HL7v2
 */

class CReceiverHL7v2 extends CInteropReceiver {
  // DB Table key

  /** @var null */
  public $receiver_hl7v2_id;

  /** @var null */
  public $_extension;

  /** @var null */
  public $_i18n_code;

  /** @var null */
  public $_tag_hl7;

  /**
   * Initialize object specification
   *
   * @return CMbObjectSpec
   */
  function getSpec() {
    $spec = parent::getSpec();
    $spec->table = 'receiver_hl7v2';
    $spec->key   = 'receiver_hl7v2_id';
    $spec->messages = array(
      // HL7
      "MFN"    => array ("CMFN"),

      // IHE
      "PAM"    => array ("evenementsPatient"),
      "PAM_FR" => array ("evenementsPatient"),
      "DEC"    => array ("CDEC"),
      "SWF"    => array ("CSWF"),
      "PDQ"    => array ("CPDQ"),
      "PIX"    => array ("CPIX"),
    );

    return $spec;
  }

  /**
   * @see parent::getProps()
   */
  function getProps() {
    $props = parent::getProps();

    $props["_tag_hl7"] = "str";

    return $props;
  }

  /**
   * Get backward reference specifications
   *
   * @return array Array of form "collection-name" => "class join-field"
   */
  function getBackProps() {
    $backProps                   = parent::getBackProps();
    $backProps['object_configs'] = "CReceiverHL7v2Config object_id";
    $backProps['echanges']       = "CExchangeHL7v2 receiver_id";

    return $backProps;
  }

  /**
   * Update the form (derived) fields plain fields
   *
   * @return void
   */
  function updateFormFields() {
    parent::updateFormFields();

    $this->_tag_hl7 = CHL7::getObjectTag($this->group_id);

    if (!$this->_configs) {
      $this->loadConfigValues();
    }
  }

  /**
   * Get object handler
   *
   * @param CEAIObjectHandler $objectHandler Object handler
   *
   * @return mixed
   */
  function getFormatObjectHandler(CEAIObjectHandler $objectHandler) {
    $ihe_object_handlers = CIHE::getObjectHandlers();
    $object_handler_class  = get_class($objectHandler);
    if (array_key_exists($object_handler_class, $ihe_object_handlers)) {
      return $ihe_object_handlers[$object_handler_class];
    }
  }

  /**
   * Get HL7 version for one transaction
   *
   * @param string $transaction Transaction name
   *
   * @return int|string
   */
  function getHL7Version($transaction) {
    $iti_hl7_version = $this->_configs[$transaction."_HL7_version"];

    foreach (CHL7::$versions as $_version => $_sub_versions) {
      if (in_array($iti_hl7_version, $_sub_versions)) {
        return $_version;
      }
    }

    return null;
  }

  /**
   * Get internationalization code
   *
   * @param string $transaction Transaction name
   *
   * @return null
   */
  function getInternationalizationCode($transaction) {
    $iti_hl7_version = $this->_configs[$transaction."_HL7_version"];

    if (preg_match("/([A-Z]{2})_(.*)/", $iti_hl7_version, $matches)) {
      $this->_i18n_code = $matches[1];
    }

    return $this->_i18n_code;
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
   * @param CHL7v2Event $evenement Event type
   * @param CMbObject   $mbObject  Object
   *
   * @return null|string
   *
   * @throws CMbException
   */
  function sendEvent($evenement, CMbObject $mbObject) {
    $evenement->_receiver = $this;

    // build_mode = Mode simplifi� lors de la g�n�ration du message
    $this->loadConfigValues();
    CHL7v2Message::setBuildMode($this->_configs["build_mode"]);
    $evenement->build($mbObject);
    CHL7v2Message::resetBuildMode();

    if (!$msg = $evenement->flatten()) {
      return null;
    }

    $exchange = $evenement->_exchange_hl7v2;

    // Si l'�change est invalide
    if (!$exchange->message_valide) {
      return null;
    }

    // Si on n'est pas en synchrone
    if (!$this->synchronous) {
      return null;
    }

    // Si on n'a pas d'IPP et NDA
    if ($exchange->master_idex_missing) {
      return null;
    }

    $evt    = $this->getEventMessage($evenement->profil);
    $source = CExchangeSource::get("$this->_guid-$evt");

    if (!$source->_id || !$source->active) {
      return null;
    }

    if ($this->_configs["encoding"] == "UTF-8") {
      $msg = utf8_encode($msg);
    }

    $source->setData($msg, null, $exchange);
    try {
      $source->send();
    }
    catch (Exception $e) {
      throw new CMbException("CExchangeSource-no-response");
    }

    $exchange->date_echange = CMbDT::dateTime();

    $ack_data = $source->getACQ();

    if (!$ack_data) {
      $exchange->store();
      return null;
    }

    $data_format = CIHE::getEvent($exchange);

    $ack = new CHL7v2Acknowledgment($data_format);
    $ack->handle($ack_data);
    $exchange->date_echange        = CMbDT::dateTime();
    $exchange->statut_acquittement = $ack->getStatutAcknowledgment();
    $exchange->acquittement_valide = $ack->message->isOK(CHL7v2Error::E_ERROR) ? 1 : 0;
    $exchange->_acquittement       = $ack_data;
    $exchange->store();

    return $ack_data;
  }
}