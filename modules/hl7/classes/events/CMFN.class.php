<?php

/**
 * Master File Notification
 *
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CMFN
 * Master File Notification
 */
class CMFN extends CHL7 {
  /**
   * @var array
   */
  static $versions = array (
    "2.1", "2.2", "2.3", "2.4", "2.5"
  );

  /**
   * @var array
   */
  static $evenements = array(
    "M05" => "CHL7EventMFNM05",
  );

  /**
   * Construct
   *
   * @return \CMFN
   */
  function __construct() {
    $this->domain = "HL7";
    $this->type   = "MFN";

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
   * Return data format object
   *
   * @param CExchangeDataFormat $exchange Instance of exchange
   *
   * @return object|null An instance of data format
   */
  static function getEvent(CExchangeDataFormat $exchange) {
    $code    = $exchange->code;
    $version = $exchange->version;

    foreach (CHL7::$versions as $_version => $_sub_versions) {
      if (in_array($version, $_sub_versions)) {
        $classname = "CHL7{$_version}EventMFN$code";
        return new $classname;
      }
    }

    return null;
  }
}