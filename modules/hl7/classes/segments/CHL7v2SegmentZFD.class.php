<?php

/**
 * Represents an HL7 ZFD message segment (Compl�ment d�mographique) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentZFD
 * ZFD - Represents an HL7 ZFD message segment (Compl�ment d�mographique)
 */

class CHL7v2SegmentZFD extends CHL7v2Segment {

  /** @var string */
  public $name   = "ZFD";
  

  /** @var CPatient */
  public $patient;

  /**
   * Build ZFD segement
   *
   * @param CHL7v2Event $event Event
   *
   * @return null
   */
  function build(CHL7v2Event $event) {
    parent::build($event);
    
    $patient = $this->patient;
    
     // ZFD-1: Date lunaire
    if (CMbDT::isLunarDate($patient->naissance)) {
      $date = explode("-", $patient->naissance);
      $data[] = array(
        // ZFD-1.1 : Jour
        $date[2],
        // ZFD-1.2 : Mois
        $date[1],
        // ZFD-1.1 : Ann�e
        $date[0]
      );
    }
    else {
      $data[] = null;
    }
    
    // ZFD-2: Nombre de semaines de gestation
    $data[] = null;
    
    $this->fill($data);
  }
}