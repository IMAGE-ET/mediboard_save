<?php

/**
 * Transporte des éléments de structure liés à la localisation du patient - HL7
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventMFNM05
 * Transporte des éléments de structure liés à la localisation du patient - HL7
 */
class CHL7v2EventMFNM05 extends CHL7v2EventMFN {

  /** @var string */
  public $code = "M05";

  /** @var string */
  public $struct_code = "M05";

  /**
   * Build M05 event
   *
   * @param CEntity $entite entity
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($object) {
    parent::build($object);
  }
}