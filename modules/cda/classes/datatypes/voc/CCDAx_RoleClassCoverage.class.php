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
 * abstDomain: V14008 (C-0-D11555-V13940-V14008-cpt)
 */
class CCDAx_RoleClassCoverage extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'COVPTY',
    'POLHOLD',
    'SPNSR',
    'UNDWRT',
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