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
    
    // Traitement Sejour
    if ($mbObject instanceof CSejour) {
      $sejour = $mbObject;
      
      // Si Serveur
      if (CAppUI::conf('smp server')) {} 
      // Si Client
      else {
        $code = $this->getCode($sejour);
        
        if (!$sejour->_NDA) {
          $NDA = new CIdSante400();
          $NDA->loadLatestFor($sejour, $receiver->_tag_sejour);
          
          $group = new CGroups();
          $group->load($receiver->group_id);
          $group->loadConfigValues();
            
          // G�n�ration du NDA ? 
          if (!$NDA->id400 && $group->_configs["smp_idex_generator"]) {
            if (!$NDA = CIncrementer::generateIdex($sejour, $receiver->_tag_sejour, $receiver->group_id)) {
              throw new CMbException("incrementer_undefined");
            }
          }
          
          $sejour->_NDA = $NDA->id400;
        }
        
        $insert = in_array($code, CHL7v2SegmentZBE::$actions["INSERT"]);
        $update = in_array($code, CHL7v2SegmentZBE::$actions["UPDATE"]);
        $cancel = in_array($code, CHL7v2SegmentZBE::$actions["CANCEL"]);
        
        $movement                = new CMovement();
        $movement->object_class  = $mbObject->_class;
        $movement->object_id     = $mbObject->_id;
        $movement->movement_type = $mbObject->getMovementType();
        if ($insert) {
          $movement->original_trigger_code = $code;
          $movement->loadMatchingObject();
        }
        elseif ($update || $cancel) {
          $movement->cancel = 0;
          $movement->loadMatchingObject();
          
          if ($cancel) {
            $movement->cancel = 1;
          }         
        }
        
        $movement->store();

        // Envoi de l'�v�nement
        $this->sendITI($this->profil, $this->transaction, $code, $sejour);
      }
    }
    
     // Traitement Affectation
    if ($mbObject instanceof CAffectation) {
      $affectation = $mbObject;
      
      
    }
  }
  
  function getCode(CSejour $sejour) {
    $last_log = $sejour->loadLastLog();
    if (!in_array($last_log->type, array("create", "store"))) {
      return null;
    }
    
    // Cas d'une pr�-admission
    if ($sejour->_etat == "preadmission") {
      // Cr�ation d'une pr�-admission
      if ($last_log->type == "create") {
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
      if ($sejour->fieldModified("service_entree_id")) {
        return "A02";
      }
      // Annulation de la mutation ? 
      if ($sejour->fieldModified("service_entree_id", "")) {
        return "A12";
      }
      
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
      
      // Annulation de la sortie
      if ($sejour->_old->sortie_reelle && !$sejour->sortie_reelle) {
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