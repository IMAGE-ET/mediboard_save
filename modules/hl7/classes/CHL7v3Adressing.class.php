<?php

/**
 * Adressing - IHE
 *
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v3Adressing
 * WSAddressing
 */
class CHL7v3Adressing {
  /**
   * Cr�ation de l'ent�te
   *
   * @param string $name           The name of the SoapHeader object
   * @param mixed  $data           A SOAP header's content (PHP value)
   * @param bool   $mustunderstand Value of the mustUnderstand attribute of the SOAP header element
   *
   * @return SoapHeader
   */
  static function createHeaders($name, $data, $mustunderstand = false) {
    return new SoapHeader("http://www.w3.org/2005/08/addressing", $name, $data, $mustunderstand);
  }

  /**
   * Cr�ation d'une ent�te WS-Adressing
   *
   * @param string $action_name Action name
   * @param string $to          To
   *
   * @return CSVSMessageXML[]
   */
  static function createWSAddressing($action_name, $to) {
    $headers[] = self::createHeaders("Action"   , $action_name, true);
    $headers[] = self::createHeaders("MessageID", "urn:uuid:".CMbSecurity::generateUUID());
    $headers[] = self::createHeaders("ReplyTo"  , array("Address" => array("_", "http://www.w3.org/2005/08/addressing/anonymous")));
    $headers[] = self::createHeaders("To"       , $to, true);

    return $headers;
  }
}