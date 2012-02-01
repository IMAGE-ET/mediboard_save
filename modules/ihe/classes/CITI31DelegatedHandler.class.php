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
  static $handled        = array ("CSejour", "CAffectation");
  protected $profil      = "PAM";
  protected $transaction = "ITI31";
  
  static $inpatient      = array("comp", "ssr", "psy", "seances", "consult");
  static $outpatient     = array("urg", "ambu", "exte");
  
  static function isHandled(CMbObject $mbObject) {
    return in_array($mbObject->_class, self::$handled);
  }
 
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
      
      if ($sejour->_no_synchro) {
        return;
      }
      
      // Si Serveur
      if (CAppUI::conf('smp server')) {} 
      // Si Client
      else {
        $code = $this->getCode($sejour);
        
        // Cas o� : 
        // * on est l'initiateur du message 
        // * le destinataire ne supporte pas le message
        if ($sejour->_eai_initiateur_group_id || !$this->isMessageSupported($this->transaction, $code, $receiver)) {
          return;
        }

        if (!$sejour->_NDA) {
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
        $this->sendITI($this->profil, $this->transaction, $code, $sejour);
      }
    }
    
     // Traitement Affectation
    if ($mbObject instanceof CAffectation) {
      $affectation = $mbObject;
      $current_log = $affectation->_ref_current_log;
      if (!$current_log || $affectation->_no_synchro || !in_array($current_log->type, array("create", "store"))) {
        return;
      }
      
      $sejour = $affectation->loadRefSejour();
      if ($sejour->_etat == "preadmission") {
        return;
      }

      // Cr�ation d'une affectation
      if ($current_log->type == "create") {
        $code = "A02";
      }
      
      // Modifcation d'une affectation
      if ($current_log->type == "store") {
        $code = "Z99"; 
      }

      // Cas o� : 
      // * on est l'initiateur du message 
      // * le destinataire ne supporte pas le message
      if ($affectation->_eai_initiateur_group_id || !$this->isMessageSupported($this->transaction, $code, $receiver)) {
        return;
      }
      
      $sejour->loadRefPatient();
      $sejour->_receiver = $receiver;
      
      $this->createMovement($code, $sejour, $affectation);
   
      // Envoi de l'�v�nement
      $this->sendITI($this->profil, $this->transaction, $code, $sejour);
    }
  }
  
  function createMovement($code, CSejour $sejour, CAffectation $affectation = null) {
    $insert = in_array($code, CHL7v2SegmentZBE::$actions["INSERT"]);
    $update = in_array($code, CHL7v2SegmentZBE::$actions["UPDATE"]);
    $cancel = in_array($code, CHL7v2SegmentZBE::$actions["CANCEL"]);
    
    $movement = new CMovement();
    // Initialise le mouvement 
    $movement->sejour_id     = $sejour->_id;
    $movement->movement_type = $sejour->getMovementType();

    $current_log = $sejour->loadLastLog();
  
    if ($affectation) {
      $movement->affectation_id = $affectation->_id;  
    }
    
    if ($insert) {
      $movement->original_trigger_code = $code;
    }
    elseif ($update || $cancel) {
      $movement->cancel = 0;               
    }

    $order = "affectation_id DESC";
    $movements = $movement->loadMatchingList($order);
    if (!empty($movements)) {
      $movement = reset($movements);
    }
    
    if ($cancel) {
      $movement->cancel = 1;
    }
      
    $movement->store();
    
    return $sejour->_ref_hl7_movement = $movement;
  }
  
  function getCode(CSejour $sejour) {
    $current_log = $sejour->loadLastLog();
    if (!in_array($current_log->type, array("create", "store"))) {
      return null;
    }
    
    $sejour->loadOldObject();
    // Cas d'une pr�-admission
    if ($sejour->_etat == "preadmission") {
      // Cr�ation d'une pr�-admission
      if ($current_log->type == "create") {
        return "A05";
      } 
      // Modification d'une pr�-admission
      // Cas d'une annulation ? 
      if ($sejour->fieldModified("annule", "1")) {
        return "A38";
      }

      // Annulation de l'admission
      if ($sejour->_old->entree_reelle && !$sejour->entree_reelle) {
        return "A11";
      }
      
      // Simple modification ? 
      return "Z99";
    }
    
    // Cas d'un s�jour en cours (entr�e r�elle)
    if ($sejour->_etat == "encours") {
      // Admission faite
      if ($sejour->fieldModified("entree_reelle") && !$sejour->_old->entree_reelle) {
        // Patient externe
        if (in_array($sejour->type, self::$outpatient)) {
          return "A04";
        } 
        // Admission hospitalis�
        return "A01";
      }
      
      // Modification de la sortie (date de sortie, mode de sortie)
      /* @todo _sortie_autorisee ? */
      //if ($sejour->fieldModified("mode_sortie")) {
      //  return "A16";
      //}
      
      // Cas d'une mutation ? 
      /*if ($sejour->fieldModified("service_entree_id")) {
        return "A02";
      }
      
      // Annulation de la mutation ? 
      if ($sejour->fieldModified("service_entree_id", "")) {
        return "A12";
      }*/
      
      // Bascule externe devient hospitalis� (outpatient > inpatient)
      if ($sejour->fieldModified("type") 
        && (in_array($sejour->type, self::$inpatient)) 
        && in_array($sejour->_old->type, self::$outpatient)) {
        return "A06";
      }
      
      // Bascule d'hospitalis� � externe (inpatient > outpatient)
      if ($sejour->fieldModified("type") 
        && (in_array($sejour->type, self::$outpatient)) 
        && in_array($sejour->_old->type, self::$inpatient)) {
        return "A07";
      }
      
      // Changement du m�decin responsable
      if ($sejour->fieldModified("praticien_id")) {
        return "A54";
      }
      
      // Annulation du m�decin responsable
      if ($sejour->fieldModified("praticien_id") && 
         ($sejour->praticien_id != $sejour->_old->praticien_id)) {
        return "A55";
      }
      
      // R�attribution dossier administratif
      if ($sejour->fieldModified("patient_id")) {
        return "A44";
      }
      
      /* @todo Changement d'UF M�dicale */
      
      /* @todo Changement d'UF de Soins */
      
      // Cas d'une annulation
      if ($sejour->fieldModified("annule", "1")) {
        return "A11";
      }
      
      // Annulation de la sortie r�elle
      if ($sejour->_old->sortie_reelle && !$sejour->sortie_reelle) {
        return "A13";
      }
      
      // Simple modification ? 
      return "Z99";
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
      
      // Simple modification ? 
      return "Z99";
    }
  }
  
  

  function onBeforeMerge(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    
  }
  
  function onAfterMerge(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    
  }  
}
?>