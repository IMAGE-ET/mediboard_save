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
 * abstDomain: V10247 (C-0-D10901-V10247-cpt)
 */
class CCDAParticipationAncillary extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'ADM',
    'ATND',
    'CALLBCK',
    'CON',
    'DIS',
    'ESC',
    'REF',
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