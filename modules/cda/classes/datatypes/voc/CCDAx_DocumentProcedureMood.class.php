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
 * abstDomain: V19460 (C-0-D10196-V19460-cpt)
 */
class CCDAx_DocumentProcedureMood extends CCDA_Datatype_Voc {

  public $_enumeration = array (
    'INT',
    'APT',
    'ARQ',
    'DEF',
    'EVN',
    'PRMS',
    'PRP',
    'RQO',
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