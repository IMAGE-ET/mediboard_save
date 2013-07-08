<?php

/**
 * $Id$
 *  
 * @category XDS
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */
 
/**
 * classe correspondant � l?�l�ment XML rim:VersionInfo,
 * contenant la version de l?objet du registre, notamment la version de la fiche ;
 */
class CXDSVersionInfo {

  public $value;

  /**
   * Construction de l'instance
   *
   * @param String $value String
   */
  function __construct($value) {
    $this->value = $value;
  }

  /**
   * G�n�ration du xml
   *
   * @return CXDSXmlDocument
   */
  function toXML() {
    $xml = new CXDSXmlDocument();

    $xml->createVersionInfo($this->value);

    return $xml;
  }
}
