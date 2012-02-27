<?php

/**
 * A02 - Transfer a patient - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA02_FR
 * A02 - Transfer a patient
 */
class CHL7v2EventADTA02_FR extends CHL7v2EventADTA02 {
  function __construct($i18n = "FR") {
    parent::__construct($i18n);
  }
  
  /**
   * @see parent::build()
   */
  function build($sejour) {
    parent::build($sejour);
    
    // Movement segment
    $this->addZBE($sejour);
    
    // Compl�ments sur la rencontre
    $this->addZFV($sejour);
  }
  
}

?>