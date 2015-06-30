<?php

/**
 * A31 - Update person information - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA31_FR 
 * A31 - Add person information
 */
class CHL7v2EventADTA31_FR extends CHL7v2EventADTA31 {
  /**
   * Construct
   *
   * @param string $i18n i18n
   *
   * @return \CHL7v2EventADTA31_FR
   */
  function __construct($i18n = "FR") {
    parent::__construct($i18n);
  }

  /**
   * Build i18n segements
   *
   * @param CPatient $patient Person
   *
   * @see parent::buildI18nSegments()
   *
   * @return void
   */
  function buildI18nSegments($patient) {
    if ($this->_receiver->_configs["send_insurance"]) {
      // Insurance
      $this->addIN1($patient);

      // Insurance (Additional Information)
      $this->addIN2($patient);
    }
  }
}