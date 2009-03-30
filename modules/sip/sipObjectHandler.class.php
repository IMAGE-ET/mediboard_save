<?php
/**
 *  @package Mediboard
 *  @subpackage sip
 *  @version $Revision: $
 *  @author Yohann Poiron
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * Class CSipObjectHandler 
 * @abstract Event handler class for CMbObject
 */

class CSipObjectHandler extends CMbObjectHandler {
  static $handled = array ("CPatient");
    
  static function isHandled(CMbObject &$mbObject) {
    return in_array($mbObject->_class_name, self::$handled);
  }
  
  function onStore(CMbObject &$mbObject) {
    if (!$this->isHandled($mbObject)) {
      return;
    }
    
    if (!$mbObject->_ref_last_log) {
      return;
    }
    
    // Si client et traitement HPRIM
    if (isset($mbObject->_coms_from_hprim) && ($mbObject->_coms_from_hprim == 1) && !CAppUI::conf('sip server')) {
      return;
    }
    
    // Si serveur et pas d'IPP sur le patient
    if (isset($mbObject->_no_ipp) && ($mbObject->_no_ipp == 1) && CAppUI::conf('sip server')) {
      return;
    }
      
    $dest_hprim = new CDestinataireHprim();
    
    // Si Serveur
    if (CAppUI::conf('sip server')) {
      $listDest = $dest_hprim->loadList();
            
      foreach ($listDest as $_curr_dest) {
        // Recherche si le patient poss�de un identifiant externe sur le SIP
        $id400 = new CIdSante400();
        //Param�trage de l'id 400
        $id400->object_id = $mbObject->_id;
        $id400->object_class = "CPatient";
        $id400->tag = $_curr_dest->destinataire;
  
        if($id400->loadMatchingObject())
          $mbObject->_id400 = $id400->id400;
        else
          $mbObject->_id400 = null;
  
        $domEvenement = new CHPrimXMLEvenementsPatients();
        $domEvenement->_emetteur = CAppUI::conf('mb_id');
        $domEvenement->_destinataire = $_curr_dest->destinataire;
        $domEvenement->_destinataire_libelle = " ";
        
        $echange_hprim = new CEchangeHprim();
        $echange_hprim->load($mbObject->_hprim_initiator_id);
        
        $initiateur = ($_curr_dest->destinataire == $echange_hprim->emetteur) ? $echange_hprim->_id : null;
        mbTrace("la", "la", true);
        $domEvenement->generateEvenementsPatients($mbObject, true, $initiateur);
      }
    } 
    // Si Client
    else {
      $dest_hprim->type = "sip";
      $dest_hprim->loadMatchingObject();
      
      $domEvenement = new CHPrimXMLEvenementsPatients();
      $domEvenement->_emetteur = CAppUI::conf('mb_id');
      $domEvenement->_destinataire = $dest_hprim->destinataire;
      $messageEvtPatient = $domEvenement->generateEvenementsPatients($mbObject);
  
      if (!$client = CMbSOAPClient::make($dest_hprim->url, $dest_hprim->username, $dest_hprim->password)) {
        trigger_error("Impossible de joindre le destinataire : ".$dest_hprim->url);
      }
      
      // R�cup�re le message d'acquittement apr�s l'execution la methode evenementPatient
      if (null == $acquittement = $client->evenementPatient($messageEvtPatient)) {
        trigger_error("Notification d'evenement patient impossible sur le CIP : ".$dest_hprim->url);
      }
  
      $echange_hprim = new CEchangeHprim();
      $echange_hprim->load($domEvenement->_identifiant);
      $echange_hprim->date_echange = mbDateTime();
      $echange_hprim->acquittement = $acquittement;
      
      $echange_hprim->store();
    }
  }

  function onMerge(CMbObject &$mbObject) {
    $this->onStore($mbObject);
  }
  
  function onDelete(CMbObject &$mbObject) {
  } 
}
?>