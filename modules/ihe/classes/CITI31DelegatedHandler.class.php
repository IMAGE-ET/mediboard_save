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
        
        // Envoi de l'�v�nement
        $this->sendITI($this->profil, $this->transaction, $code, $sejour);
      }
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
      if ($sejour->fieldModified("mode_sortie")) {
        return "A16";
      }
      
      // Cas d'une mutation ? 
      if ($sejour->fieldModified("service_entree_id")) {
        return "A02";
      }
      // Annulation de la mutation ? 
      if ($sejour->fieldModified("service_entree_id", "")) {
        return "A12";
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