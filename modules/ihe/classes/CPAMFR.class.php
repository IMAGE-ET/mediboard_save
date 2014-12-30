<?php

/**
 * Patient Administration Management - National extension France IHE
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CPAMFR 
 * Patient Administration Management - National extension France
 */
class CPAMFR extends CPAM {
  /**
   * @var array
   */
  static $versions = array (
    "2.1", "2.2", "2.3", "2.4", "2.5", "2.6"
  );

  /**
   * @var array
   */
  static $transaction_iti30 = array(
    "A24", "A37", "A28", "A31", "A40", "A47"
  );

  /**
   * @var array
   */
  static $transaction_iti31 = array(
    "A01", "A02", "A03", "A04", "A05", "A06", "A07",
    "A11", "A12", "A13", "A14", "A16", "A21", "A22", 
    "A25", "A38", "A44", "A54", "A55", "Z80", "Z81", 
    "Z84", "Z85", "Z99"
  );

  /**
   * @var array
   */
  static $evenements = array(
    // ITI-30
    "A24" => "CHL7EventADTA24_FR",
    "A28" => "CHL7EventADTA28_FR",
    "A31" => "CHL7EventADTA31_FR",
    "A37" => "CHL7EventADTA37_FR",
    "A40" => "CHL7EventADTA40_FR",
    "A47" => "CHL7EventADTA47_FR",

    // ITI-31
    "A01" => "CHL7EventADTA01_FR",
    "A02" => "CHL7EventADTA02_FR",
    "A03" => "CHL7EventADTA03_FR",
    "A04" => "CHL7EventADTA04_FR",
    "A05" => "CHL7EventADTA05_FR",
    "A06" => "CHL7EventADTA06_FR",
    "A07" => "CHL7EventADTA07_FR",
    "A11" => "CHL7EventADTA11_FR",
    "A12" => "CHL7EventADTA12_FR",
    "A13" => "CHL7EventADTA13_FR",
    "A14" => "CHL7EventADTA14_FR",
    "A16" => "CHL7EventADTA16_FR",
    "A21" => "CHL7EventADTA21_FR",
    "A22" => "CHL7EventADTA22_FR",
    "A25" => "CHL7EventADTA25_FR",
    "A38" => "CHL7EventADTA38_FR",
    "A44" => "CHL7EventADTA44_FR",
    "A54" => "CHL7EventADTA54_FR",
    "A55" => "CHL7EventADTA55_FR",
    "Z80" => "CHL7EventADTZ80_FR",
    "Z81" => "CHL7EventADTZ81_FR",
    "Z84" => "CHL7EventADTZ84_FR",
    "Z85" => "CHL7EventADTZ85_FR",
    "Z99" => "CHL7EventADTZ99_FR",
  );

  /**
   * Construct
   *
   * @return CPAMFR
   */
  function __construct() {
    parent::__construct();

    $this->domain = "ITI";
    $this->type   = "PAM_FR";

    $this->_categories = array(
      "ITI-30" => self::$transaction_iti30,
      "ITI-31" => self::$transaction_iti31,
    );
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
    if (in_array($code, self::$transaction_iti30)) {
      return "ITI30";
    }
    
    if (in_array($code, self::$transaction_iti31)) {
      return "ITI31";
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
        $classname = "CHL7{$_version}EventADT{$code}_FR";
        return new $classname;
      }
    }
  }
}