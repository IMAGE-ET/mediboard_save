<?php /* $Id:$ */

/**
 * Message XML HL7
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2MessageXML 
 * Message XML HL7
 */

class CHL7v2MessageXML extends CMbXMLDocument implements CHL7MessageXML {
  var $_ref_exchange_ihe     = null;
  
  function __construct() {
    parent::__construct("utf-8");

    $this->formatOutput = true;
  }
  
  function addNameSpaces($name) {
    // Ajout des namespace pour XML Spy
    $this->addAttribute($this->documentElement, "xmlns", "urn:hl7-org:v2xml");
    $this->addAttribute($this->documentElement, "xmlns:xsi", "http://www.w3.org/2001/XMLSchema-instance");
    $this->addAttribute($this->documentElement, "xsi:schemaLocation", "urn:hl7-org:v2xml $name.xsd");
  }
  
  function addElement($elParent, $elName, $elValue = null, $elNS = "urn:hl7-org:v2xml") {
    return parent::addElement($elParent, $elName, $elValue, $elNS);
  }
  
  function getMSHEvenementXML() {
    $data = array();
    
    $xpath = new CHL7v2MessageXPath($this);   
    $MSH = $xpath->queryUniqueNode("//MSH");
    
    $data['dateHeureProduction'] = mbDateTime($xpath->queryTextNode("MSH.7/TS.1", $MSH));
    $data['identifiantMessage']  = $xpath->queryTextNode("MSH.10", $MSH);
    
    return $data;
  }
  
  function getContentsXML() {
    $data = array();

    $xpath = new CHL7v2MessageXPath($this);
    
    $data["PID"] = $PID = $xpath->queryUniqueNode("//PID");
    
    $data["patientIdentifiers"] = $this->getPatientIdentifiers($PID);
    
    $data["PD1"] = $PD1 = $xpath->queryUniqueNode("//PD1");
    
    $data["NK1"] = $NK1 = $xpath->query("//NK1");
    
    $data["ROL"] = $NK1 = $xpath->query("//ROL");
    
    return $data;
  }
  
  function getPatientIdentifiers(DOMNode $node) {
    $data = array();
    
    $xpath = new CHL7v2MessageXPath($this);
    // PID/PID.3
    foreach ($xpath->query("PID.3", $node) as $_PID3) {
      // RI - Resource identifier 
      if (($xpath->queryTextNode("CX.5", $_PID3) == "RI") && 
          ($xpath->queryTextNode("CX.4/HD.2", $_PID3) == CAppUI::conf("hl7 assigningAuthorityUniversalID"))) {
        $data["RI"] = $xpath->queryTextNode("CX.1", $_PID3);
      }
      // PI - Patient internal identifier
      if ($xpath->queryTextNode("CX.5", $_PID3) == "PI") {
        $data["PI"] = $xpath->queryTextNode("CX.1", $_PID3);
      }
      // INS-C - Identifiant national de sant� calcul�
      if ($xpath->queryTextNode("CX.5", $_PID3) == "INS-C") {
        $data["INSC"] = $xpath->queryTextNode("CX.1", $_PID3);
      }
    }
   
    return $data;
  }
 
  function recordPerson(CHL7Acknowledgment $ack, CPatient $newPatient, $data) {
    // Traitement du message des erreurs
    $comment = $warning = "";
    $_modif_patient = false;
    
    $exchange_ihe = $this->_ref_exchange_ihe;
    $exchange_ihe->_ref_sender->loadConfigValues();
    $sender       = $exchange_ihe->_ref_sender;
    
    $patientRI = $patientPI = null;
    foreach ($data['patientIdentifiers'] as $identifier_type => $_patient_identifier) {
      switch ($identifier_type) {
        case "RI":
          $patientRI = $_patient_identifier;  
          break;
        case "PI":
          $patientPI = $_patient_identifier;  
          break;
      }
    }
    
    // Acquittement d'erreur : identifiants RI et PI non fournis
    if (!$patientRI && !$patientPI) {
      return $exchange_ihe->setAckAR($ack, "E003", null, $newPatient);
    }
        
    $IPP = CIdSante400::getMatch("CPatient", $sender->_tag_patient, $patientPI);

    // PI non connu
    if (!$IPP->_id) {
      // RI fourni
      if ($patientRI) {
        // Recherche du patient par son RI
        if ($newPatient->load($patientRI)) {
          // Mapping du patient
          $newPatient = $this->mappingPatient($data, $newPatient);
          
          // Notifier les autres destinataires autre que le sender
          $newPatient->_eai_initiateur_group_id = $sender->group_id;
          if ($msgPatient = $newPatient->store()) {
            return $exchange_ihe->setAckAR($ack, "E004", $msgPatient, $newPatient);
          }
                    
          $code_IPP      = "I021";
          $_modif_patient = true; 
        } 
        // Patient non retrouv� par son RI
        else {
          $code_IPP = "I020";
        }
      } else {
        $code_IPP = "I022";
      }      
      
      if (!$newPatient->_id) {
        // Mapping du patient
        $newPatient = $this->mappingPatient($data, $newPatient);
        
        // Patient retrouv�
        if ($newPatient->loadMatchingPatient()) {
          // Mapping du patient
          $newPatient = $this->mappingPatient($data, $newPatient);
                
          $code_IPP      = "A021";
          $_modif_patient = true; 
        }
        
        // Notifier les autres destinataires autre que le sender
        $newPatient->_eai_initiateur_group_id = $sender->group_id;
        if ($msgPatient = $newPatient->store()) {
          return $exchange_ihe->setAckAR($ack, "E004", $msgPatient, $newPatient);
        }
      }

      if ($msgIPP = CEAIPatient::storeIPP($IPP, $newPatient)) {
        return $exchange_ihe->setAckAR($ack, "E005", $msgIPP, $newPatient);
      }
      
      $codes = array (($_modif_patient ? "I002" : "I001"), $code_IPP);
      
      $comment  = CEAIPatient::getComment($newPatient);
      $comment .= CEAIPatient::getComment($IPP);
    } 
    // PI connu
    else {
      $newPatient->load($IPP->object_id);
      
      // Mapping du patient
      $newPatient = $this->mappingPatient($data, $newPatient);
                      
      // RI non fourni
      if (!$patientRI) {
        $code_IPP = "I023"; 
      } else {
        $tmpPatient = new CPatient();
        // RI connu
        if ($tmpPatient->load($patientRI)) {
          if ($tmpPatient->_id != $IPP->object_id) {
            $comment = "L'identifiant source fait r�f�rence au patient : $IPP->object_id et l'identifiant cible au patient : $tmpPatient->_id.";
            return $exchange_ihe->setAckAR($ack, "E004", $comment, $newPatient);
          }
          $code_IPP = "I024"; 
        }
        // RI non connu
        else {
          $code_IPP = "A020";
        }
      }
      
      // Notifier les autres destinataires autre que le sender
      $newPatient->_eai_initiateur_group_id = $sender->group_id;
      if ($msgPatient = $newPatient->store()) {
        return $exchange_ihe->setAckAR($ack, "E004", $msgPatient, $newPatient);
      }
            
      $codes = array ("I002", $code_IPP);
      
      $comment = CEAIPatient::getComment($newPatient);
    }
    
    return $exchange_ihe->setAck($ack, $codes, $comment, $newPatient);
  }    
  
  function mappingPatient($data, CPatient $newPatient) {
    //$mbPatient = $this->getPID($node, $newPatient);
    //$mbPatient = $this->getActiviteSocioProfessionnelle($node, $newPatient);
    //$mbPatient = $this->getPersonnesPrevenir($node, $mbPatient);
    
    return $newPatient;
  }
}

?>