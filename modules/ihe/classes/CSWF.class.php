<?php

/**
 * Scheduled Workflow IHE
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CSWF 
 * Scheduled Workflow
 */
class CSWF extends CIHE {
  /**
   * @var array
   */
  static $versions = array (
    "2.3", "2.3.1", "2.4", "2.5"
  );

  /**
   * @var array
   */
  static $transaction_rad3 = array(
    "O01"
  );

  /**
   * @var array
   */
  static $transaction_rad48 = array(
    "S12", "S13", "S14", "S15" 
  );

  /**
   * @var array
   */
  static $evenements = array(
    // SIU
    "S12" => "CHL7EventSIUS12",
    "S13" => "CHL7EventSIUS13",
    "S14" => "CHL7EventSIUS14",
    "S15" => "CHL7EventSIUS15",

    // ORM
    "O01" => "CHL7EventORMO01"
  );

  /**
   * Construct
   *
   * @return CSWF
   */
  function __construct() {
    $this->domain = "RAD";
    $this->type   = "SWF";

    $this->_categories = array(
      "RAD-3"  => self::$transaction_rad3,
      "RAD-48" => self::$transaction_rad48,
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
    if (in_array($code, self::$transaction_rad3)) {
      return "RAD3";
    }

    if (in_array($code, self::$transaction_rad48)) {
      return "RAD48";
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
        // Transaction RAD-48
        if (in_array($code, self::$transaction_rad48)) {
          $classname = "CHL7{$_version}EventSIU$code";
        }

        // Transaction RAD-3
        if (in_array($code, self::$transaction_rad3)) {
          $classname = "CHL7{$_version}EventORM$code";
        }

        return new $classname;
      }
    }
  }
}