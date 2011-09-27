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
class CITI31DelegatedHandler extends CIHEDelegatedHandler {
  static $handled = array ("CSejour", "CAffectation");
  protected $profil      = "PAM";
  protected $transaction = "ITI31";
  
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
    if (!in_array(array("create", "store"), $last_log->type)) {
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
      // Simple modification ? 
      return "Z99";
    }
    
    // Cas d'un s�jour en cours (entr�e r�elle)
    if ($sejour->_etat == "encours") {
      // Admission faite
      if ($sejour->fieldModified("entree_reelle")) {
        // Admission hospitalis�
        if ($sejour->type = "comp") {
          return "A01";
        } 
        // Patient externe
        return "A04";
      }
      
      // Modification de l'admission 
      /* @todo AJOUTER LES TESTS EN AMONT */
      // Cas d'une annulation ? 
      if ($sejour->fieldModified("annule", "1")) {
        return "A11";
      }
      
      // Simple modification ? 
      return "Z99";
    }
    
    // Cas d'un s�jour cl�tur� (sortie r�elle)
    if ($sejour->_etat == "cloture") {
      // Sortie r�elle renseign�e
      if ($sejour->fieldModified("sortie_reelle")) {
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