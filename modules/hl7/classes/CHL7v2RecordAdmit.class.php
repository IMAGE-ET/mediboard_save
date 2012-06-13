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
  var $_object_found_by_vn = null;
  
  function getContentNodes() {
    $data  = parent::getContentNodes();

    $sender = $this->_ref_sender;
    
    $this->queryNodes("NK1", null, $data, true);
    
    $this->queryNodes("ROL", null, $data, true);    
    
    $PV1 = $this->queryNode("PV1", null, $data, true);

    $data["admitIdentifiers"] = $this->getAdmitIdentifiers($PV1, $sender);
    
    $this->queryNode("PV2", null, $data, true);
    
    // Traitement des segments sp�cifiques extension fran�aise PAM
    if ($this->_is_i18n == "FR") {
      $this->queryNode("ZBE", null, $data, true);
    
      $this->queryNode("ZFP", null, $data, true);
      
      $this->queryNode("ZFV", null, $data, true);
      
      $this->queryNode("ZFM", null, $data, true);
      
      $this->queryNode("ZFD", null, $data, true);
    }
    
    $this->queryNodes("GT1", null, $data, true);
    
    return $data;
  }
  
  function getVenueAN($sender, $data) {
    switch ($sender->_configs["handle_NDA"]) {
      case 'PV1_19':
        return CValue::read($data['admitIdentifiers'], "AN");
      default:
       return CValue::read($data['personIdentifiers'], "AN");
    }
  }
  
  function handle(CHL7Acknowledgment $ack, CPatient $newPatient, $data) {
    $event_temp = $ack->event;

    $exchange_ihe = $this->_ref_exchange_ihe;
    $sender       = $exchange_ihe->_ref_sender;
    $sender->loadConfigValues();

    $this->_ref_sender = $sender;

    // Acquittement d'erreur : identifiants RI et NA, VN non fournis
    if (!$data['admitIdentifiers'] && !$this->getVenueAN($sender, $data)) {
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
    $newVenue->loadRefPatient();
    
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
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    // R�cup�rer donn�es de la mutation
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleA03(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    // R�cup�rer donn�es de la sortie
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
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
    $venueVN       = CValue::read($data['admitIdentifiers'], "VN");
    $venueAN       = $this->getVenueAN($sender, $data);

    $NDA = new CIdSante400();
    if ($venueAN) {
      $NDA = CIdSante400::getMatch("CSejour", $sender->_tag_sejour, $venueAN);
    }
	
    // NDA non connu (non fourni ou non retrouv�)
    if (!$NDA->_id) {
      // Aucun NDA fourni / Association du NDA
      $code_NDA = !$venueAN ? "I225" : "I222";
      
      $found = false;
      
      // NPA fourni
      if (!$found && $venueNPA) {
        /* @todo G�rer ce cas */
      }
      
      // VN fourni
      if (!$found && $venueVN) {
        // Le champ PV1.2 conditionne le remplissage et l'interpr�tation de PV1.19
        $this->getSejourByVisitNumber($newVenue, $data);
        if ($newVenue->_id) {
          $found = true;
          
          // Mapping du s�jour
          $this->mappingVenue($data, $newVenue);
          
          // Notifier les autres destinataires autre que le sender
          $newVenue->_eai_initiateur_group_id = $sender->group_id;
          $newVenue->_generate_NDA = false;
          if ($msgVenue = $newVenue->store()) {
            return $exchange_ihe->setAckAR($ack, "E201", $msgVenue, $newVenue);
          }
                    
          $code_NDA      = "A222";
          $_modif_sejour = true;
        }
      }
      
      // RI fourni
      if (!$found && $venueRI) {
        // Recherche du s�jour par son RI
        if ($newVenue->load($venueRI)) {
          $recoveredSejour = clone $newVenue;
          
          // Mapping du s�jour
          $this->mappingVenue($data, $newVenue);
          
          // Le s�jour retrouv� est-il diff�rent que celui du message ?
          /* @todo voir comment faire (m�me patient, m�me praticien, m�me date ?) */
          
          // Notifier les autres destinataires autre que le sender
          $newVenue->_eai_initiateur_group_id = $sender->group_id;
          $newVenue->_generate_NDA = false;
          if ($msgVenue = $newVenue->store()) {
            return $exchange_ihe->setAckAR($ack, "E201", $msgVenue, $newVenue);
          }
                    
          $code_NDA      = "I221";
          $_modif_sejour = true; 
        }
        // S�jour non retrouv� par son RI
        else {
          $code_NDA = "I220";
        }  
      }
      
      if (!$newVenue->_id) {
        // Mapping du s�jour
        $this->mappingVenue($data, $newVenue);
      
        // S�jour retrouv� ?
        if (CAppUI::conf("hl7 strictSejourMatch")) {
          // Recherche d'un num dossier d�j� existant pour cette venue 
          if ($newVenue->loadMatchingSejour(null, true, false)) {                 
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
        $newVenue->_generate_NDA = false;
        if ($msgVenue = $newVenue->store()) {
          return $exchange_ihe->setAckAR($ack, "E201", $msgVenue, $newVenue);
        }
      }
      
      if ($msgNDA = CEAISejour::storeNDA($NDA, $newVenue, $sender)) {
        return $exchange_ihe->setAckAR($ack, "E202", $msgNDA, $newVenue);
      }
      
      // Cr�ation du VN, voir de l'objet
      if ($msgVN = $this->createObjectByVisitNumber($newVenue, $data)) {
        return $exchange_ihe->setAckAR($ack, "E210", $msgVN, $newVenue);
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
        $code_NDA = "I223"; 
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
      
      // Cr�ation du VN, voir de l'objet
      if ($msgVN = $this->createObjectByVisitNumber($newVenue, $data)) {
        return $exchange_ihe->setAckAR($ack, "E210", $msgVN, $newVenue);
      }
            
      $codes = array ("I202", $code_NDA);
      
      $comment = CEAISejour::getComment($newVenue);
    }
    
    // Mapping du mouvement
    $return_movement = $this->mapAndStoreMovement($ack, $newVenue, $data);
    if (is_string($return_movement)) {
      return $exchange_ihe->setAckAR($ack, "E207", $return_movement, $newVenue);
    }
    $movement = $return_movement;
    
    // Mapping de l'affectation
    $return_affectation = $this->mapAndStoreAffectation($ack, $newVenue, $data, $return_movement);
    if (is_string($return_affectation)) {
      return $exchange_ihe->setAckAR($ack, "E208", $return_affectation, $newVenue);
    }
    $affectation = $return_affectation;
    
    // Affectation de l'affectation au mouvement
    if ($affectation && $affectation->_id) {
      $movement->affectation_id = $affectation->_id;
      $movement->store();
    }
    
    return $exchange_ihe->setAckAA($ack, $codes, $comment, $newVenue);
  }
  
  function handleA06(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleA07(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleA08(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleA11(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    // Suppression de l'entr�e r�elle / mode d'entr�e
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleA12(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleA13(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    // Suppression sortie r�elle, mode de sortie, ...
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleA14(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleA16(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleA25(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleA38(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleA44(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleA54(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleA55(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleZ80(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleZ81(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleZ84(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleZ85(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function handleZ99(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $this->_ref_exchange_ihe->setAckAR($ack, "E204", null, $newVenue);
    }
    
    return $this->mapAndStoreVenue($ack, $newVenue, $data);
  }
  
  function trashNDA(CSejour $newVenue, CInteropSender $sender) {
    return true;
  }
  
  function getSejourByVisitNumber(CSejour $newVenue, $data) {
    $sender  = $this->_ref_sender;
    $venueVN = CValue::read($data['admitIdentifiers'], "VN");
        
    $where = $ljoin = array();
    $where["id_sante400.tag"]   = " = '$sender->_tag_visit_number'";
    $where["id_sante400.id400"] = " = '$venueVN'";
           
    switch ($this->queryTextNode("PV1.2", $data["PV1"])) {
      // Identifie la venue pour actes et consultation externe
      case 'O':
        $consultation = new CConsultation();
                
        $ljoin["id_sante400"]              = "id_sante400.object_id = consultation.consultation_id";
        $where["id_sante400.object_class"] = " = 'CConsultation'";
        $where["consultation.type"]        = " != 'chimio'";
        
        $consultation->loadObject($where, null, null, $ljoin);
        // N�cessaire pour savoir quel objet cr�� en cas de besoin
        $this->_object_found_by_vn = $consultation;
        
        if (!$consultation->_id) {
          return false;
        }
        
        $newVenue->load($consultation->sejour_id);
        
        return true;
      // Identifie une s�ance 
      case 'R':
        $consultation = new CConsultation();
                
        $ljoin["id_sante400"]              = "id_sante400.object_id = consultation.consultation_id";
        $where["id_sante400.object_class"] = " = 'CConsultation'"; 
        $where["consultation.type"]        = " = 'chimio'";
        
        $consultation->loadObject($where, null, null, $ljoin);
        // N�cessaire pour savoir quel objet cr�� en cas de besoin
        $this->_object_found_by_vn = $consultation;
        
        if (!$consultation->_id) {
          return false;
        }
        
        $newVenue->load($consultation->sejour_id);
        
        return true;
      // Identifie le n� de passage aux urgences
      case 'E':
        $rpu = new CRPU();
        
        $ljoin["id_sante400"]              = "id_sante400.object_id = rpu.rpu_id";
        $where["id_sante400.object_class"] = " = 'CRPU'"; 
        
        $rpu->loadObject($where, null, null, $ljoin);
        // N�cessaire pour savoir quel objet cr�� en cas de besoin
        $this->_object_found_by_vn = $rpu;
        
        if (!$rpu->_id) {
          return false;
        }
        
        $newVenue->load($rpu->sejour_id);
        
        return true;
      // Identifie le s�jour ou hospitalisation � domicile
      default:      
        $idexVisitNumber = CIdSante400::getMatch("CSejour", $sender->_tag_visit_number, $venueVN);  
        $this->_object_found_by_vn = $newVenue;
        if (!$idexVisitNumber->_id) {
          return false;
        }
        
        $newVenue->load($idexVisitNumber->object_id);
        $this->_object_found_by_vn = $newVenue;
        
        return true;
    }
    
    return false;
  }
  
  function createObjectByVisitNumber(CSejour $newVenue, $data) {
    $venueVN = CValue::read($data['admitIdentifiers'], "VN");
    if (!$venueVN) {
      return;
    }
    
    $this->getSejourByVisitNumber($newVenue, $data);
    if (!$this->_object_found_by_vn) {
      return; 
    }
    
    $object_found_by_vn = $this->_object_found_by_vn;
    // Cr�ation de l'objet ? 
    if (!$object_found_by_vn->_id) {
      if (!CAppUI::conf("smp create_object_by_vn")) {
        return;
      }
            
      $where = array();
      $where["sejour_id"] = " = '$newVenue->_id'";
      $object_found_by_vn->sejour_id = $newVenue->_id;

      // On va rechercher l'objet en fonction de son type, o� le cr�er
      switch ($this->queryTextNode("PV1.2", $data["PV1"])) {
        // Identifie la venue pour actes et consultation externe (CConsultation && type != chimio)
        case 'O':
          $where["type"] = " != 'chimio'";
          break;
        // Identifie une s�ance (CConsultation && type == chimio)
        case 'R':
          $where["type"] = " = 'chimio'";
          $object_found_by_vn->type = "chimio";
          break;
        // Identifie le n� de passage aux urgences
        case 'E':
          $object_found_by_vn->_patient_id = $newVenue->patient_id;
          $object_found_by_vn->_entree     = $newVenue->entree;
          $object_found_by_vn->_group_id   = $newVenue->group_id;
          
          break;
      }  
      
      $count_list = $object_found_by_vn->countList($where);
      if ($count_list > 1) {
        /* @todo voir comment g�rer ceci ! */
        return;
      }
      
      if ($object_found_by_vn instanceof CConsultation) {        
        $datetime = $this->queryTextNode("EVN.6/TS.1", $data["EVN"]);
        
        if ($data["PV2"]) {
          $object_found_by_vn->motif = $this->queryTextNode("PV2.12", $data["PV2"]);
        }
        
        // Cr�ation de la consultation
        if ($msg = $object_found_by_vn->createByDatetime($datetime, $newVenue->praticien_id, $newVenue->patient_id)) {
          return $msg;
        }
      }
      
      // Dans le cas o� l'on doit cr�er l'objet
      if (!$object_found_by_vn->_id) {
        if ($msg = $object_found_by_vn->store()) {
          return $msg;
        }
      }
    }
    
    // On affecte le VN
    $sender       = $this->_ref_sender;
    $object_class = $object_found_by_vn->_class;
    $object_id    = $object_found_by_vn->_id;
      
    $idexVN = CIdSante400::getMatch($object_class, $sender->_tag_visit_number, $venueVN, $object_id);
    // L'idex est d�j� associ� sur notre objet
    if ($idexVN->_id) {
      return;
    }
    
    // Cr�ation de l'idex
    $idexVN->last_update = mbDateTime();

    return $idexVN->store();
  } 
  
  function admitFound(CSejour $newVenue, $data) {
    $exchange_ihe = $this->_ref_exchange_ihe;
    $sender       = $this->_ref_sender;
    
    $venueRI       = CValue::read($data['admitIdentifiers'], "RI");
    $venueRISender = CValue::read($data['admitIdentifiers'], "RI_Sender");
    $venueVN       = CValue::read($data['admitIdentifiers'], "VN");
    $venueNPA      = CValue::read($data['admitIdentifiers'], "NPA");
    $venueAN       = $this->getVenueAN($sender, $data);
    
    $NDA = new CIdSante400();
    if ($venueAN) {
      $NDA = CIdSante400::getMatch("CSejour", $sender->_tag_sejour, $venueAN);
    }
    
    if ($NDA->_id) {
      $newVenue->load($NDA->object_id);
      
      return true;
    }

    if ($newVenue->load($venueRI)) {
      return true;
    }
    
    if ($venueVN) {
      return $this->getSejourByVisitNumber($newVenue, $data);
    }
    
    return false;
  }
  
  function mapAndStoreVenue(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    $exchange_ihe = $this->_ref_exchange_ihe;
    $sender       = $this->_ref_sender;
    
    // Mapping du s�jour
    $this->mappingVenue($data, $newVenue);
    
    // Notifier les autres destinataires autre que le sender
    $newVenue->_eai_initiateur_group_id = $sender->group_id;
    if ($msgVenue = $newVenue->store()) {
      return $exchange_ihe->setAckAR($ack, "E201", $msgVenue, $newVenue);
    }
    
    // Mapping du mouvement
    $return_movement = $this->mapAndStoreMovement($ack, $newVenue, $data);
    if (is_string($return_movement)) {
      return $exchange_ihe->setAckAR($ack, "E207", $return_movement, $newVenue);
    }
    $movement = $return_movement;
    
    // Mapping de l'affectation
    $return_affectation = $this->mapAndStoreAffectation($ack, $newVenue, $data, $return_movement);
    if (is_string($return_affectation)) {
      return $exchange_ihe->setAckAR($ack, "E208", $return_affectation, $newVenue);
    }
    $affectation = $return_affectation;
    
    // Attribution de l'affectation au mouvement
    if ($affectation && $affectation->_id) {
      $movement->affectation_id = $affectation->_id;
      $movement->store();
    }
    
    // Cr�ation du VN, voir de l'objet
    if ($msgVN = $this->createObjectByVisitNumber($newVenue, $data)) {
      return $exchange_ihe->setAckAR($ack, "E210", $msgVN, $newVenue);
    }
    
    $codes   = array ("I202", "I226");
    $comment = CEAISejour::getComment($newVenue);
    
    return $exchange_ihe->setAckAA($ack, $codes, $comment, $newVenue);
  }
  
  function mappingVenue($data, CSejour $newVenue) {
    $event_code = $this->_ref_exchange_ihe->code;
    // Cas sp�cifique de certains segments 
    // A38 : Annulation du s�jour
    if ($event_code == "A38") {
      $newVenue->annule = 1;
    }
    
    // Segment PV1
    $this->getSegment("PV1", $data, $newVenue);
    
    // Segment PV2
    $this->getSegment("PV2", $data, $newVenue);
    
    // Segment ZFD
    $this->getSegment("ZFD", $data, $newVenue);
    
    // Segment ZFM
    $this->getSegment("ZFM", $data, $newVenue);
    
    // Segment ZFP
    $this->getSegment("ZFP", $data, $newVenue);
    
    // Segment ZFV
    $this->getSegment("ZFV", $data, $newVenue);

    // D�biteurs
    if (array_key_exists("GT1", $data)) {
      foreach ($data["GT1"] as $_GT1) {
        $this->getGT1($_GT1, $newVenue);
      }
    }

    /* TODO Supprimer ceci apr�s l'ajout des times picker */
    $newVenue->_hour_entree_prevue = null;
    $newVenue->_min_entree_prevue = null;
    $newVenue->_hour_sortie_prevue = null;
    $newVenue->_min_sortie_prevue = null;
    
    return $newVenue;
  }
  
  function mapAndStoreMovement(CHL7Acknowledgment $ack, CSejour $newVenue, $data) {
    if (!array_key_exists("ZBE", $data)) {
      return;
    }
    
    $exchange_ihe = $this->_ref_exchange_ihe;
    
    $movement = new CMovement();
    if (!$movement = $this->mappingMovement($data, $newVenue, $movement)) {
      $movement_id = $this->queryTextNode("ZBE.1/EI.1", $node);
      $comment = "Le mouvement '$movement_id' est inconnu dans Mediboard";
      return $exchange_ihe->setAckAR($ack, "E206", $comment, $newVenue);
    }
    
    return $movement;
  }
  
  function mapAndStoreAffectation(CHL7Acknowledgment $ack, CSejour $newVenue, $data, CMovement $movement = null) {
    if (!$movement) {
      return;
    }
    
    $exchange_ihe = $this->_ref_exchange_ihe;
    
    $PV1_3 = $this->queryNode("PV1.3", $data["PV1"]);
        
    $affectation = new CAffectation();
    $affectation->sejour_id = $newVenue->_id;

    // Si pas de lit on retourne une affectation vide
    if (!$this->queryTextNode("PL.3", $PV1_3)) {
      // On essaye de r�cup�rer le service dans ce cas depuis l'UF d'h�bergement
      $uf           = new CUniteFonctionnelle();
      $uf->group_id = $newVenue->group_id;
      $uf->code     = $this->queryTextNode("PL.1", $PV1_3); 
      if (!$uf->loadMatchingObject()) {
        return $affectation;      
      }

      $affectation_uf               = new CAffectationUniteFonctionnelle();
      $affectation_uf->uf_id        = $uf->_id;
      $affectation_uf->object_class = "CService";
      if (!$affectation_uf->loadMatchingObject()) {
        return $affectation;      
      }

      $newVenue->service_id = $affectation_uf->object_id;
      $newVenue->store();
      
      return $affectation;     
    }
    
    // Chargement des affectations du s�jour
    $datetime = $this->queryTextNode("EVN.6/TS.1", $data["EVN"]);
    
    if ($this->_ref_exchange_ihe->code == "A11") {
    	$affectation =  $newVenue->getCurrAffectation($datetime);
    	
      // Si on le mouvement n'a pas d'affectation associ�e, et que l'on a d�j� une affectation dans MB
      if (!$movement->affectation_id && $affectation->_id) {
        return "Le mouvement '$movement->_id' n'est pas li� � une affectation dans Mediboard";
      }
      
      // Si on a une affectation associ�e, alors on charge celle-ci
      if ($movement->affectation_id) {
        $affectation = $movement->loadRefAffectation();
      }
      
      if ($msg = $affectation->delete()) {
      	return $msg;
      }
      
      return null;
    }

    // Cas mutation - A02
    if ($this->_ref_exchange_ihe->code == "A02") {
      $affectation->entree = $datetime;
      $affectation->loadMatchingObject();

      // Si on ne retrouve pas une affectation
      // Cr�ation de l'affectation 
      // et mettre � 'effectuee' la pr�c�dente si elle existe sinon cr�ation de celle-ci
      if (!$affectation->_id) {
        // R�cup�ration du Lit et UFs
        $this->getPL($PV1_3, $affectation);
      
        $return_affectation = $newVenue->forceAffectation($datetime, $affectation->lit_id);
        if (is_string($return_affectation)) {
          return $return_affectation;
        }
        
        $affectation = $return_affectation;
      }
    }

    // Cas modification - Z99
    elseif ($this->_ref_exchange_ihe->code == "Z99") {
      $affectation =  $newVenue->getCurrAffectation($datetime);
      // Si on le mouvement n'a pas d'affectation associ�e, et que l'on a d�j� une affectation dans MB
      if (!$movement->affectation_id && $affectation->_id) {
        return "Le mouvement '$movement->_id' n'est pas li� � une affectation dans Mediboard";
      }
      
      // Si on a une affectation associ�e, alors on charge celle-ci
      if ($movement->affectation_id) {
        $affectation = $movement->loadRefAffectation();
      }
      // Sinon on r�cup�re et on met � jour la premi�re affectation
      else {
        $affectation->sejour_id = $newVenue->_id;
        $affectation->entree    = $newVenue->entree;
        $affectation->sortie    = $newVenue->sortie;
      }      
    }
    
    // Tous les autres cas on r�cup�re et on met � jour la premi�re affectation
    else {
      $affectation =  $newVenue->getCurrAffectation($datetime);    
      if (!$affectation->_id) {
        $affectation->sejour_id = $newVenue->_id;
        $affectation->entree    = $newVenue->entree;
        $affectation->sortie    = $newVenue->sortie;
      }
    } 
    
    // R�cup�ration du Lit et UFs
    $this->getPL($PV1_3, $affectation);
    $affectation->uf_medicale_id = $this->mappingUFMedicale($data);
    $affectation->uf_soins_id    = $this->mappingUFSoins($data);
    
    if ($msg = $affectation->store()) {
      return $msg;
    }
    
    return $affectation;
  }
  
  function mappingUFMedicale($data) {
    if (!array_key_exists("ZBE", $data)) {
      return;
    }
	
	if (!($ZBE_7 = $this->queryNode("ZBE.7", $data["ZBE"]))) {
		return;
	}
    
    return CUniteFonctionnelle::getUF($this->queryTextNode("XON.10", $ZBE_7))->_id;
  }
  
  function mappingUFSoins($data) {
    if (!array_key_exists("ZBE", $data)) {
      return;
    }
	
	if (!($ZBE_8 = $this->queryNode("ZBE.8", $data["ZBE"]))) {
		return;
	}
    
    return CUniteFonctionnelle::getUF($this->queryTextNode("XON.10", $ZBE_8))->_id;
  }  
  
  function mappingMovement($data, CSejour $newVenue, CMovement $movement) {
    // Segment ZBE    
    return $this->getZBE($data["ZBE"], $newVenue, $movement);
  }
  
  function getPV1(DOMNode $node, CSejour $newVenue) {    
    // Classe de patient
    $this->getPatientClass($node, $newVenue);
    
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
  
  function getPL(DOMNode $node, CAffectation $affectation) {
    $code_uf     = $this->queryTextNode("PL.1", $node);    
    $nom_lit     = $this->queryTextNode("PL.3", $node);
    
    $lit = new CLit();
    $lit->nom = $nom_lit;
    $lit->loadMatchingObjectEsc();
    
    // Affectation du lit
    $affectation->lit_id = $lit->_id;

    // Affectation de l'UF h�bergement
    $affectation->uf_hebergement_id = CUniteFonctionnelle::getUF($code_uf)->_id;
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
        if (($this->queryTextNode("XCN.9/HD.2", $node) == CAppUI::conf("hl7 assigning_authority_universal_id"))) {
          return $id;
        }
      default :
        // Recherche du praticien par son idex
        $id400  = CIdSante400::getMatch($object->_class, $this->_ref_sender->_tag_mediuser, $id);
        if ($id400->_id) {
          return $id400->object_id;
        }

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
        (($object instanceof CMediusers && !$object->_user_last_name) ||
        ($object instanceof CMedecin && !$object->nom))) {
      return null;      
    }
    
    if ($object instanceof CMedecin && $object->loadMatchingObjectEsc()) {
      return $object->_id;
    }
      
    if ($object instanceof CMediusers) {
      $user = new CUser;
      $user->user_first_name = $first_name;
      $user->user_last_name  = $last_name;
      if ($user->loadMatchingObjectEsc()) {
        return $user->_id;
      }
      
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
    $function->loadMatchingObjectEsc();
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
    if (!$user->loadMatchingObjectEsc()) {
      $mediuser = new CMediusers();
      $mediuser->_user_last_name = $user->user_last_name;
      
      return $this->createDoctor($mediuser);
    } 
      
    return $user->loadRefMediuser()->_id;
  }
  
  function getReferringDoctor(DOMNode $node, CSejour $newVenue) {
    $PV1_8 = $this->query("PV1.8", $node);
    
    $medecin = new CMedecin();
    foreach ($PV1_8 as $_PV1_8) {
      $newVenue->adresse_par_prat_id = $this->getDoctor($_PV1_8, $medecin);
    }
  }
  
  function getHospitalService(DOMNode $node, CSejour $newVenue) {
    $sender = $this->_ref_sender;
    $PV1_10 = $this->queryTextNode("PV1.10", $node);
    
    // Hospital Service
    switch ($sender->_configs["handle_PV1_10"]) {
      // idex du service
      case 'service':
        $newVenue->service_id = CIdSante400::getMatch("CService", $sender->_tag_service, $PV1_10)->object_id;
        break;
      // Discipline m�dico-tarifaire
      default:
        $newVenue->discipline_id = $PV1_10;
        break; 
    }
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
    /* @todo Gestion des circonstances de sortie ? */
  }
  
  function getDischargedToLocation(DOMNode $node, CSejour $newVenue) {
    if (!$finess = $this->queryTextNode("PV1.37/DLD.1", $node)) {
      return;
    }
    
    $etab_ext = new CEtabExterne();
    $etab_ext->finess = $finess;
    if (!$etab_ext->loadMatchingObjectEsc()) {
      return;
    }
    
    $newVenue->etablissement_sortie_id = $etab_ext->_id;
  }
  
  function getAdmitDischarge(DOMNode $node, CSejour $newVenue) {
    $event_code = $this->_ref_exchange_ihe->code;
    
    // On r�cup�re l'entr�e r�elle ssi msg !A05
    if ($event_code != "A05") {
      $newVenue->entree_reelle = $this->queryTextNode("PV1.44", $node);
    }
    
    // On r�cup�re la sortie r�elle ssi msg A03 / Z99
    if ($event_code == "A03" || $event_code == "Z99") {
      $newVenue->sortie_reelle = $this->queryTextNode("PV1.45", $node);
    }
    
    // Cas sp�cifique de certains segments 
    // A11 : on supprime la date d'entr�e r�elle 
    if ($event_code == "A11") {
      $newVenue->entree_reelle = "";
    }
    
    // A13 : on supprime la date de sortie r�elle 
    if ($event_code == "A13") {
      $newVenue->sortie_reelle = "";
    }
  }
  
  function getPV2(DOMNode $node, CSejour $newVenue) {    
    // Entr�e / Sortie pr�vue du s�jour
    $this->getExpectedAdmitDischarge($node, $newVenue);
    
    // Visit description
    $this->getVisitDescription($node, $newVenue);
    
    // Mode de transport d'entr�e
    $this->getModeArrivalCode($node, $newVenue);
  }
  
  function getExpectedAdmitDischarge(DOMNode $node, CSejour $newVenue) {
    $entree_prevue = $this->queryTextNode("PV2.8", $node);
    $sortie_prevue = $this->queryTextNode("PV2.9", $node);
    
    if (!$entree_prevue) {
      $entree_prevue = $newVenue->entree_reelle ? $newVenue->entree_reelle : $newVenue->entree_prevue;
    }
    $newVenue->entree_prevue = $entree_prevue;
    
    if ($sortie_prevue) {
      $newVenue->sortie_prevue = $sortie_prevue;
    }
    elseif (!$sortie_prevue && !$newVenue->sortie_prevue) {
      $newVenue->sortie_prevue = mbAddDateTime(CAppUI::conf("dPplanningOp CSejour sortie_prevue ".$newVenue->type).":00:00", 
                    $newVenue->entree_reelle ? $newVenue->entree_reelle : $newVenue->entree_prevue);
    } 
    else {
      $newVenue->sortie_prevue = $newVenue->sortie_reelle ? $newVenue->sortie_reelle : $newVenue->sortie_prevue;
    }

    // On r�cup�re l'entr�e et sortie r�elle ssi !entree_prevue && !sortie_prevue
    $parentNode = $node->parentNode;
    if (!$newVenue->entree_prevue) {
      $newVenue->entree_prevue = $this->queryTextNode("PV1.44", $this->queryNode("PV1", $parentNode));
    }
    
    if (!$newVenue->sortie_prevue) {
      $newVenue->sortie_prevue = $this->queryTextNode("PV1.45", $this->queryNode("PV1", $parentNode));
    }
  }

  function getVisitDescription(DOMNode $node, CSejour $newVenue) {    
    $newVenue->libelle = $this->queryTextNode("PV2.12", $node);
  }
  
  function getModeArrivalCode(DOMNode $node, CSejour $newVenue) {
    $mode_arrival_code = $this->queryTextNode("PV2.38", $node);
    
    $newVenue->transport = CHL7v2TableEntry::mapFrom("0430", $mode_arrival_code);
  }
  
  function getZBE(DOMNode $node, CSejour $newVenue, CMovement $movement) {    
    $sender       = $this->_ref_sender;
    $id400_create = false;
    
    $movement_id       = $this->queryTextNode("ZBE.1/EI.1", $node);
    $start_movement_dt = $this->queryTextNode("ZBE.2/TS.1", $node);
    $action            = $this->queryTextNode("ZBE.4", $node);
    $original_trigger  = $this->queryTextNode("ZBE.6", $node);
    
    $movement->sejour_id = $newVenue->_id;
    $movement->original_trigger_code = $original_trigger;
    // Notre propre ID de mouvement
    if (($this->queryTextNode("ZBE.1/EI.3", $node) == CAppUI::conf("hl7 assigning_authority_universal_id"))) {
      $movement_id_split       = explode("-", $movement_id);
      $movement->_id           = $movement_id_split[0];
      $movement->movement_type = $movement_id_split[1];
      $movement->loadMatchingObjectEsc();
      if (!$movement->_id) {
        return null;
      }
    }
    // ID mouvement provenant d'un syst�me tiers
    else {
      $id400Movement = CIdSante400::getMatch("CMovement", $sender->_tag_movement, $movement_id);
      $id400Movement->_id ? $movement->load($id400Movement->object_id) : ($id400_create = true);
      
      $movement->movement_type = $newVenue->getMovementType($original_trigger);
    }
    
    // Erreur dans le cas o� le type du mouvement est UPDATE ou CANCEL et que l'on a pas retrouv� le mvt
    if (($original_trigger == "UPDATE" || $original_trigger == "CANCEL") && !$movement->_id) {
      return null;
    }
    $movement->start_of_movement = $start_movement_dt;
    $movement->last_update = mbDateTime();
    
    if ($msg = $movement->store()) {
      return $msg;
    }
    
    if ($id400_create) {
      $id400Movement->last_update = mbDateTime();
      $id400Movement->object_id   = $movement->_id;
      if ($msg = $id400Movement->store()) {
        return $msg;
      } 
    }
    
    return $movement;
  }

  function getZFD(DOMNode $node, CSejour $newVenue) {  
    // Date lunaire
    if ($date_lunaire = $this->queryTextNode("ZFD.1", $node)) {
      $patient = $newVenue->_ref_patient;
      $patient->naissance = $date_lunaire;
      $patient->store();
    }
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
  	$ZFM_3 = $this->queryTextNode("ZFM.3", $node);
  	if ($ZFM_3 == 0) {
  	  $ZFM_3 = null;	
  	}
    $newVenue->provenance = $ZFM_3;
  }
  
  function getModeDestinationPMSI(DOMNode $node, CSejour $newVenue) {
  	$ZFM_4 = $this->queryTextNode("ZFM.4", $node);
  	if ($ZFM_4 == 0) {
  	  $ZFM_4 = null;	
  	}
    $newVenue->destination = $ZFM_4;
  }
  
  function getZFP(DOMNode $node, CSejour $newVenue) {    
    // Cat�gorie socioprofessionnelle
    if ($csp = $this->queryTextNode("ZFP.2", $node)) {
      $patient = $newVenue->_ref_patient;
      $patient->csp = $csp;
      $patient->store();
    }  
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
    if (!$etab_ext->loadMatchingObjectEsc()) {
      return;
    }
    
    $newVenue->etablissement_entree_id = $etab_ext->_id;
  }
  
  function getGT1(DOMNode $node, CSejour $newVenue) {
    
  }
}
?>