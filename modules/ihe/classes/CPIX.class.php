<?php

/**
 * Patient Identifier Cross Referencing IHE
 *
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CPIX
 * Patient Identifier Cross Referencing
 */
class CPIX extends CIHE {
  /**
   * @var array
   */
  static $versions = array (
    "2.1", "2.2", "2.3", "2.4", "2.5", "2.6"
  );

  /**
   * @var array
   */
  static $transaction_iti9 = array(
    "Q23"
  );

  /**
   * @var array
   */
  static $evenements = array(
    // ITI-9
    "Q23" => "CHL7EventQBPQ23",
  );

  /**
   * Construct
   *
   * @return \CPIX
   */
  function __construct() {
    $this->domain = "ITI";
    $this->type   = "PIX";

    $this->_categories = array(
      "ITI-9" => self::$transaction_iti9
    );

    parent::__construct();
  }

  /**
   * @see parent::getEvenements
   */
  function getEvenements() {
    return self::$evenements;
  }

  /**
   * @see parent::getVersions
   */
  function getVersions() {
    return self::$versions;
  }

  /**
   * Retrieve transaction name
   *
   * @param string $code Event code
   *
   * @return string Transaction name
   */
  static function getTransaction($code) {
    if (in_array($code, self::$transaction_iti9)) {
      return "ITI9";
    }
  }

  /**
   * Return data format object
   *
   * @param CExchangeDataFormat $exchange Instance of exchange
   *
   * @return object An instance of data format
   */
  static function getEvent(CExchangeDataFormat $exchange) {
    $code    = $exchange->code;
    $version = $exchange->version;

    foreach (CHL7::$versions as $_version => $_sub_versions) {
      if (in_array($version, $_sub_versions)) {
        $classname = "CHL7{$_version}EventQBP$code";
        return new $classname;
      }
    }
  }
}