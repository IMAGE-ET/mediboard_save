<?php /* $Id:$ */

/**
 * Record person, message XML
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2RecordPerson 
 * Record person, message XML HL7
 */

class CHL7v2RecordPerson extends CHL7v2MessageXML {
  function getContentNodes() {
    $data = parent::getContentNodes();
    
    $this->queryNodes("NK1", null, $data);
    
    $this->queryNodes("ROL", null, $data);
    
    return $data;
  }
 
  function handle(CHL7Acknowledgment $ack, CPatient $newPatient, $data) {
    // Traitement du message des erreurs
    $comment = $warning = "";
    $_modif_patient = false;
    
    $exchange_ihe = $this->_ref_exchange_ihe;
    $exchange_ihe->_ref_sender->loadConfigValues();
    $sender       = $exchange_ihe->_ref_sender;
    
    $patientRI = CValue::read($data['patientIdentifiers'], "RI");
    $patientPI = CValue::read($data['patientIdentifiers'], "PI");

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
          $this->mappingPatient($data, $newPatient);
          
          // Notifier les autres destinataires autre que le sender
          $newPatient->_eai_initiateur_group_id = $sender->group_id;
          if ($msgPatient = $newPatient->store()) {
            return $exchange_ihe->setAckAR($ack, "E101", $msgPatient, $newPatient);
          }
                    
          $code_IPP      = "I121";
          $_modif_patient = true; 
        } 
        // Patient non retrouv� par son RI
        else {
          $code_IPP = "I120";
        }
      } else {
        $code_IPP = "I122";
      }      
      
      if (!$newPatient->_id) {
        // Mapping du patient
        $this->mappingPatient($data, $newPatient);
        
        // Patient retrouv�
        if ($newPatient->loadMatchingPatient()) {
          // Mapping du patient
          $this->mappingPatient($data, $newPatient);
                
          $code_IPP      = "A121";
          $_modif_patient = true; 
        }
        
        // Notifier les autres destinataires autre que le sender
        $newPatient->_eai_initiateur_group_id = $sender->group_id;
        if ($msgPatient = $newPatient->store()) {
          return $exchange_ihe->setAckAR($ack, "E101", $msgPatient, $newPatient);
        }
      }

      if ($msgIPP = CEAIPatient::storeIPP($IPP, $newPatient)) {
        return $exchange_ihe->setAckAR($ack, "E102", $msgIPP, $newPatient);
      }
      
      $codes = array (($_modif_patient ? "I102" : "I101"), $code_IPP);
      
      $comment  = CEAIPatient::getComment($newPatient);
      $comment .= CEAIPatient::getComment($IPP);
    } 
    // PI connu
    else {
      $newPatient->load($IPP->object_id);
      
      // Mapping du patient
      $this->mappingPatient($data, $newPatient);
                      
      // RI non fourni
      if (!$patientRI) {
        $code_IPP = "I123"; 
      } else {
        $tmpPatient = new CPatient();
        // RI connu
        if ($tmpPatient->load($patientRI)) {
          if ($tmpPatient->_id != $IPP->object_id) {
            $comment = "L'identifiant source fait r�f�rence au patient : $IPP->object_id et l'identifiant cible au patient : $tmpPatient->_id.";
            return $exchange_ihe->setAckAR($ack, "E100", $comment, $newPatient);
          }
          $code_IPP = "I124"; 
        }
        // RI non connu
        else {
          $code_IPP = "A120";
        }
      }
      
      // Notifier les autres destinataires autre que le sender
      $newPatient->_eai_initiateur_group_id = $sender->group_id;
      if ($msgPatient = $newPatient->store()) {
        return $exchange_ihe->setAckAR($ack, "E101", $msgPatient, $newPatient);
      }
            
      $codes = array ("I102", $code_IPP);
      
      $comment = CEAIPatient::getComment($newPatient);
    }
    
    return $exchange_ihe->setAckAA($ack, $codes, $comment, $newPatient);
  }    
  
  function mappingPatient($data, CPatient $newPatient) {
    // Mapping de l'INS-C
    if (array_key_exists("INSC", $data["patientIdentifiers"])) {
      $newPatient->INSC = $data["patientIdentifiers"]["INSC"];
    }
    
    // Segment PID
    $this->getPID($data["PID"], $newPatient);
    
    // Segment PD1
    $this->getPD1($data["PD1"], $newPatient);
    
    // Correspondants m�dicaux
    if ($newPatient->_id && array_key_exists("ROL", $data)) {
      foreach ($data["ROL"] as $_ROL) {
        $this->getROL($_ROL, $newPatient);
      }
    }
    
    // Correspondances
    // Possible ssi le patient est d�j� enregistr�
    if ($newPatient->_id && array_key_exists("NK1", $data)) {
      foreach ($data["NK1"] as $_NK1) {
        $this->getNK1($_NK1, $newPatient);
      }
    }
  }
  
  function getPID(DOMNode $node, CPatient $newPatient) {    
    $PID5 = $this->query("PID.5", $node);
    foreach ($PID5 as $_PID5) {
      // Nom(s)
      $this->getNames($_PID5, $newPatient, $PID5);
      
      // Prenom(s)
      $this->getFirstNames($_PID5, $newPatient);
      
      // Civilit�
      $newPatient->civilite = $this->queryTextNode("XPN.5", $_PID5);
    }
    
    // Date de naissance
    $newPatient->naissance = mbDate($this->queryTextNode("PID.7/TS.1", $node));
    
    // Sexe
    $newPatient->sexe = CHL7v2TableEntry::mapFrom("1", $this->queryTextNode("PID.8", $node));
    
    // Adresse(s)
    $this->getAdresses($node, $newPatient);
    
    // T�l�phones
    $this->getPhones($node, $newPatient);
    
    //NSS
    $newPatient->matricule = $this->queryTextNode("PID.19", $node);
  }
  
  function getNames(DOMNode $node, CPatient $newPatient, DOMNodeList $PID5) {    
    if ($this->queryTextNode("XPN.7", $node) == "D") {
      $newPatient->nom = $this->queryTextNode("XPN.1/FN.1", $node);
    }
    if ($this->queryTextNode("XPN.7", $node) == "L") {
      // Dans le cas o� l'on a pas de nom de nom de naissance le legal name
      // est le nom du patient
      if ($PID5->length > 1) {
        $newPatient->nom_jeune_fille = $this->queryTextNode("XPN.1/FN.1", $node);
      } 
      else {
        $newPatient->nom = $this->queryTextNode("XPN.1/FN.1", $node);
      }
    }
  }
  
  function getFirstNames(DOMNode $node, CPatient $newPatient) {    
    $newPatient->prenom = $this->queryTextNode("XPN.2", $node);
    $first_names = explode(",", $this->queryTextNode("XPN.3", $node));
    $newPatient->prenom_2 = CValue::read($first_names, 1);
    $newPatient->prenom_3 = CValue::read($first_names, 2);
    $newPatient->prenom_4 = CValue::read($first_names, 3);
  }
  
  function getAdresses(DOMNode $node, CPatient $newPatient) {    
    $PID11 = $this->query("PID.11", $node);
    $addresses = array();
    foreach ($PID11 as $_PID11) {
      $adress_type = $this->queryTextNode("XAD.7", $_PID11);
      /* @todo Ajouter la gestion des multi-lignes - SAD.2 */
      $addresses[$adress_type]["adresse"]    = $this->queryTextNode("XAD.1", $_PID11);
      $addresses[$adress_type]["ville"]      = $this->queryTextNode("XAD.3", $_PID11);
      $addresses[$adress_type]["cp"]         = $this->queryTextNode("XAD.5", $_PID11);
      $addresses[$adress_type]["pays_insee"] = $this->queryTextNode("XAD.6", $_PID11);
    }
    // Adresse  naissance
    if (array_key_exists("BR", $addresses)) {
      $newPatient->lieu_naissance       = CValue::read($addresses["BR"], "ville");
      $newPatient->cp_naissance         = CValue::read($addresses["BR"], "cp");
      $newPatient->pays_naissance_insee = CValue::read($addresses["BR"], "pays_insee");
      
      unset($addresses["BR"]);
    }
    // Adresse
    if (array_key_exists("H", $addresses)) {
      $this->getAdress($addresses["H"], $newPatient);
    } else {
      foreach ($addresses as $adress_type => $_address) {
        $this->getAdress($_address, $newPatient);
      }
    }
  }
  
  function getAdress($adress, CPatient $newPatient) {    
    $newPatient->adresse    = $adress["adresse"];
    $newPatient->ville      = $adress["ville"];
    $newPatient->cp         = $adress["cp"];
    $newPatient->pays_insee = $adress["pays_insee"];
  }
  
  function getPhones(DOMNode $node, CPatient $newPatient) {
    $PID13 = $this->query("PID.13", $node);
    $phones = array();
    foreach ($PID13 as $_PID13) {
      $tel_number = $this->queryTextNode("XTN.1", $_PID13);
      switch ($this->queryTextNode("XTN.2", $_PID13)) {
        case "PRN" :
          $newPatient->tel  = $tel_number;
          break;
        case "ORN" :
          $newPatient->tel2 = $tel_number;
          break;
        default :
          $newPatient->tel_autre = $tel_number;
          break;
      }
    }
  }
  
  function getPD1(DOMNode $node, CPatient $newPatient) {    
    // VIP ?
    $newPatient->vip = ($this->queryTextNode("PD1.12", $node) == "Y") ? 1 : 0; 
  }
  
  function getROL(DOMNode $node, CPatient $newPatient) {
    switch ($this->queryTextNode("ROL.3/CE.1", $node)) {
      // M�decin traitant
      case "ODRP" :
        $newPatient->medecin_traitant = $this->getMedecin($this->queryNode("ROL.4", $node));
        break;
      case "RT" : 
        $correspondant = new CCorrespondant();
        $correspondant->patient_id = $newPatient->_id;
        $correspondant->medecin_id = $this->getMedecin($this->queryNode("ROL.4", $node));
        if (!$correspondant->loadMatchingObject()) {
          $correspondant->store();
        } 
        break;
    }
  }
  
  function getNK1(DOMNode $node, CPatient $newPatient) {    
    /* @todo voir quand on aura les tables de correspondances */
  }
  
  function getMedecin(DOMNode $node) {    
    $xcn1  = $this->queryTextNode("XCN.1", $node);
    $xcn2  = $this->queryTextNode("XCN.2/FN.1", $node);
    $xcn3  = $this->queryTextNode("XCN.3", $node);
      
    $medecin = new CMedecin();
    
    switch ($this->queryTextNode("XCN.13", $node)) {
      case "RPPS" :
        $medecin->rpps  = $xcn1;
        $medecin->loadMatchingObject();
        break;
      case "ADELI" :
        $medecin->adeli = $xcn1;
        $medecin->loadMatchingObject();
        break;
      case "RI" :
        // Gestion de l'identifiant MB
        if ($this->queryTextNode("XCN.9/CX.4/HD.2", $node) == CAppUI::conf("hl7 assigningAuthorityUniversalID")) {
          $medecin->load($xcn1);
        }
        /* @todo Gestion des id externes */
        break;
    }
    
    // Si pas retrouv� par son identifiant
    if (!$medecin->_id) {
      $medecin->nom    = $xcn2;
      $medecin->prenom = $xcn3;
      $medecin->loadMatchingObject();
      
      // Dans le cas o� il n'est pas connu dans MB on le cr��
      $medecin->store();
    }
    
    return $medecin->_id;
  }
}

?>