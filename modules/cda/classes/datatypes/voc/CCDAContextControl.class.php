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
 * vocSet: D16478 (C-0-D16478-cpt)
 */
class CCDAContextControl extends CCDA_Datatype_Voc {

  public $_enumeration = array (
  );
  public $_union = array (
    'ContextControlAdditive',
    'ContextControlNonPropagating',
    'ContextControlOverriding',
    'ContextControlPropagating',
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