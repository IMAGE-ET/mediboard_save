<?php

/**
 * A04 - Register a patient - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA04_FR
 * A04 - Register a patient
 */
class CHL7v2EventADTA04_FR extends CHL7v2EventADTA04 {
  /**
   * Construct
   *
   * @param string $i18n i18n
   *
   * @return \CHL7v2EventADTA04_FR
   */
  function __construct($i18n = "FR") {
    parent::__construct($i18n);
  }

  /**
   * Build i18n segements
   *
   * @param CSejour $sejour Admit
   *
   * @see parent::buildI18nSegments()
   *
   * @return void
   */
  function buildI18nSegments($sejour) {
    // Movement segment
    $this->addZBE($sejour);
    
    // Situation professionnelle
    $this->addZFP($sejour);
    
    // Compl�ments sur la rencontre
    $this->addZFV($sejour);
    
    // Mouvement PMSI
    $this->addZFM($sejour);
    
    // Compl�ment d�mographique
    $this->addZFD($sejour);

    if ($this->_receiver->_configs["send_insurance"]) {
      // Insurance
      $this->addIN1($sejour->_ref_patient);

      // Insurance (Additional Information)
      $this->addIN2($sejour->_ref_patient);
    }
  }
}