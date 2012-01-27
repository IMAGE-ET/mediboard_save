<?php /* $Id:$ */

/**
 * Record admit, message XML
 *  
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v2RecordAdmit 
 * Record admit, message XML HL7
 */

class CHL7v2RecordAdmit extends CHL7v2MessageXML {
  function getContentNodes() {
    $data  = parent::getContentNodes();

    $sender = $this->_ref_sender;
    
    $this->queryNodes("NK1", null, $data, true);
    
    $this->queryNodes("ROL", null, $data, true);    
    
    $PV1 = $this->queryNode("PV1", null, $data, true);

    $data["admitIdentifiers"] = $this->getAdmitIdentifiers($PV1, $sender);

    $this->queryNode("PV2", null, $data, true);
    
    $this->queryNode("ZBE", null, $data, true);
    
    $this->queryNode("ZFP", null, $data, true);
    
    $this->queryNode("ZFV", null, $data, true);
    
    $this->queryNode("ZFM", null, $data, true);
    
    $this->queryNode("ZFD", null, $data, true);
    
    return $data;
  }
  
  function handle(CHL7Acknowledgment $ack, CPatient $newPatient, $data) {
    $event_temp = $ack->event;

    $exchange_ihe = $this->_ref_exchange_ihe;
    $sender       = $this->_ref_sender;
  
    // Acquittement d'erreur : identifiants RI et NA non fournis
    if (!$data['admitIdentifiers']) {
      return $exchange_ihe->setAckAR($ack, "E200", null, $newPatient);
    }

    // Traitement du patient
    $hl7v2_record_person = new CHL7v2RecordPerson();
    $hl7v2_record_person->_ref_exchange_ihe = $exchange_ihe;
    $msg_ack = $hl7v2_record_person->handle($ack, $newPatient, $data);
    
    // Retour de l'acquittement si erreur sur le traitement du patient
    if ($exchange_ihe->statut_acquittement == "AR") {
      return $msg_ack;
    }
        
    // Traitement du s�jour
    $ack = new CHL7v2Acknowledgment($event_temp);
    $ack->message_control_id = $data['identifiantMessage'];
    $ack->_ref_exchange_ihe  = $exchange_ihe;
   
    $newVenue = new CSejour();
    
    // Affectation du patient
    $newVenue->patient_id = $newPatient->_id; 
    // Affectation de l'�tablissement
    $newVenue->group_id = $sender->group_id;
    
    $function_handle = "handle$exchange_ihe->code";
    if (!method_exists($this, $function_handle)) {
      return $exchange_ihe->setAckAR($ack, "E006", null, $newVenue);
    }
    
    return $this->$function_handle($ack, $newVenue, $data); 
  } 
 
  function handleA01(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation possible
    
    return $this->handleA05($ack, $newVenue, $data);
  }
  
  function handleA02(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    // R�cup�rer donn�es de la mutation
    return $this->mappingAndStoreVenue($data, $newVenue);
  }
  
  function handleA03(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    // R�cup�rer donn�es de la sortie
    return $this->mappingAndStoreVenue($data, $newVenue);
  }
  
  function handleA04(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation possible
    return $this->handleA05($ack, $newVenue, $data);
  }
  
  function handleA05(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation possible
    
    // Traitement du message des erreurs
    $comment = $warning = "";
    $_modif_sejour = false; 
    
    $exchange_ihe = $this->_ref_exchange_ihe;
    $sender       = $this->_ref_sender;
    
    $venueRI       = CValue::read($data['admitIdentifiers'], "RI");
    $venueRISender = CValue::read($data['admitIdentifiers'], "RI_Sender");
    $venueNPA      = CValue::read($data['admitIdentifiers'], "NPA");
    $venueAN       = CValue::read($data['personIdentifiers'], "AN");
    
    $NDA = new CIdSante400();
    if ($venueAN) {
      $NDA = CIdSante400::getMatch("CSejour", $sender->_tag_sejour, $venueAN);
    }
    
    // NDA non connu (non fourni ou non retrouv�)
    if (!$venueAN || !$NDA->_id) {
      // NPA fourni
      if ($venueNPA) {
        /* @todo G�rer ce cas */
      }
      
      // RI fourni
      if ($venueRI) {
        // Recherche du s�jour par son RI
        if ($newVenue->load($venueRI)) {
          $recoveredSejour = clone $newVenue;
          
          // Mapping du s�jour
          $this->mappingVenue($data, $newVenue);
          
          // Le s�jour retrouv� est-il diff�rent que celui du message ?
          /* @todo voir comment faire (m�me patient, m�me praticien, m�me date ?) */
          
          // Notifier les autres destinataires autre que le sender
          $newVenue->_eai_initiateur_group_id = $sender->group_id;
          if ($msgVenue = $newVenue->store()) {
            return $exchange_ihe->setAckAR($ack, "E201", $msgPatient, $newVenue);
          }
                    
          $code_NDA      = "I221";
          $_modif_sejour = true; 
        }
        // S�jour non retrouv� par son RI
        else {
          $code_NDA = "I220";
        }  
      }
      else {
        // Aucun NDA fourni
        if (!$venueAN) {
          $code_NDA = "I225";
        } 
        // Association de l'IPP
        else {
          $code_NDA = "I222";
        }  
      }
      
      if (!$newVenue->_id) {
        // Mapping du patient
        $this->mappingVenue($data, $newVenue);
      
        // S�jour retrouv� ?
        if (CAppUI::conf("hl7 strictSejourMatch")) {
          // Recherche d'un num dossier d�j� existant pour cette venue 
          if ($newVenue->loadMatchingSejour(null, true)) {                 
            $code_NDA     = "A221";
            $_modif_sejour = true;
          }
        } else {
          $collision = $newVenue->getCollisions();

          if (count($collision) == 1) {
            $newVenue = reset($collision);
            
            $code_NDA     = "A222";
            $_modif_sejour = true;
          }
        }
        
        // Mapping du s�jour
        $newVenue = $this->mappingVenue($data, $newVenue);
        
        // Notifier les autres destinataires autre que le sender
        $newVenue->_eai_initiateur_group_id = $sender->group_id;
        if ($msgVenue = $newVenue->store()) {
          return $exchange_ihe->setAckAR($ack, "E201", $msgVenue, $newVenue);
        }
      }
      
      if ($msgNDA = CEAISejour::storeNDA($NDA, $newVenue, $sender)) {
        return $exchange_ihe->setAckAR($ack, "E202", $msgNDA, $newVenue);
      }
      
      $codes = array (($_modif_sejour ? "I202" : "I201"), $code_NDA);
      
      $comment  = CEAISejour::getComment($newVenue);
      $comment .= CEAISejour::getComment($NDA);
    }
    // NDA connu
    else {
      $newVenue->load($NDA->object_id);
      
      $recoveredVenue = clone $newVenue;
          
      // Mapping de la venue
      $this->mappingVenue($data, $newVenue);
      
      // RI non fourni
      if (!$venueRI) {
        $code_IPP = "I223"; 
      } else {
        $tmpVenue = new CSejour();
        // RI connu
        if ($tmpVenue->load($venueRI)) {
          if ($tmpVenue->_id != $NDA->object_id) {
            $comment = "L'identifiant source fait r�f�rence au s�jour : $NDA->object_id et l'identifiant cible au s�jour : $tmpVenue->_id.";
            return $exchange_ihe->setAckAR($ack, "E201", $comment, $newVenue);
          }
          $code_NDA = "I224"; 
        }
        // RI non connu
        else {
          $code_NDA = "A220";
        }
      }
      
      // Notifier les autres destinataires autre que le sender
      $newVenue->_eai_initiateur_group_id = $sender->group_id;
      if ($msgVenue = $newVenue->store()) {
        return $exchange_ihe->setAckAR($ack, "E201", $msgVenue, $newVenue);
      }
            
      $codes = array ("I202", $code_NDA);
      
      $comment = CEAISejour::getComment($newVenue);
    }
    
    return $exchange_ihe->setAckAA($ack, $codes, $comment, $newVenue);
  }
  
  function handleA06(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mappingAndStoreVenue($data, $newVenue);
  }
  
  function handleA07(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mappingAndStoreVenue($data, $newVenue);
  }
  
  function handleA08(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mappingAndStoreVenue($data, $newVenue);
  }
  
  function handleA11(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mappingAndStoreVenue($data, $newVenue);
  }
  
  function handleA12(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mappingAndStoreVenue($data, $newVenue);
  }
  
  function handleA13(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mappingAndStoreVenue($data, $newVenue);
  }
  
  function handleA38(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mappingAndStoreVenue($data, $newVenue);
  }
  
  function handleA44(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mappingAndStoreVenue($data, $newVenue);
  }
  
  function handleA45(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mappingAndStoreVenue($data, $newVenue);
  }
  
  function handleA54(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mappingAndStoreVenue($data, $newVenue);
  }
  
  function handleA55(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mappingAndStoreVenue($data, $newVenue);
  }
  
  function handleZ99(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mappingAndStoreVenue($data, $newVenue);
  }
  
  function trashNDA(CSejour $newVenue, CInteropSender $sender) {
    return true;
  }
  
  function admitFound(CSejour $newVenue) {
    $exchange_ihe = $this->_ref_exchange_ihe;
    $sender       = $this->_ref_sender;
    
    $venueRI       = CValue::read($data['admitIdentifiers'], "RI");
    $venueRISender = CValue::read($data['admitIdentifiers'], "RI_Sender");
    $venueNPA      = CValue::read($data['admitIdentifiers'], "NPA");
    $venueAN       = CValue::read($data['personIdentifiers'], "NA");
    
    $NDA = new CIdSante400();
    if ($venueAN) {
      $NDA = CIdSante400::getMatch("CSejour", $sender->_tag_sejour, $venueAN);
    }
    
    if ($NDA->_id) {
      return true;
    }
    
    if ($newVenue->load($venueRI)) {
      return true;
    }
    
    return false;
  }
  
  function mappingAndStoreVenue($data, CSejour $newVenue) {
    $exchange_ihe = $this->_ref_exchange_ihe;
    $sender       = $this->_ref_sender;
    
    // Mapping du s�jour
    $this->mappingAndStoreVenue($data, $newVenue);
    
    // Notifier les autres destinataires autre que le sender
    $newVenue->_eai_initiateur_group_id = $sender->group_id;
    if ($msgVenue = $newVenue->store()) {
      return $exchange_ihe->setAckAR($ack, "E201", null, $newVenue);
    }
    
    $codes   = array ("I202", "I226");
    $comment = CEAISejour::getComment($newVenue);
    
    return $exchange_ihe->setAckAA($ack, $codes, $comment, $newVenue);
  }
  
  function mappingVenue($data, CSejour $newVenue) {
    // Segment PV1
    $this->getPV1($data["PV1"], $newVenue);
    
    // Segment PV2
    $this->getPV2($data["PV2"], $newVenue);
    
    // Segment ZBE
    $this->getZBE($data["ZBE"], $newVenue);
    
    // Segment ZFD
    $this->getZFD($data["ZFD"], $newVenue);
    
    // Segment ZFM
    $this->getZFM($data["ZFM"], $newVenue);
    
    // Segment ZFP
    $this->getZFP($data["ZFP"], $newVenue);
    
    // Segment ZFV
    $this->getZFV($data["ZFV"], $newVenue);

    /* TODO Supprimer ceci apr�s l'ajout des times picker */
    $newVenue->_hour_entree_prevue = null;
    $newVenue->_min_entree_prevue = null;
    $newVenue->_hour_sortie_prevue = null;
    $newVenue->_min_sortie_prevue = null;
    
    return $newVenue;
  }
  
  
  function getPV1(DOMNode $node, CSejour $newVenue) {    
    // Classe de patient
    $this->getPatientClass($node, $newVenue);

    // H�bergement du patient
    $this->getPL($node, $newVenue);
    
    // Type de l'admission
    $this->getAdmissionType($node, $newVenue);
    
    // M�decin responsable
    $this->getAttendingDoctor($node, $newVenue);
    
    // M�decin adressant
    $this->getReferringDoctor($node, $newVenue);
    
    // Discipline m�dico-tarifaire
    $this->getHospitalService($node, $newVenue);
    
    // Mode d'entr�e
    $this->getAdmitSource($node, $newVenue);
    
    // Code tarif su s�jour
    $this->getFinancialClass($node, $newVenue);
    
    // Demande de chambre particuli�re
    $this->getCourtesyCode($node, $newVenue);
    
    // Etablissement de destination
    $this->getDischargedToLocation($node, $newVenue);
    
    // Entr�e / Sortie r�elle du s�jour
    $this->getAdmitDischarge($node, $newVenue);
  }
  
  function getPatientClass(DOMNode $node, CSejour $newVenue) {
    $patient_class = CHL7v2TableEntry::mapFrom("4", $this->queryTextNode("PV1.2", $node));
 
    $newVenue->type = $patient_class ? $patient_class : "comp";
  }
  
  function getPL(DOMNode $node, CSejour $newVenue) {
    /* @todo Gestion des mouvements / affectations */
  }
  
  function getAdmissionType(DOMNode $node, CSejour $newVenue) {
    /* @todo G�rer par la suite avec les naissances */
  }
  
  function getAttendingDoctor(DOMNode $node, CSejour $newVenue) {
    $PV17 = $this->query("PV1.7", $node);
    
    $mediuser = new CMediusers();
    foreach ($PV17 as $_PV17) {
      $newVenue->praticien_id = $this->getDoctor($_PV17, $mediuser);
    }
    
    // Dans le cas ou la venue ne contient pas de medecin responsable
    // Attribution d'un medecin indetermin�
    if (!$newVenue->praticien_id) {
      $newVenue->praticien_id = $this->createIndeterminateDoctor();
    }
  }
  
  function getDoctor(DOMNode $node, CMbObject $object) {
    $type_id    = $this->queryTextNode("XCN.13", $node);
    $id         = $this->queryTextNode("XCN.1", $node);
    $last_name  = $this->queryTextNode("XCN.2/FN.1", $node);
    $first_name = $this->queryTextNode("XCN.3", $node);
    
    switch ($type_id) {
      case "RPPS" :
        $object->rpps = $id;
        break;
      case "ADELI" :
        $object->adeli = $id;
        break;
      case "RI" :
        // Notre propre RI
        if (($this->queryTextNode("XCN.9/HD.2", $node) == CAppUI::conf("hl7 assigningAuthorityUniversalID"))) {mbLog("ixi");
          $object->id = $id;
          break;
        }
      default :
        if ($object instanceof CMediusers) {
          $object->_user_first_name = $first_name;
          $object->_user_last_name  = $last_name;
        }
        if ($object instanceof CMedecin) {
          $object->prenom = $first_name;
          $object->nom    = $last_name;
        }
        break;
    }
    
    // Cas o� l'on a aucune information sur le m�decin
    if (!$object->rpps && !$object->adeli && !$object->_id &&
        (($object instanceof CMediusers && (!$object->_user_first_name || $object->_user_last_name)) ||
        ($object instanceof CMedecin && (!$object->prenom || $object->nom)))) {
      return null;      
    }
    
    if ($object->loadMatchingObject()) {
      return $object->_id;
    }
    
    if ($object instanceof CMediusers) {
      $object->_user_first_name = $first_name;
      $object->_user_last_name  = $last_name;
      
      return $this->createDoctor($object);
    }
  }
  
  function createDoctor(CMediusers $mediuser) {
    $sender = $this->_ref_sender;

    $function = new CFunctions();
    $function->text = CAppUI::conf("hl7 importFunctionName");
    $function->group_id = $sender->group_id;
    $function->loadMatchingObject();
    if (!$function->_id) {
      $function->type = "cabinet";
      $function->compta_partagee = 0;
      $function->store();
    }
    $mediuser->function_id = $function->_id;
    $mediuser->makeUsernamePassword($mediuser->_user_first_name, $mediuser->_user_last_name, null, true);
    $mediuser->_user_type = 13; // Medecin
    $mediuser->actif = CAppUI::conf("hl7 doctorActif") ? 1 : 0; 
    
    $user = new CUser();
    $user->user_last_name   = $mediuser->_user_last_name;
    $user->user_first_name  = $mediuser->_user_first_name;
    $users = $user->seek("$user->user_last_name $user->user_first_name");
    if (count($users) == 1) {
      $user = reset($users);
      $user->loadRefMediuser();
      $mediuser = $user->_ref_mediuser;
    } else {
      $mediuser->store();
    }
    
    return $mediuser->_id;
  }
  
  function createIndeterminateDoctor() {
    $sender = $this->_ref_sender;
    
    $user    = new CUser();
    $user->user_last_name = CAppUI::conf("hl7 indeterminateDoctor")." $sender->group_id";
    if (!$user->loadMatchingObject()) {
      $mediuser = new CMediusers();
      $mediuser->_user_last_name = $user->user_last_name;
      
      return $this->createDoctor($mediuser);
    } 
      
    return $user->loadRefMediuser()->_id;
  }
  
  function getReferringDoctor(DOMNode $node, CSejour $newVenue) {
    $PV18 = $this->query("PV1.8", $node);
    
    $medecin = new CMedecin();
    foreach ($PV18 as $_PV18) {
      $newVenue->adresse_par_prat_id = $this->getDoctor($_PV18, $medecin);
    }
  }
  
  function getHospitalService(DOMNode $node, CSejour $newVenue) {
    $newVenue->discipline_id = $this->queryTextNode("PV1.10", $node);
  }
  
  function getAdmitSource(DOMNode $node, CSejour $newVenue) {
    $admit_source = $this->queryTextNode("PV1.14", $node);
    
    /* @todo Voir comment g�rer */
    
  }
  
  function getFinancialClass(DOMNode $node, CSejour $newVenue) {
    /* @todo Voir comment g�rer */
  }
  
  function getCourtesyCode(DOMNode $node, CSejour $newVenue) {
    $newVenue->chambre_seule = $this->getBoolean($this->queryTextNode("PV1.22", $node));
  }
  
  function getDischargeDisposition(DOMNode $node, CSejour $newVenue) {
    /* @todo Festion des circonstances de sortie ? */
  }
  
  function getDischargedToLocation(DOMNode $node, CSejour $newVenue) {
    if (!$finess = $this->queryTextNode("PV1.37/DLD.1", $node)) {
      return;
    }
    
    $etab_ext = new CEtabExterne();
    $etab_ext->finess = $finess;
    if (!$etab_ext->loadMatchingObject()) {
      return;
    }
    
    $newVenue->etablissement_sortie_id = $etab_ext->_id;
  }
  
  function getAdmitDischarge(DOMNode $node, CSejour $newVenue) {
    $newVenue->entree_reelle = $this->queryTextNode("PV1.44", $node);
    $newVenue->sortie_reelle = $this->queryTextNode("PV1.45", $node);
  }
  
  function getPV2(DOMNode $node, CSejour $newVenue) {    
    // Entr�e / Sortie pr�vue du s�jour
    $this->getExpectedAdmitDischarge($node, $newVenue);
    
    // Mode de transport d'entr�e
    $this->getModeArrivalCode($node, $newVenue);
  }
  
  function getExpectedAdmitDischarge(DOMNode $node, CSejour $newVenue) {
    $newVenue->entree_prevue = $this->queryTextNode("PV2.8", $node);
    $newVenue->sortie_prevue = $this->queryTextNode("PV2.9", $node);
  }
  
  function getModeArrivalCode(DOMNode $node, CSejour $newVenue) {
    $mode_arrival_code = $this->queryTextNode("PV2.38", $node);
    
    $newVenue->transport = CHL7v2TableEntry::mapFrom("0430", $mode_arrival_code);
  }
  
  function getZBE(DOMNode $node, CSejour $newVenue) {    
    
  }
  
  function getZFD(DOMNode $node, CSejour $newVenue) {  
    /* @todo � associer sur le patient - Date lunaire du patient */
  }
  
  function getZFM(DOMNode $node, CSejour $newVenue) {   
    // Mode entr�e PMSI 
    $this->getModeEntreePMSI($node, $newVenue);
    
    // Mode de sortie PMSI
    $this->getModeSortiePMSI($node, $newVenue);
    
    // Mode de provenance PMSI
    $this->getModeProvenancePMSI($node, $newVenue);
    
    // Mode de destination PMSI
    $this->getModeDestinationPMSI($node, $newVenue);
  }
  
  function getModeEntreePMSI(DOMNode $node, CSejour $newVenue) {
    $newVenue->mode_entree = $this->queryTextNode("ZFM.1", $node);
  }
  
  function getModeSortiePMSI(DOMNode $node, CSejour $newVenue) {
    $newVenue->mode_sortie = CHL7v2TableEntry::mapFrom("9001", $this->queryTextNode("ZFM.2", $node)); 
  }
  
  function getModeProvenancePMSI(DOMNode $node, CSejour $newVenue) {
    $newVenue->provenance = $this->queryTextNode("ZFM.3", $node);
  }
  
  function getModeDestinationPMSI(DOMNode $node, CSejour $newVenue) {
    $newVenue->destination = $this->queryTextNode("ZFM.4", $node);
  }
  
  function getZFP(DOMNode $node, CSejour $newVenue) {    
    /* @todo � associer sur le patient */
  }
  
  function getZFV(DOMNode $node, CSejour $newVenue) {    
    // Etablissement de provenance
    $this->getEtablissementProvenance($node, $newVenue);
  }
  
  function getEtablissementProvenance(DOMNode $node, CSejour $newVenue) {
    if (!$finess = $this->queryTextNode("ZFV.1/DLD.1", $node)) {
      return;
    }
    
    $etab_ext = new CEtabExterne();
    $etab_ext->finess = $finess;
    if (!$etab_ext->loadMatchingObject()) {
      return;
    }
    
    $newVenue->etablissement_entree_id = $etab_ext->_id;
  }
}
?>