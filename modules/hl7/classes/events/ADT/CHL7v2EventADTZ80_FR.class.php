<?php

/**
 * Z80 - Change of medical responsibility - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2EventADTZ80
 * Z80 - Change of medical responsibility
 */
class CHL7v2EventADTZ80 extends CHL7v2EventADT implements CHL7EventADTA01 {
  var $code        = "Z80";
  var $struct_code = "A01";
  
  function __construct($i18n = null) {
    parent::__construct($i18n);
  }
  
  function getEVNOccuredDateTime(CSejour $sejour) {
    return $sejour->entree_reelle;
  }
  
  /**
   * @see parent::build()
   */
  function build($sejour) {
    parent::build($sejour);
    
    $patient = $sejour->_ref_patient;
    // Patient Identification
    $this->addPID($patient, $sejour);
    
    // Patient Additional Demographic
    $this->addPD1($patient);
    
    // Doctors
    $this->addROLs($patient);
    
    // Next of Kin / Associated Parties
    $this->addNK1s($patient);
    
    // Patient Visit
    $this->addPV1($sejour);
    
    // Patient Visit - Additionale Info
    $this->addPV2($sejour);
    
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
    
    // Guarantor
    $this->addGT1($patient);
  }
}

?>