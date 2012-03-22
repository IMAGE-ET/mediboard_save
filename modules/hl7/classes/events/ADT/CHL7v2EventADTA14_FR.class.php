<?php

/**
 * A14 - Pending Admit - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA14_FR
 * A14 - Pending Admit
 */
class CHL7v2EventADTA14_FR extends CHL7v2EventADTA14 {
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
    $this->addZFP($sejour);
    
    // Compléments sur la rencontre
    $this->addZFV($sejour);
    
    // Mouvement PMSI
    $this->addZFM($sejour);
    
    // Complément démographique
    $this->addZFD($sejour);
  }
}

?>