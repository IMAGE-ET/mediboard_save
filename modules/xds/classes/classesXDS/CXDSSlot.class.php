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
 * classe correspondant � l?�l�ment XML rim:Slot, contenant l?insertion
 * d?une liste variable d?attributs suppl�mentaires � un objet du registre ;
 */
class CXDSSlot {

  public $name;
  public $data = array();

  /**
   * Cr�ation d'une instance
   *
   * @param String   $name String
   * @param String[] $data String[]
   */
  function __construct($name, $data) {
    $this->name = $name;
    $this->data = $data;
  }

  /**
   * G�n�ration de xml
   *
   * @return CXDSXmlDocument
   */
  function toXML() {
    $xml = new CXDSXmlDocument();
    $xml->createSlotRoot($this->name);
    $xml->createSlotValue($this->data);

    return $xml;
  }
}
