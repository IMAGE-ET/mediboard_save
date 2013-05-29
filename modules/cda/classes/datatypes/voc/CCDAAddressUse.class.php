<?php

/**
 * $Id$
 *
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @link     http://www.mediboard.org
 */

/**
 * abstDomain: V190 (C-0-D201-V190-cpt)
 */
class CCDAAddressUse extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'BAD',
    'TMP',
  );
  public $_union = array (
    'HomeAddressUse',
    'WorkPlaceAddressUse',
  );


  /**
   * Retourne les propri�t�s
   *
   * @return array
   */
  function getProps() {
    parent::getProps();
    $props["data"] = "str xml|data enum|".implode("|", $this->getEnumeration(true));
    return $props;
  }
}