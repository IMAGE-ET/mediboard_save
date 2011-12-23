<?php

/**
 * Represents an HL7 PID message segment (Patient Identification) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentPID 
 * PID - Represents an HL7 PID message segment (Patient Identification)
 */

class CHL7v2SegmentPID extends CHL7v2Segment {
  var $name    = "PID";
  var $set_id  = null;
  
  /**
   * @var CPatient
   */
  var $patient = null;
  
  /**
   * @var CSejour
   */
  var $sejour  = null;
  
  function build(CHL7v2Event $event) {
    parent::build($event);
    
    $message  = $event->message;
    $receiver = $event->_receiver;
    $group    = $receiver->_ref_group;
    
    $patient  = $this->patient;
    
    $data = array();
    // PID-1: Set ID - PID (SI) (optional)
    $data[] = $this->set_id;
    
    // PID-2: Patient ID (CX) (optional)
    $data[] = null;
    
    // PID-3: Patient Identifier List (CX) (repeating)
    $data[] = $this->getPersonIdentifiers($patient, $group);
    
    // PID-4: Alternate Patient ID - PID (CX) (optional repeating)
    $data[] = null;
    
    // PID-5: Patient Name (XPN) (repeating)
    $data[] = $this->getXPN($patient);
    
    // PID-6: Mother's Maiden Name (XPN) (optional repeating)
    $data[] = null;
    
    // PID-7: Date/Time of Birth (TS) (optional)
    $data[] = isLunarDate($patient->naissance) ? null : $patient->naissance;
    
    // PID-8: Administrative Sex (IS) (optional)
    // Table - 0001
    // F - Female
    // M - Male
    // O - Other
    // U - Unknown
    // A - Ambiguous  
    // N - Not applicable
    $data[] = CHL7v2TableEntry::mapTo("1", $patient->sexe);
    
    // PID-9: Patient Alias (XPN) (optional repeating)
    $data[] = null;
    
    // PID-10: Race (CE) (optional repeating)
    $data[] = null;
    
    // PID-11: Patient Address (XAD) (optional repeating)
    $address = array();
    
    $linesAdress = explode("\n", $patient->adresse, 2);
    $address[] = array(
      CValue::read($linesAdress, 0),
      str_replace("\n", $message->componentSeparator, CValue::read($linesAdress, 1)),
      $patient->ville,
      null,
      $patient->cp,
      $patient->pays_insee,
      // Table - 0190
      // B   - Firm/Business 
      // BA  - Bad address 
      // BDL - Birth delivery location (address where birth occurred)  
      // BR  - Residence at birth (home address at time of birth)  
      // C   - Current Or Temporary
      // F   - Country Of Origin 
      // H   - Home 
      // L   - Legal Address 
      // M   - Mailing
      // N   - Birth (nee) (birth address, not otherwise specified)  
      // O   - Office
      // P   - Permanent 
      // RH  - Registry home
      "H",
    );
    if ($patient->lieu_naissance || $patient->cp_naissance || $patient->pays_naissance_insee) {
      $address[] = array(
        null,
        null,
        $patient->lieu_naissance,
        null,
        $patient->cp_naissance,
        $patient->pays_naissance_insee,
        // Table - 0190
        // B   - Firm/Business 
        // BA  - Bad address 
        // BDL - Birth delivery location (address where birth occurred)  
        // BR  - Residence at birth (home address at time of birth)  
        // C   - Current Or Temporary
        // F   - Country Of Origin 
        // H   - Home 
        // L   - Legal Address 
        // M   - Mailing
        // N   - Birth (nee) (birth address, not otherwise specified)  
        // O   - Office
        // P   - Permanent 
        // RH  - Registry home
        "BDL",
      );
    }
    $data[] = $address;
    
    // PID-12: County Code (IS) (optional)
    $data[] = null;
    
    // PID-13: Phone Number - Home (XTN) (optional repeating)
    $phones = array();
    if ($patient->tel) {
      $phones[] = array(
        $patient->tel,
        // Table - 0201
        // ASN - Answering Service Number
        // BPN - Beeper Number 
        // EMR - Emergency Number  
        // NET - Network (email) Address
        // ORN - Other Residence Number 
        // PRN - Primary Residence Number 
        // VHN - Vacation Home Number  
        // WPN - Work Number
        "PRN",
        // Table - 0202
        // BP       - Beeper  
        // CP       - Cellular Phone  
        // FX       - Fax 
        // Internet - Internet Address: Use Only If Telecommunication Use Code Is NET 
        // MD       - Modem 
        // PH       - Telephone  
        // TDD      - Telecommunications Device for the Deaf  
        // TTY      - Teletypewriter
        "PH"
      );
    }
    if ($patient->tel2) {
      $phones[] = array(
        $patient->tel2,
        // Table - 0201
        "ORN",
        // Table - 0202
        "CP"
      );
    }
    if ($patient->tel_autre) {
      $phones[] = array(
        $patient->tel_autre,
        // Table - 0201
        "ORN",
        // Table - 0202
        "PH"
      );
    }
    $data[] =  $phones;
    
    // PID-14: Phone Number - Business (XTN) (optional repeating)
    $data[] = null;
    
    // PID-15: Primary Language (CE) (optional)
    $data[] = null;
    
    // PID-16: Marital Status (CE) (optional)
    $data[] = null;
    
    // PID-17: Religion (CE) (optional)
    $data[] = null;
    
    // PID-18: Patient Account Number (CX) (optional)
    if ($this->sejour) {
      $sejour = $this->sejour;
      $sejour->loadNDA($group->_id);
      $sejour->_NDA ?
        $data[] = array( 
                  array(
                    $sejour->_NDA,
                    null,
                    null,
                    // PID-3-4 Autorit� d'affectation
                    $this->getAssigningAuthority("FINESS", $group->finess),
                    "AN")
                ) 
        : null;
    } else {
      $data[] = null;
    }
    
    // PID-19: SSN Number - Patient (ST) (optional)
    $data[] = $patient->matricule;
    
    // PID-20: Driver's License Number - Patient (DLN) (optional)
    $data[] = null;
    
    // PID-21: Mother's Identifier (CX) (optional repeating)
    $data[] = null;
    
    // PID-22: Ethnic Group (CE) (optional repeating)
    $data[] = null;
    
    // PID-23: Birth Place (ST) (optional)
    $data[] = null;
    
    // PID-24: Multiple Birth Indicator (ID) (optional)
    $data[] = null;
    
    // PID-25: Birth Order (NM) (optional)
    $data[] = null;
    
    // PID-26: Citizenship (CE) (optional repeating)
    $data[] = null;
    
    // PID-27: Veterans Military Status (CE) (optional)
    $data[] = null;
    
    // PID-28: Nationality (CE) (optional)
    $data[] = null;
    
    // PID-29: Patient Death Date and Time (TS) (optional)
    $data[] = ($patient->deces) ? $patient->deces : null;
    
    // PID-30: Patient Death Indicator (ID) (optional)
    $data[] = ($patient->deces) ? "Y" : "N";
    
    // PID-31: Identity Unknown Indicator (ID) (optional)
    $data[] = null;
    
    // PID-32: Identity Reliability Code (IS) (optional repeating)
    $data[] =  array (
      // Table - 0445
      // VIDE  - Identit� non encore qualifi�e
      // PROV  - Provisoire
      // VALI  - Valid�
      // DOUB  - Doublon ou esclave
      // DESA  - D�sactiv�
      // DPOT  - Doublon potentiel
      // DOUA  - Doublon av�r�
      // COLP  - Collision potentielle
      // COLV  - Collision valid�e
      // FILI  - Filiation
      // CACH  - Cach�e
      // ANOM  - Anonyme
      // IDVER - Identit� v�rifi�e par le patient
      // RECD  - Re�ue d'un autre domaine
      // IDRA  - Identit� rapproch�e dans un autre domaine
      // USUR  - Usurpation
      // HOMD  - Homonyme detect�
      // HOMA  - Homonyme av�r�
      is_numeric($patient->nom) ? "ANOM" : "VALI"
    );

    // PID-33: Last Update Date/Time (TS) (optional)
    $data[] =  $event->last_log->date;
    
    // PID-34: Last Update Facility (HD) (optional)
    $data[] = null;
    
    // PID-35: Species Code (CE) (optional)
    $data[] = null;
    
    // PID-36: Breed Code (CE) (optional)
    $data[] = null;
    
    // PID-37: Strain (ST) (optional)
    $data[] = null;
    
    // PID-38: Production Class Code (CE) (optional)
    $data[] = null;
    
    // PID-39: Tribal Citizenship (CWE) (optional repeating)
    $data[] = null;
          
    $this->fill($data);
  }
}
?>