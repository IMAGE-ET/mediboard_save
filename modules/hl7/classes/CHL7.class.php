<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Class CHL7 
 * Tools
 */
class CHL7 extends CInteropNorm {
  static $versions = array ();

  /**
   * @see parent::__construct
   */
  function __construct() {
    $this->name = "CHL7";

    parent::__construct();
  }

  /**
   * Return data format object
   *
   * @param CExchangeDataFormat $exchange Instance of exchange
   *
   * @throws CMbException
   *
   * @return object An instance of data format
   */
  static function getEvent(CExchangeDataFormat $exchange) {
    $code    = $exchange->code;
    $version = $exchange->version;

    foreach (CHL7::$versions as $_version => $_sub_versions) {
      if (in_array($version, $_sub_versions)) {
        $classname = "CHL7{$_version}Event{$exchange->type}$code";
        return new $classname;
      }
    }

    return null;
  }
  /**
   * Get object tag
   *
   * @param string $group_id Group
   *
   * @return string|null
   */
  static function getObjectTag($group_id = null) {
    // Recherche de l'établissement
    $group = CGroups::get($group_id);
    if (!$group_id) {
      $group_id = $group->_id;
    }

    $cache = new Cache(__METHOD__, array($group_id), Cache::INNER);

    if ($cache->exists()) {
      return $cache->get();
    }

    $tag = self::getDynamicTag();

    return $cache->put(str_replace('$g', $group_id, $tag));
  }

  /**
   * Get object dynamic tag
   *
   * @return string
   */
  static function getDynamicTag() {
    return CAppUI::conf("hl7 tag_default");
  }
}

CHL7::$versions = array (
  "v2" => CHL7v2::$versions,
);
