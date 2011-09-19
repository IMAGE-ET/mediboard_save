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
        $sejour->loadLastLog();
        $last_log = $sejour->_ref_last_log;
        
        // Cas d'une pr�-admission
        if ($sejour->_etat == "preadmission") {
          // Cr�ation d'une pr�-admission
          if ($last_log->type == "create") {
            $code = "A05";
          } 
          // Modification d'une pr�-admission
          elseif ($last_log->type == "store") {
            // Cas d'une annulation ? 
            if ($sejour->fieldModified("annule", "1")) {
              $code = "A38";
            }
            // Simple modification ? 
            else {
              $code = "Z99";
            }
          } 
          // Aucun cas ?
          else {
            $code = null;
          }
        }
        
        // Cas d'un s�jour en cours (entr�e r�elle)
        elseif ($sejour->_etat == "encours") {
          // Admission faite
          if ($sejour->fieldModified("entree_reelle")) {
            // Admission hospitalis�
            if ($sejour->type = "comp") {
              $code = "A01";
            } 
            // Patient externe
            else {
              $code = "A04";
            }
          }
          // Modification de l'admission 
          else {
            /* @todo AJOUTER LES TESTS EN AMONT */
            // Cas d'une annulation ? 
            if ($sejour->fieldModified("annule", "1")) {
              $code = "A11";
            }
            // Simple modification ? 
            else {
              $code = "Z99";
            }
          }
        }
        
        // Cas d'un s�jour cl�tur� (sortie r�elle)
        elseif ($sejour->_etat == "cloture") {
          // Sortie r�elle renseign�e
          if ($sejour->fieldModified("sortie_reelle")) {
            $code = "A03";
          }
          // Modification de l'admission 
          else {
            // Cas d'une annulation ? 
            if ($sejour->fieldModified("annule", "1")) {
              $code = "A13";
            }
            // Simple modification ? 
            else {
              $code = "Z99";
            }
          }
        }
        
        // Envoi de l'�v�nement
        $this->sendITI($this->profil, $this->transaction, $code, $sejour);
      }
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