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
 * specDomain: V13934 (C-0-D10882-V13922-V10883-V13934-cpt)
 */
class CCDAEntityClassManufacturedMaterial extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'MMAT',
  );
  public $_union = array (
    'EntityClassContainer',
    'EntityClassDevice',
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