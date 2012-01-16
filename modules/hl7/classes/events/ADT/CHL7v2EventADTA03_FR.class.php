<?php

/**
 * A03 - Discharge/end visit - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA03_FR
 * A03 - Discharge/end visit
 */
class CHL7v2EventADTA03_FR extends CHL7v2EventADTA03 {
  function __construct($i18n = "FR") {
    parent::__construct($i18n);

    $this->transaction = CPAMFR::getTransaction($this->code);
  }
  
  function build($sejour) {
    parent::build($sejour);

    // Movement segment
    $this->addZBE($sejour);
    
    // Compl�ments sur la rencontre
    $this->addZFV($sejour);
  }
  
}

?>