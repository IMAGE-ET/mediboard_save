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
 * specDomain: V10263 (C-0-D10901-V10263-cpt)
 */
class CCDAParticipationInformationRecipient extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'IRCP',
    'NOT',
    'PRCP',
    'REFB',
    'REFT',
    'TRC',
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