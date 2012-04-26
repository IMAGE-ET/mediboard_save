<?php

/**
 * Z99 - Change admit - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTZ99_FR
 * Z99 - Change admit
 */
class CHL7v2EventADTZ99_FR  extends CHL7v2EventADTZ99 {
  function __construct($i18n = "FR") {
    parent::__construct($i18n);
  }
  
  /**
   * @see parent::buildI18nSegments()
   */
  function buildI18nSegments($sejour) {

    // Movement segment
    $this->addZBE($sejour);
    
    // Situation professionnelle
    // Si A01, A04, A05, A14
    $this->addZFP($sejour);
    
    // Compl�ments sur la rencontre
    // Si A01, A02, A03, A04, A05, A14, A21
    $this->addZFV($sejour);
    
    // Mouvement PMSI
    // Si A01, A02, A03, A04, A05, A14, 
    // Z80, Z81, Z82, Z83, Z84, Z85, Z86, Z87 
    $this->addZFM($sejour);
    
    // Compl�ment d�mographique
    // Si A01, A04, A05, A14
    $this->addZFD($sejour);
  }
}

?>