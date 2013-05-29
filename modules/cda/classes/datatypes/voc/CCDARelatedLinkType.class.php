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
 * specDomain: V19615 (C-0-D11603-V19615-cpt)
 */
class CCDARelatedLinkType extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'REL',
    'BACKUP',
    'DIRAUTH',
    'INDAUTH',
    'PART',
    'REPL',
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