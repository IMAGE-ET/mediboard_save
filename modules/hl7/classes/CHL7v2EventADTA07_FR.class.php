<?php

/**
 * A07 - Change an inpatient to an outpatient - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTA07_FR
 * A07 - Change an inpatient to an outpatient
 */
class CHL7v2EventADTA07_FR extends CHL7v2EventADTA07 {
  function __construct($i18n = null) {
    parent::__construct($i18n);
        
    $this->transaction = CPAMFR::getTransaction($this->code);
  }
  
  function build($sejour) {
    parent::build($sejour);
    
    // Movement segment
    $this->addZBE($sejour);
  }
  
}

?>