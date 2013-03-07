<?php

/**
 * Patient utilities EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CEAIPatient
 * Patient utilities EAI
 */

class CEAIPatient extends CEAIMbObject {
  /**
   * Recording the external identifier of the CIP
   * 
   * @param CIdSante400    $id400Patient    Object id400
   * @param CInteropSender $sender          Sender
   * @param int            $idSourcePatient External identifier
   * @param CPatient       $newPatient      Patient
   * 
   * @return null|string null if successful otherwise returns and error message
   */ 
  static function storeID400CIP(CIdSante400 $id400Patient, CInteropSender $sender, $idSourcePatient, CPatient $newPatient) {
    //Param�trage de l'id 400
    $id400Patient->object_class = "CPatient";
    $id400Patient->tag          = $sender->_tag_patient;
    $id400Patient->id400        = $idSourcePatient;
    $id400Patient->object_id    = $newPatient->_id;
    $id400Patient->_id          = null;
    $id400Patient->last_update  = CMbDT::dateTime();

    return $id400Patient->store();
  }
  
  /**
   * Recording the external identifier of the CIP
   * 
   * @param CIdSante400 $IPP          Object id400
   * @param int         $idPatientSIP External identifier
   * 
   * @return void
   */ 
  static function IPPSIPSetting(CIdSante400 $IPP, $idPatientSIP = null) {
    $IPP->object_class = "CPatient";
    $IPP->tag          = CAppUI::conf("sip tag_ipp");
    if ($idPatientSIP) {
      $IPP->object_id  = $idPatientSIP;
    }
  }
  
  /**
   * Recording IPP
   * 
   * @param CIdSante400    $IPP     Object id400
   * @param CPatient       $patient Patient
   * @param CInteropSender $sender  Sender
   * 
   * @return null|string null if successful otherwise returns and error message
   */ 
  static function storeIPP(CIdSante400 $IPP, CPatient $patient, CInteropSender $sender) {
    /* Gestion du num�roteur */
    $group = new CGroups();
    $group->load($sender->group_id);
    $group->loadConfigValues();
    
    // Purge de l'IPP existant sur le patient et on le remplace par le nouveau
    if ($sender->_configs["purge_idex_movements"]) {
      // On charge l'IPP courant du patient
      $patient->loadIPP($sender->group_id);
      
      $ref_IPP = $patient->_ref_IPP;
      
      // Si l'IPP actuel est identique � celui qu'on re�oit on ne fait rien
      if ($ref_IPP->id400 == $IPP->id400) {
        return;
      }
      
      // On passe l'IPP courant en trash
      $patient->trashIPP($ref_IPP);
      
      // On sauvegarde le nouveau
      $IPP->tag          = $sender->_tag_patient;
      $IPP->object_class = "CPatient";
      $IPP->object_id    = $patient->_id;
      $IPP->last_update  = CMbDT::dateTime();
      
      return $IPP->store();  
    }

    // G�n�ration de l'IPP ? 
    // Non
    if (!$group->_configs["sip_idex_generator"]) {
      if (!$IPP->id400) {
        return null;
      }
      
      if ($patient) {
        $IPP->object_id   = $patient->_id;
      }
        
      $IPP->last_update = CMbDT::dateTime();
      
      return $IPP->store();  
    }
    else {
      $IPP_temp = CIdSante400::getMatch("CPatient", $sender->_tag_patient, null, $patient->_id);

      // Pas d'IPP pass�
      if (!$IPP->id400) {
        if ($IPP_temp->_id) {
          return null; 
        }
        
        if (!CIncrementer::generateIdex($patient, $sender->_tag_patient, $sender->group_id)) {
          return CAppUI::tr("CEAIPatient-error-generate-idex");
        }
        
        return null;
      }
      else {
        // Si j'ai d�j� un identifiant
        if ($IPP_temp->_id) {
          // On passe l'IPP courant en trash
          $IPP_temp->tag = CAppUI::conf("dPpatients CPatient tag_ipp_trash").$IPP_temp->tag;
          $IPP_temp->store();
        }
      
        /* @todo Gestion des plages d'identifiants */
        if (($IPP->id400 < $group->_configs["ipp_range_min"]) || ($IPP->id400 > $group->_configs["ipp_range_max"])) {
           return CAppUI::tr("CEAIPatient-idex-not-in-the-range");
        }
        
        $IPP->object_id   = $patient->_id;
        $IPP->last_update = CMbDT::dateTime();
      
        return $IPP->store();  
      }
    }  
  }

  /**
   * Recording patient
   * 
   * @param CPatient       $patient     Patient
   * @param CInteropSender $sender      Sender
   * @param bool           $generateIPP Generate IPP ?
   * 
   * @return null|string null if successful otherwise returns and error message
   */ 
  static function storePatient(CPatient $newPatient, CInteropSender $sender, $generateIPP = false) {   
    // Notifier les autres destinataires autre que le sender
    $newPatient->_eai_initiateur_group_id = $sender->group_id;
    $newPatient->_generate_IPP            = $generateIPP;
    
    if ($msg = $newPatient->store()) {
      if ($sender->_configs["repair_patient"]) {
        $newPatient->repair();
      }
      
      return $newPatient->store();
    }
  }
  
  /**
   * Recording patient
   * 
   * @param CPatient    $newPatient Patient
   * @param CIdSante400 $IPP        Object id400
   * 
   * @return null|string null if successful otherwise returns and error message
   */ 
  static function storePatientSIP(CPatient $newPatient, $IPP) {
    $newPatient->_IPP = $IPP;
    
    return $newPatient->store();
  }
}

?>