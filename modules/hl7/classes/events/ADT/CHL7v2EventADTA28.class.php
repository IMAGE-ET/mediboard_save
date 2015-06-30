<?php

/**
 * A28 - Add person information - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA28 
 * A28 - Add person information
 */
class CHL7v2EventADTA28 extends CHL7v2EventADT implements CHL7EventADTA05 {

  /** @var string */
  public $code        = "A28";

  /** @var string */
  public $struct_code = "A05";

  /**
   * Build A28 event
   *
   * @param CPatient $patient Person
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($patient) {
    parent::build($patient);
    
    // Patient Identification
    $this->addPID($patient);
    
    // Patient Additional Demographic
    $this->addPD1($patient);

    if ($this->version > "2.3.1") {
      // Doctors
      $this->addROLs($patient);
    }
    
    // Next of Kin / Associated Parties
    $this->addNK1s($patient);

    // Build specific segments (i18n)
    $this->buildI18nSegments($patient);
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
  }
}