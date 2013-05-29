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
 * specDomain: V10337 (C-0-D10317-V10337-cpt)
 */
class CCDAActRelationshipSequel extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'SEQL',
    'APND',
    'DOC',
    'ELNK',
    'GEN',
    'GEVL',
    'INST',
    'MTCH',
    'OPTN',
    'REV',
    'UPDT',
    'XFRM',
  );
  public $_union = array (
    'ActRelationshipExcerpt',
    'ActRelationshipFulfills',
    'ActRelationshipReplacement',
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