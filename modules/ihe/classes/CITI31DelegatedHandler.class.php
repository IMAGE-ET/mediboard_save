<?php

/**
 * ITI31 Delegated Handler
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CITI31DelegatedHandler 
 * ITI31 Delegated Handler
 */
class CITI31DelegatedHandler extends CITIDelegatedHandler {
  /**
   * @var array
   */
  static $handled        = array ("CSejour", "CAffectation", "CNaissance");
  /**
   * @var string
   */
  protected $profil      = "PAM";
  /**
   * @var string
   */
  protected $message     = "ADT";
  /**
   * @var string
   */
  protected $transaction = "ITI31";

  /**
   * @var array
   */
  static $inpatient      = array("comp", "ssr", "psy", "seances", "consult", "ambu");
  /**
   * @var array
   */
  static $outpatient     = array("urg", "exte");

  /**
   * If object is handled ?
   *
   * @param CMbObject $mbObject Object
   *
   * @return bool
   */
  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class, self::$handled);
  }

  /**
   * Trigger after event store
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function onAfterStore(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    $receiver = $mbObject->_receiver;
    $receiver->getInternationalizationCode($this->transaction);  

    // Traitement Sejour
    if ($mbObject instanceof CSejour) {
      $sejour = $mbObject;
      $sejour->loadRefPatient();

      // Si on ne souhaite explicitement pas de synchro
      if ($sejour->_no_synchro) {
        return;
      }
      
      // Si on est en train de cr�er un s�jour et qu'il s'agit d'une naissance
      $current_log = $sejour->loadLastLog();
      if ($current_log->type == "create" && $sejour->_naissance) {
        return;
      }

      // Si Serveur
      if (CAppUI::conf('smp server')) {
        return;
      }

      // Si initiateur du message
      if ($sejour->_eai_initiateur_group_id) {
        return;
      }

      // Si le group_id du s�jour est diff�rent de celui du destinataire
      if ($sejour->group_id != $receiver->group_id) {
        return;
      }

      // On ne synchronise pas un s�jour d'urgences qui est un reliquat
      $rpu = $sejour->loadRefRPU();
      if ($rpu && $rpu->mutation_sejour_id && ($rpu->sejour_id != $rpu->mutation_sejour_id)) {
        return;
      }

      $code = $this->getCodeSejour($sejour);

      if (!$code) {
        return;
      }

      // Cas o� :
      // * on est l'initiateur du message
      // * le destinataire ne supporte pas le message
      if (!$this->isMessageSupported($this->transaction, $this->message, $code, $receiver)) {
        return;
      }

      if (!$sejour->_NDA) {
        // G�n�ration du NDA dans le cas de la cr�ation, ce dernier n'�tait pas cr��
        if ($msg = $sejour->generateNDA()) {
          CAppUI::setMsg($msg, UI_MSG_ERROR);
        }

        $NDA = new CIdSante400();
        $NDA->loadLatestFor($sejour, $receiver->_tag_sejour);
        $sejour->_NDA = $NDA->id400;
      }

      $current_affectation = null;
      // Cas o� lors de l'entr�e r�elle j'ai une affectation qui n'a pas �t� envoy�e
      if ($sejour->fieldModified("entree_reelle") && !$sejour->_old->entree_reelle) {
        $current_affectation = $sejour->getCurrAffectation();
      }

      $this->createMovement($code, $sejour, $current_affectation);

      // Envoi de l'�v�nement
      $this->sendITI($this->profil, $this->transaction, $this->message, $code, $sejour);
    }

    // Traitement Affectation
    if ($mbObject instanceof CAffectation) {
      $affectation = $mbObject;
      $current_log = $affectation->_ref_current_log;

      if (!$current_log || $affectation->_no_synchro || !in_array($current_log->type, array("create", "store"))) {
        return;
      }

      // Affectation non li�e � un s�jour
      $sejour = $affectation->loadRefSejour();
      if (!$sejour->_id) {
        return;
      }

      // Si le group_id du s�jour est diff�rent de celui du destinataire
      if ($sejour->group_id != $receiver->group_id) {
        return;
      }

      // On envoie pas les affectations pr�visionnelles 
      if (!$receiver->_configs["send_provisional_affectation"] && $sejour->_etat == "preadmission") {
        return;
      }
      $first_affectation = $sejour->loadRefFirstAffectation();

      $code = $this->getCodeAffectation($affectation, $first_affectation);

      // Cas o� : 
      // * on est l'initiateur du message 
      // * le destinataire ne supporte pas le message
      if ($affectation->_eai_initiateur_group_id || !$this->isMessageSupported($this->transaction, $this->message, $code, $receiver)) {
        return;
      }

      $sejour->loadRefPatient();
      $sejour->_receiver = $receiver;
      
      $this->createMovement($code, $sejour, $affectation);
   
      // Envoi de l'�v�nement
      $this->sendITI($this->profil, $this->transaction, $this->message, $code, $mbObject);
    }
    
    // Traitement Naissance
    if ($mbObject instanceof CNaissance) {
      $current_log = $mbObject->loadLastLog();
      if ($current_log->type != "create") {
        return;
      }

      $sejour_enfant = $mbObject->loadRefSejourEnfant();
      $sejour_enfant->loadRefPatient();
      $sejour_enfant->_receiver = $receiver;
      
      $code = $this->getCodeSejour($sejour_enfant);
        
      // Cas o� : 
      // * on est l'initiateur du message 
      // * le destinataire ne supporte pas le message
      if ($mbObject->_eai_initiateur_group_id || !$this->isMessageSupported($this->transaction, $this->message, $code, $receiver)) {
        return;
      }
      
      if (!$sejour_enfant->_NDA) {
        // G�n�ration du NDA dans le cas de la cr�ation, ce dernier n'�tait pas cr��
        if ($msg = $sejour_enfant->generateNDA()) {
          CAppUI::setMsg($msg, UI_MSG_ERROR);
        }
        
        $NDA = new CIdSante400();
        $NDA->loadLatestFor($sejour_enfant, $receiver->_tag_sejour);
        $sejour_enfant->_NDA = $NDA->id400;
      }
      
      $current_affectation = null;
      // Cas o� lors de l'entr�e r�elle j'ai une affectation qui n'a pas �t� envoy�e
      if ($sejour_enfant->fieldModified("entree_reelle") && !$sejour_enfant->_old->entree_reelle) {
        $current_affectation = $sejour_enfant->getCurrAffectation();
      }

      $this->createMovement($code, $sejour_enfant, $current_affectation);

      // Envoi de l'�v�nement
      $this->sendITI($this->profil, $this->transaction, $this->message, $code, $sejour_enfant);
    }
  }

  /**
   * Create movement
   *
   * @param string       $code        HL7 event code
   * @param CSejour      $sejour      Admit
   * @param CAffectation $affectation Affectation
   *
   * @return CMovement|mixed
   */
  function createMovement($code, CSejour $sejour, CAffectation $affectation = null) {
    $insert = in_array($code, CHL7v2SegmentZBE::$actions["INSERT"]);
    $update = in_array($code, CHL7v2SegmentZBE::$actions["UPDATE"]);
    $cancel = in_array($code, CHL7v2SegmentZBE::$actions["CANCEL"]);

    $movement = new CMovement();
    // Initialise le mouvement 
    $movement->sejour_id = $sejour->_id;

    $affectation_id = null;
    if ($affectation) {
      $current_log       = $affectation->_ref_current_log;
      $first_affectation = $sejour->loadRefFirstAffectation();
    
      // Dans le cas o� il s'agit de la premi�re affectation du s�jour et qu'on est en type "cr�ation" on ne recherche pas 
      // un mouvement avec l'affectation, mais on va prendre le mouvement d'admission
      if ($current_log && ($current_log->type == "create") && $first_affectation && ($first_affectation->_id == $affectation->_id)) {
        $affectation_id = $affectation->_id;
        $affectation    = null;
      }
      else {
        $movement->affectation_id = $affectation->_id;  
      }
    }

    if ($insert) {
      // Dans le cas d'un insert le type correspond n�cessairement au type actuel du s�jour
      $movement->movement_type         = $sejour->getMovementType($code);
      $movement->original_trigger_code = $code;
      $movement->start_of_movement     = $this->getStartOfMovement($code, $sejour, $affectation);
      $movement->store();

      return $sejour->_ref_hl7_movement = $movement;
    }
    elseif ($update) {
      // Dans le cas d'un update le type correspond � celui du trigger
      $movement_type = null;

      // Mise � jour entr�e r�elle
      if ($sejour->fieldModified("entree_reelle")) {
        $movement_type = "ADMI";
      }
      
      // Mise � jour sortie r�elle
      if ($sejour->fieldModified("sortie_reelle")) {
        $movement_type = "SORT";
      }

      $movement->movement_type = $movement_type;

      // On ne recherche pas parmi les mouvements annul�s
      $movement->cancel = 0;
    }

    $order = "affectation_id DESC";

    $movements = $movement->loadMatchingList($order);

    if (!empty($movements)) {
      $movement = reset($movements);
    }
    
    if ($update) {
      if ($movement->original_trigger_code == "A02") {
        if (!$affectation) {
          $affectation = new CAffectation();
        }
        $affectation->load($movement->affectation_id);
      }
      $movement->start_of_movement = $this->getStartOfMovement($movement->original_trigger_code, $sejour, $affectation);
    }
    
    // on annule un mouvement sauf dans le cas d'une annulation de mutation et que 
    if ($cancel && !($code == "A12" && $movement->original_trigger_code != "A02")) {
      $movement->cancel = 1;
    }

    if ($affectation_id) {
      $movement->affectation_id = $affectation_id;
    }

    $movement->store();
    
    return $sejour->_ref_hl7_movement = $movement;
  }

  /**
   * Get start of movement
   *
   * @param string       $code        HL7 event code
   * @param CSejour      $sejour      Admit
   * @param CAffectation $affectation Affectation
   *
   * @return null|string
   */
  function getStartOfMovement($code, CSejour $sejour, CAffectation $affectation = null) {
    switch ($code) {
      // Admission hospitalis� / externe
      case 'A01' : 
      case 'A04' :
        // Date de l'admission
        return $sejour->entree_reelle;
      // Mutation : changement d'UF h�bergement
      case 'A02':
        return $affectation->entree;
      // Changement de statut externe ou urgence vers hospitalis�
      case 'A06':
        // Changement de statut hospitalis� ou urgence vers externe
      case 'A07':
        // Absence provisoire (permission) et mouvement de transfert vers un plateau technique pour acte (<48h)
      case 'A21':
        // Retour d'absence provisoire (permission) et mouvement de transfert vers un plateau technique pour acte (<48h)
      case 'A22': 
        // Changement de m�decin responsable
      case 'A54':
        // Changement d'UF m�dicale
      case 'Z80':
        // Changement d'UF de soins
      case 'Z84':
        // Date du transfert
        return CMbDT::dateTime();
      // Sortie d�finitive
      case 'A03':
        // Date de la sortie
        return $sejour->sortie_reelle;
      // Pr�-admission
      case 'A05':
      case 'A14' :
        // Date de la pr�-admission
        return $sejour->entree_prevue;
      // Sortie en attente
      case 'A16':
        // Date de la sortie
        return $sejour->sortie;
    }
  }

  /**
   * Get bascule HL7 event code
   *
   * @param CSejour $from Admit from
   * @param CSejour $to   Admit to
   *
   * @return string
   */
  function getBasculeCode(CSejour $from, CSejour $to) {
    $matrix = array(    // comp/M   comp/C   comp/O   bebe/*   ambu/*   urg/*   seances/* exte/*
      "comp/M"    => array( null,   "A06",   "A06",   "A06",   "A06",   "A07",   "A06",   "A07"),
      "comp/C"    => array("A06",    null,   "A06",   "A06",   "A06",   "A07",   "A06",   "A07"),
      "comp/O"    => array("A06",   "A06",    null,   "A06",   "A06",   "A07",   "A06",   "A07"),
      "bebe/*"    => array("A06",   "A06",   "A06",    null,   "A06",   "A07",   "A06",   "A07"),
      "ambu/*"    => array("A06",   "A06",   "A06",   "A06",    null,   "A07",   "A06",   "A07"),
      "urg/*"     => array("A06",   "A06",   "A06",   "A06",   "A06",    null,   "A06",   "A07"),
      "seances/*" => array("A06",   "A06",   "A06",   "A06",   "A06",   "A07",    null,   "A07"),
      "exte/*"    => array("A06",   "A06",   "A06",   "A06",   "A06",   "A07",   "A06",    null),
    );
    
    $from->completeField("type", "type_pec");
    $type_from     = $from->type;
    $type_pec_from = $from->type_pec;
    
    $to->completeField("type", "type_pec");
    $type_to     = $to->type;
    $type_pec_to = $to->type_pec;
    
    // psy || ssr == seances
    if (in_array($type_from, array("psy", "ssr"))) {
      $type_from = "seances";
    }
    if (in_array($type_to, array("psy", "ssr"))) {
      $type_to = "seances";
    }
    
    /* // TODO prendre en compte les sejours de type nouveau n�
    $naissances = $from->loadRefsNaissances();
    foreach ($naissances as $_naissance) {
      if ($naissances->sejour_bebe_id == $from->_id) {
        $type_from = "bebe";
        break;
      }
    }*/
    
    $row = CMbArray::first($matrix, array("$type_from/$type_pec_from", "$type_from/*"));
    
    if (!$row) {
      return $this->getModificationAdmitCode($from->_receiver);
    }
    
    $columns = array_flip(array_keys($matrix));
    $col_num = CMbArray::first($columns, array("$type_to/$type_pec_to", "$type_to/*"));
    
    if ($columns === null) {
      return $this->getModificationAdmitCode($from->_receiver);
    }

    return $row[$col_num];
  }

  /**
   * Get admit HL7 event code
   *
   * @param CSejour $sejour Admit
   *
   * @return null|string
   */
  function getCodeSejour(CSejour $sejour) {
    $current_log = $sejour->loadLastLog();
    if (!in_array($current_log->type, array("create", "store"))) {
      return null;
    }
    
    $receiver = $sejour->_receiver;
    $configs  = $receiver->_configs;
        
    $sejour->loadOldObject();
    // Cas d'une pr�-admission
    if ($sejour->_etat == "preadmission") {
      // Cr�ation d'une pr�-admission
      if ($current_log->type == "create") {
        // Pending admit
        if ($configs["iti31_pending_event_management"] && $sejour->recuse == -1) {
          return "A14";
        }

        return "A05";
      }

      // Cancel the pending admission
      if ($configs["iti31_pending_event_management"] && $sejour->recuse == -1 && $sejour->fieldModified("annule", "1")) {
        return "A27";
      }

      // Modification d'une pr�-admission
      // Cas d'une annulation ? 
      if ($sejour->fieldModified("annule", "1")) {
        return "A38";
      }
      
      // Cas d'un r�tablissement d'annulation ? 
      if ($sejour->fieldModified("annule", "0") && ($sejour->_old->annule == 1)) {
        return "A05";
      }

      // Annulation de l'admission
      if ($sejour->_old->entree_reelle && !$sejour->entree_reelle) {
        return "A11";
      }
      
      // Simple modification ? 
      return $this->getModificationAdmitCode($sejour->_receiver);
    }
    
    // Cas d'un s�jour en cours (entr�e r�elle)
    if ($sejour->_etat == "encours") {
      // Admission faite
      $sejour_old = $sejour->_old;
      if ($sejour->fieldModified("entree_reelle") && !$sejour_old->entree_reelle
          || $sejour->entree_reelle && !$sejour_old->entree_reelle
      ) {
        // Patient externe
        if (in_array($sejour->type, self::$outpatient)) {
          return "A04";
        } 
        // Admission hospitalis�
        return "A01";
      }
      
      // Confirmation de sortie
      if ($sejour->fieldModified("confirme", "1")) {
        return "A16";
      }
      
      // Annulation confirmation de sortie
      if ($sejour->_old->confirme && $sejour->fieldModified("confirme", "0")) {
        return "A25";
      }

      // Bascule du type et type_pec
      if ($sejour->fieldModified("type")) {
        return $this->getBasculeCode($sejour->_old, $sejour);
      }

      // Changement du m�decin responsable
      if ($sejour->fieldModified("praticien_id")) {
        $first_log = $sejour->loadFirstLog();

        $praticien_id = $sejour->getValueAtDate($first_log->date, "praticien_id");

        $send_change_attending_doctor = $configs["send_change_attending_doctor"];
        // Annulation du m�decin responsable
        if ($sejour->praticien_id == $praticien_id) {
          return (($send_change_attending_doctor == "A54") ? "A55" : $this->getModificationAdmitCode($receiver));
        }

        return (($send_change_attending_doctor == "A54") ? "A54" : $this->getModificationAdmitCode($receiver));
      }
      
      // R�attribution dossier administratif
      if ($sejour->fieldModified("patient_id")) {
        return "A44";
      }

      // Cas d'une annulation
      if ($sejour->fieldModified("annule", "1")) {
        return "A11";
      }
      
      // Annulation de la sortie r�elle
      if ($sejour->_old->sortie_reelle && !$sejour->sortie_reelle) {
        return "A13";
      }

      // Notification sur le transfert
      if ($configs["iti31_pending_event_management"]
          && $sejour->fieldModified("mode_sortie")
          && $sejour->mode_sortie == "transfert"
      ) {
        return "A15";
      }

      // Annulation de la notification sur le transfert
      if ($configs["iti31_pending_event_management"]
          && $sejour->_old->mode_sortie
          && $sejour->_old->mode_sortie == "transfert"
          && !$sejour->mode_sortie
      ) {
        return "A26";
      }

      // On ne transmet pas les modifications d'un s�jour d�s lors que celui-ci a une entr�e r�elle
      if (!$receiver->_configs["send_change_after_admit"]) {
        return null;
      }
      
      // Simple modification ? 
      return $this->getModificationAdmitCode($sejour->_receiver);
    }
    
    // Cas d'un s�jour cl�tur� (sortie r�elle)
    if ($sejour->_etat == "cloture") {
      // Sortie r�elle renseign�e
      if ($sejour->fieldModified("sortie_reelle") && !$sejour->_old->sortie_reelle) {
        return "A03";
      }
      
      // Modification de l'admission
      // Cas d'une annulation ? 
      if ($sejour->fieldModified("annule", "1")) {
        return "A13";
      }

      // On ne transmet pas les modifications d'un s�jour d�s lors que celui-ci a une entr�e r�elle
      if (!$receiver->_configs["send_change_after_admit"]) {
        return null;
      }

      // Simple modification ? 
      return $this->getModificationAdmitCode($sejour->_receiver);
    }

    return null;
  }

  /**
   * Get affectation HL7 event code
   *
   * @param CAffectation $affectation       Affectation
   * @param CAffectation $first_affectation First affectation
   *
   * @return null|string
   */
  function getCodeAffectation(CAffectation $affectation, CAffectation $first_affectation = null) {
    $current_log = $affectation->_ref_current_log;
    if (!in_array($current_log->type, array("create", "store"))) {
      return null;
    }
    
    $receiver = $affectation->_receiver;
    $configs  = $receiver->_configs;
    $service  = $affectation->loadRefService();

    if ($current_log->type == "create") {
      /* Affectation dans un service externe */
      if ($service->externe) {
        return "A21";
      }

      // Dans le cas o� il s'agit de la premi�re affectation du s�jour on ne fait pas une mutation mais une modification
      if ($first_affectation && ($first_affectation->_id == $affectation->_id)) {
        switch ($configs["send_first_affectation"]) {
          case 'Z99':
            return $this->getModificationAdmitCode($receiver);
          default:
            return "A02";
        }
      }
      
      // Cr�ation d'une affectation
      switch ($configs["send_transfer_patient"]) {
        case 'Z99':
          return $this->getModificationAdmitCode($receiver);
        default:
          return "A02";
      }
    }
            
    /* Affectation dans un service externe effectu�e */
    if ($service->externe && !$affectation->_old->effectue && $affectation->effectue) {
      return "A22";
    }

    /* Affectation dans un service externe effectu�e */
    if ($service->externe && $affectation->_old->effectue && !$affectation->effectue) {
      return "A53";
    }
    
    $send_change_medical_responsibility = $configs["send_change_medical_responsibility"];
    /* Changement d'UF M�dicale */
    if ($affectation->_old->uf_medicale_id && $affectation->fieldModified("uf_medicale_id")) {
      /* @todo G�rer le cas o� : cr�ation d'une nouvelle affectation && UM1 # UM2 */
      switch ($configs["send_change_medical_responsibility"]) {
        case 'Z80':
          return "Z80";
        case 'A02':
          return "A02";
        default:
          return $this->getModificationAdmitCode($receiver);
      }
    }

    /* Changement d'UF de Soins */
    if ($affectation->_old->uf_soins_id && $affectation->fieldModified("uf_soins_id")) {
      /* @todo G�rer le cas o� : cr�ation d'une nouvelle affectation && US1 # US2 */
      switch ($configs["send_change_nursing_ward"]) {
        case 'Z84':
          return "Z84";
        case 'A02':
          return "A02";
        default:
          return $this->getModificationAdmitCode($receiver);
      }
    }
 
    // Modifcation d'une affectation
    return $this->getModificationAdmitCode($affectation->_receiver);
  }

  /**
   * Get affectation HL7 event code
   *
   * @param CReceiverHL7v2 $receiver Receiver HL7v2
   *
   * @return string
   */
  function getModificationAdmitCode(CReceiverHL7v2 $receiver) {
    switch ($receiver->_i18n_code) {
      // Cas de l'extension fran�aise : Z99
      case "FR" :
        $code = "Z99";
        break;
      // Cas internationnal : A08
      default :
        $code = $receiver->_configs["modification_admit_code"];
        break;
    }
    
    return $code;
  }

  /**
   * Trigger before event merge
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function onBeforeMerge(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    if ($mbObject instanceof CSejour) {
      $sejour = $mbObject;

      $receiver = $sejour->_receiver;
      $receiver->getInternationalizationCode($this->transaction);

      foreach ($sejour->_fusion as $group_id => $infos_fus) {
        if ($receiver->group_id != $group_id) {
          continue;
        }

        $sejour1_nda = $sejour->_NDA = $infos_fus["sejour1_nda"];

        /** @var CSejour $sejour_eliminee */
        $sejour_eliminee = $infos_fus["sejourElimine"];
        $sejour2_nda     = $sejour_eliminee->_NDA = $infos_fus["sejour2_nda"];

        // Cas 2 NDA : Suppression du deuxi�me s�jour
        if ($sejour1_nda && $sejour2_nda) {
          // Dans la pr�-admission : A38
          if ($sejour_eliminee->_etat == "preadmission") {
            $code = "A38";
          }

          // En admission / cl�tur� : A11
          else {
            $code = "A11";
          }

          $sejour_eliminee->_receiver = $receiver;
          $sejour_eliminee->loadRefPatient();

          if (!$this->isMessageSupported($this->transaction, $this->message, $code, $receiver)) {
            return;
          }

          $this->createMovement($code, $sejour_eliminee);

          $this->sendITI($this->profil, $this->transaction, $this->message, $code, $sejour_eliminee);

          continue;
        }
      }
    }
  }

  /**
   * Trigger after event merge
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function onAfterMerge(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    if ($mbObject instanceof CSejour) {
      $sejour = $mbObject;

      $receiver = $sejour->_receiver;
      $receiver->getInternationalizationCode($this->transaction);

      foreach ($sejour->_fusion as $group_id => $infos_fus) {
        if ($receiver->group_id != $group_id) {
          continue;
        }

        $sejour1_nda = $sejour->_NDA = $infos_fus["sejour1_nda"];

        /** @var CSejour $sejour_eliminee */
        $sejour_eliminee = $infos_fus["sejourElimine"];
        $sejour2_nda     = $sejour_eliminee->_NDA = $infos_fus["sejour2_nda"];

        // Cas 0 NDA : Aucune notification envoy�e
        if (!$sejour1_nda && !$sejour2_nda) {
          continue;
        }

        // Cas 1 NDA : Pas de message de fusion mais d'une modification de s�jour
        if ($sejour1_nda xor $sejour2_nda) {
          if ($sejour2_nda) {
            $sejour->_NDA = $sejour2_nda;
          }

          $code = $this->getModificationAdmitCode($receiver);
          if (!$this->isMessageSupported($this->transaction, $this->message, $code, $receiver)) {
            return;
          }

          $this->createMovement($code, $sejour);

          $this->sendITI($this->profil, $this->transaction, $this->message, $code, $sejour);
          continue;
        }
      }
    }
  }

  /**
   * Trigger before event delete
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function onBeforeDelete(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }
    
    $receiver = $mbObject->_receiver;
    $receiver->getInternationalizationCode($this->transaction);  

    // Traitement Affectation
    if ($mbObject instanceof CAffectation) {
      $affectation = $mbObject;

      if ($affectation->_no_synchro) {
        return;
      }
      
      $sejour = $affectation->loadRefSejour();
      if (!$sejour->_id || $sejour->_etat == "preadmission") {
        return;
      }

      // Si le group_id du s�jour est diff�rent de celui du destinataire
      if ($sejour->group_id != $receiver->group_id) {
        return;
      }

      /* Annulation de l'affectation dans un service externe */
      $service = $affectation->loadRefService();
      if ($service->externe) {
        // Affectation effectu�e 
        if ($affectation->effectue) {
          $code = "A53";
        }
      }
      else {
        // Annulation d'une affectation
        $code = "A12";
      }
            
      // Cas o� : 
      // * on est l'initiateur du message 
      // * le destinataire ne supporte pas le message
      if ($affectation->_eai_initiateur_group_id || !$this->isMessageSupported($this->transaction, $this->message, $code, $receiver)) {
        return;
      }
            
      $sejour->loadRefPatient();
      $sejour->_receiver = $receiver;

      $this->createMovement($code, $sejour, $affectation);
   
      // Envoi de l'�v�nement
      $this->sendITI($this->profil, $this->transaction, $this->message, $code, $mbObject);
    }  
  }

  /**
   * Trigger after event delete
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function onAfterDelete(CMbObject $mbObject) {
  }
}