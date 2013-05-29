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
 * abstDomain: V19562 (C-0-D10317-V19562-cpt)
 */
class CCDAx_ActRelationshipPertinentInfo extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'SPRT',
    'CAUS',
    'MFST',
    'REFR',
    'SUBJ',
  );
  public $_union = array (
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