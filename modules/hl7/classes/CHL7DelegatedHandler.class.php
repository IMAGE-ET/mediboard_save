<?php

/**
 * HL7 Object Handler
 *
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7DelegatedHandler
 * HL7 Object Handler
 */
class CHL7DelegatedHandler {
  static $handled     = array ();

  /**
   * Is message supported ?
   *
   * @param string         $message  Message
   * @param string         $code     Code
   * @param CReceiverHL7v2 $receiver Recevier
   *
   * @return bool
   */
  function isMessageSupported($message, $code, $receiver) {
    $i18n_code = $this->getI18nCode($receiver);
    if (!$receiver->isMessageSupported("CHL7Event{$message}{$code}{$i18n_code}")) {
      return false;
    }

    return true;
  }

  /**
   * Send message
   *
   * @param string    $profil   Profil
   * @param string    $message  Message
   * @param string    $code     Code
   * @param CMbObject $mbObject Object
   *
   * @return null|string
   *
   * @throws CMbException
   */
  function sendEvent($profil, $message, $code, CMbObject $mbObject) {
    /** @var CReceiverHL7v2 $receiver */
    $receiver = $mbObject->_receiver;

    if (!$code) {
      throw new CMbException("CITI-code-none");
    }

    $i18n_code = $this->getI18nCode($receiver);
    if ($i18n_code) {
      $profil = $profil.$i18n_code;
    }

    $class = "CHL7v2Event".$message.$code.$i18n_code;

    if (!class_exists($class)) {
      trigger_error("class-CHL7v2Event".$message.$code.$i18n_code."-not-found", E_USER_ERROR);
      return;
    }

    $event         = new $class;
    $event->profil = $profil;

    return $receiver->sendEvent($event, $mbObject);
  }
}