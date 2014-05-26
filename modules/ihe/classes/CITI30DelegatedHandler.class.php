<?php

/**
 * ITI30 Delegated Handler
 *  
 * @category IHE
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

/**
 * Class CITI30DelegatedHandler 
 * ITI30 Delegated Handler
 */
class CITI30DelegatedHandler extends CITIDelegatedHandler {
  /**
   * @var array
   */
  static $handled     = array ("CPatient", "CCorrespondantPatient", "CIdSante400");
  /**
   * @var string
   */
  public $profil      = "PAM";
  /**
   * @var string
   */
  public $message     = "ADT";
  /**
   * @var string
   */
  public $transaction = "ITI30";

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

    /** @var CInteropReceiver $receiver */
    $receiver = $mbObject->_receiver;
    $receiver->getInternationalizationCode($this->transaction);
    
    $eai_initiateur_group_id = $mbObject->_eai_initiateur_group_id;

    $code = null;

    // Cr�ation/MAJ d'un correspondant patient
    if ($mbObject instanceof CCorrespondantPatient) {
      if (!$mbObject->patient_id) {
        return; 
      }
      
      $mbObject                           = $mbObject->loadRefPatient();
      $mbObject->_receiver                = $receiver;
      $mbObject->_eai_initiateur_group_id = $eai_initiateur_group_id;
      
      $code = "A31";
    }
    
    // MAJ de l'IPP du patient
    elseif ($mbObject instanceof CIdSante400) {
      $idex = $mbObject;
      
      // Concerne pas les patients / Pas en mode modification
      if ($idex->object_class != "CPatient" || !$idex->_old->_id) {
        return;
      }

      // Pas un tag IPP
      if ($idex->tag != CPatient::getTagIPP()) {
        return;
      }
     
      // Vraiment une modif de l'idex ?
      if ($idex->id400 == $idex->_old->id400) {
        return;
      }
      
      $code = "A47";
      
      $patient = new CPatient();
      $patient->load($idex->object_id);
      $patient->_receiver = $receiver;
      
      $patient->_patient_elimine = clone $patient;
      
      // Affecte le nouvel IPP au patient
      $patient->_IPP = $idex->id400;
      
      // Affecte l'ancien IPP au "patient �limin�"
      $patient->_patient_elimine->_IPP = $idex->_old->id400;

      if (!$this->isMessageSupported($this->transaction, $this->message, $code, $receiver)) {
        return;
      }
      
      $this->sendITI($this->profil, $this->transaction, $this->message, $code, $patient);
      
      return;
    }

    // Cr�ation/MAJ d'un patient - CPatient
    else {
      switch ($mbObject->_ref_current_log->type) {
        case "create":
          $code = "A28";
          break;
        case "store":
          // Patient li�
          if ($mbObject->fieldModified("patient_link_id")) {
            $code = "A24";
            break;
          }
          
          // Annulation de la liaison avec le patient li�
          if ($mbObject->_old->patient_link_id && !$mbObject->patient_link_id) {
            $code = "A37";
            break;
          }

          if ($receiver->_configs["send_patient_with_visit"]) {
            /** @var CPatient $mbObject */
            $sejour = $mbObject->loadRefsSejours(array("entree_reelle" => "IS NOT NULL"));
            if (count($sejour) < 1) {
              $code = null;
              break;
            }
          }

          if ($receiver->_configs["send_patient_with_current_admit"]) {
            // On charge seulement le s�jour courant pour le patient
            $sejours = $mbObject->getCurrSejour(null, $receiver->group_id);
            if (!$sejours) {
              break;
            }

            $sejour = reset($sejours);
            if (!$sejour->_id) {
              break;
            }

            $mbObject->_ref_sejour = $sejour;
          }

          // Dans tous les autres cas il s'agit d'une modification
          $code = ($receiver->_configs["send_update_patient_information"] == "A08") ? "A08" : "A31";

          break;

        default:
          $code = null;
      }
    }

    if (!$code) {
      return;
    }
    
    $patient = $mbObject;
    if ($patient->_eai_initiateur_group_id || !$this->isMessageSupported($this->transaction, $this->message, $code, $receiver)) {
      return;
    }
     
    if (!$patient->_IPP) {
      // G�n�ration de l'IPP dans le cas de la cr�ation, ce dernier n'�tait pas cr��
      if ($msg = $patient->generateIPP()) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
      }
      
      $IPP = new CIdSante400();
      $IPP->loadLatestFor($patient, $receiver->_tag_patient);
      $patient->_IPP = $IPP->id400;
    }

    // Envoi pas les patients qui n'ont pas d'IPP
    if (!$receiver->_configs["send_all_patients"] && !$patient->_IPP) {
      return;
    }
    
    $this->sendITI($this->profil, $this->transaction, $this->message, $code, $patient);
    
    $patient->_IPP = null;
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
  }

  /**
   * Trigger when merge failed
   *
   * @param CMbObject $mbObject Object
   *
   * @return bool
   */
  function onMergeFailure(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    // On va r�atribuer les idexs en cas de probl�me dans la fusion
    foreach ($mbObject->_fusion as $group_id => $infos_fus) {
      if (!$infos_fus || !array_key_exists("idexs_changed", $infos_fus)) {
        return false;
      }

      foreach ($infos_fus["idexs_changed"] as $idex_id => $tag_name) {
        $idex = new CIdSante400();
        $idex->load($idex_id);

        if (!$idex->_id) {
          continue;
        }

        // R�attribution sur l'objet non supprim�
        $patient_eliminee = $infos_fus["patientElimine"];
        $idex->object_id = $patient_eliminee->_id;

        $idex->tag = $tag_name;
        $idex->last_update = CMbDT::dateTime();
        $idex->store();
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
    
    if ($mbObject instanceof CPatient) {  
      $patient = $mbObject;
      $patient->check();
      $patient->updateFormFields();
      
      $receiver = $mbObject->_receiver; 
      $receiver->getInternationalizationCode($this->transaction);

      foreach ($patient->_fusion as $group_id => $infos_fus) {
        if ($receiver->group_id != $group_id) {
          continue;
        }      

        $patient1_ipp     = $patient->_IPP = $infos_fus["patient1_ipp"];
        
        $patient_eliminee = $infos_fus["patientElimine"];
        $patient2_ipp     = $patient_eliminee->_IPP = $infos_fus["patient2_ipp"];
  
        // Cas 0 IPP : Aucune notification envoy�e
        if (!$patient1_ipp && !$patient2_ipp) {
          continue;
        }
  
        // Cas 1 IPP : Pas de message de fusion mais d'une modification du patient
        if ($patient1_ipp xor $patient2_ipp) {
          if ($patient2_ipp) {
            $patient->_IPP = $patient2_ipp;
          }

          if ($receiver->_configs["send_patient_with_visit"]) {
            /** @var CPatient $mbObject */
            $sejour = $patient->loadRefsSejours(array("entree_reelle" => "IS NOT NULL"));
            if (count($sejour) < 1) {
              $code = null;
              continue;
            }
          }

          if ($receiver->_configs["send_patient_with_current_admit"]) {
            // On charge seulement le s�jour courant pour le patient
            $sejours = $patient->getCurrSejour(null, $receiver->group_id);
            if (!$sejours) {
              continue;
            }

            $sejour = reset($sejours);
            if (!$sejour->_id) {
              continue;
            }

            $patient->_ref_sejour = $sejour;
          }

          $code = ($receiver->_configs["send_update_patient_information"] == "A08") ? "A08" : "A31";
          if (!$this->isMessageSupported($this->transaction, $this->message, $code, $receiver)) {
            return;
          }
            
          $this->sendITI($this->profil, $this->transaction, $this->message, $code, $patient);
          continue;
        }
        
        // Cas 2 IPPs : Message de fusion
        if ($patient1_ipp && $patient2_ipp) {
          $patient->_patient_elimine = $patient_eliminee;
          
          if (!$this->isMessageSupported($this->transaction, $this->message, "A40", $receiver)) {
            return;
          }
          
          $this->sendITI($this->profil, $this->transaction, $this->message, "A40", $patient);
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
    
    return true;
  }

  /**
   * Trigger after event delete
   *
   * @param CMbObject $mbObject Object
   *
   * @return void
   */
  function onAfterDelete(CMbObject $mbObject) {
    if (!$this->isHandled($mbObject)) {
      return false;
    }

    // On g�re seulement la suppression des correspondants patient
    if (!$mbObject instanceof CCorrespondantPatient) {
      return;
    }
    
    $receiver = $mbObject->_receiver;
    $receiver->getInternationalizationCode($this->transaction);
    
    $patient = $mbObject->loadRefPatient();
    $patient->_receiver = $receiver;
    
    $code = ($receiver->_configs["send_update_patient_information"] == "A08") ? "A08" : "A31";
        
    if ($patient->_eai_initiateur_group_id || !$this->isMessageSupported($this->transaction, $this->message, $code, $receiver)) {
      return;
    }
    
    // Envoi pas les patients qui n'ont pas d'IPP
    if (!$receiver->_configs["send_all_patients"] && !$patient->_IPP) {
      return;
    }
    
    $this->sendITI($this->profil, $this->transaction, $this->message, $code, $patient);
  }
}