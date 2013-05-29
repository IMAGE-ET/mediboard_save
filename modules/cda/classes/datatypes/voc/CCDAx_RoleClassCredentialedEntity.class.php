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
 * abstDomain: V16930 (C-0-D11555-V13940-V16930-cpt)
 */
class CCDAx_RoleClassCredentialedEntity extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'ASSIGNED',
    'QUAL',
  );
  public $_union = array (
    'LicensedEntityRole',
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