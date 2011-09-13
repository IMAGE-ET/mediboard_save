<?php

/**
 * Represents an HL7 ROL message segment (Role) - HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2SegmentROL 
 * ROL - Represents an HL7 ROL message segment (Role)
 */

class CHL7v2SegmentROL extends CHL7v2Segment {
  var $medecin = null;
  var $action  = "UC";
  var $role_id = null;
  
  function build(CHL7v2Event $event) {
    parent::build($event, "ROL");
        
    $medecin  = $this->medecin;

    $data = array();
        
    // ROL-1: Role Instance ID (EI) (optional)
    // The field is optional when used in ADT and Finance messages
    $data[] = null;
    
    // ROL-2: Action Code (ID)
    // Table - 0287
    // AD - ADD - Nouveau r�le du m�decin
    // DE - DELETE - Suppression du r�le du m�decin
    // UC - UNCHANGED - Notification du m�decin � prendre en compte pour le r�le d�fini dans le contexte courant
    // UP - UPDATE - Mise � jour du r�le du m�decin
    $data[] = $this->action;
     
    // ROL-3: Role-ROL (CE)
    // Table - 0443
    // AD   - Admitting - PV1.17 M�decin de la structure qui d�cide d'hospitaliser
    // AT   - Attending - PV1-7 M�decin responsable du patient pendant le s�jour
    // CP   - Consulting Provider - M�decin consult� pour 2�me avis dans le cadre de la venue
    // FHCP - Family Health Care Professional - M�decin de famille. Utilis� dans les rares cas o� il est distinct du m�decin traitant
    // RP   - Referring Provider - PV1-8 M�decin adressant 
    // RT   - Referred to Provider - M�decin correspondant
    // ODRP - Officialy Declared Referring Physician - M�decin Traitant
    // SUBS - Substitute - Rempla�ant du m�decin traitant
    $data[] = array( 
      array (
        $this->role_id,
        null,
        null,
        null,
        null,
        null
      )
    );
     
    // ROL-4: Role Person (XCN) (repeating)
    $data[] = array(
      array (
        // XCN-1
        CValue::first($medecin->rpps, $medecin->adeli, $medecin->_id),
        // XCN-2
        $medecin->nom,
        // XCN-3
        $medecin->prenom,
        // XCN-4
        null,
        // XCN-5
        null,
        // XCN-6
        null,
        // XCN-7
        null,
        // XCN-8
        null,
        // XCN-9
        // Autorit� d'affectation
        $this->getAssigningAuthority($medecin->rpps ? "RPPS" : ($medecin->adeli ? "ADELI" : "mediboard")),
        // XCN-10
        // Table - 0200
        // L - Legal Name - Nom de famille
        "L",
        // XCN-11
        null,
        // XCN-12
        null,
        // XCN-13
        // Table - 0203
        // ADELI - Num�ro au r�pertoire ADELI du professionnel de sant�
        // RPPS  - N� d'inscription au RPPS du professionnel de sant� 
        // RI    - Ressource interne
        $medecin->rpps ? "RPPS" : ($medecin->adeli ? "ADELI" : "RI"),
        // XCN-14
        null,
        // XCN-15
        null,
        // XCN-16
        null,
        // XCN-17
        null,
        // XCN-18
        null,
        // XCN-19
        null,
        // XCN-20
        null,
        // XCN-21
        null,
        // XCN-22
        null,
        // XCN-23
        null,
      )
    );
    
    // ROL-5: Role Begin Date/Time (TS) (optional)
    $data[] = null;
    
    // ROL-6: Role End Date/Time (TS) (optional)
    $data[] = null;
    
    // ROL-7: Role Duration (CE) (optional)
    $data[] = null;
    
    // ROL-8: Role Action Reason (CE) (optional)
    $data[] = null;
    
    // ROL-9: Provider Type (CE) (optional repeating)
    $data[] = null;
    
    // ROL-10: Organization Unit Type (CE) (optional)
    $data[] = null;
    
    // ROL-11: Office/Home Address/Birthplace (XAD) (optional repeating)
    $data[] = null;
    
    // ROL-12: Phone (XTN) (optional repeating)
    $data[] = null;
    
    $this->fill($data);
  }
}

?>