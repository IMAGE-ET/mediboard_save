<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("webservices", "soap_handler");

/**
 * The CHprimSoapHandler class
 */
class CHprimSoapHandler extends CSoapHandler {
  static $paramSpecs = array(
    "evenementPatient" => array ( 
      "messagePatient" => "string"),
    "evenementServeurActes" => array ( 
      "messageServeurActes" => "string"),
    "evenementPmsi" => array ( 
      "messagePmsi" => "string")
  );

  /**
   * The message contains a collection of administrative notifications of events occurring to patients in a healthcare facility.
   * @param CHPrimXMLEvenementsPatients messagePatient
   * @return CHPrimXMLAcquittementsPatients messageAcquittement 
   **/
  function evenementPatient($messagePatient) {
    $eai_soap_handler = new CEAISoapHandler();
    
    return $eai_soap_handler->event($messagePatient);   
  }
  
  /**
   * Codage CCAM vers les syst�mes de facturation
   * @param CHPrimXMLEvenementServeurActes messageServeurActes
   * @return CHPrimXMLAcquittementsServeurActes messageAcquittement 
   **/
  function evenementServeurActes($messageServeurActes) {
    // Cr�ation de l'�change
    $echange_hprim = new CEchangeHprim();
    $messageAcquittement = null;
    $data = array();
    
    // Gestion de l'acquittement
    $domAcquittement = new CHPrimXMLAcquittementsServeurActes();
    
    $domGetEvenement = new CHPrimXMLEvenementsServeurActes();
    
    try {
      // R�cup�ration des informations du message XML
      $domGetEvenement->loadXML($messageServeurActes);
      $doc_errors = $domGetEvenement->schemaValidate(null, true);
    
      $data = $domGetEvenement->getEnteteEvenementXML("evenementsServeurActes");
      $domAcquittement->identifiant = $data['identifiantMessage'];
      $domAcquittement->destinataire = $data['idClient'];
      $domAcquittement->destinataire_libelle = $data['libelleClient'];
      $domAcquittement->_sous_type_evt = $domGetEvenement->sous_type;
      
      // Acquittement d'erreur d'un document XML recu non valide
      if ($doc_errors !== true) {
        $messageAcquittement = $domAcquittement->generateAcquittements("erreur", "E002", $doc_errors);
        $doc_valid = $domAcquittement->schemaValidate();
        $echange_hprim->date_production = mbDateTime();
        $echange_hprim->emetteur = $data['idClient'] ? $dest_hprim->_id : 0;
        $echange_hprim->destinataire = CAppUI::conf('mb_id');
        $echange_hprim->group_id = CGroups::loadCurrent()->_id;
        $echange_hprim->type = "pmsi";
        $echange_hprim->sous_type = $domGetEvenement->sous_type ? $domGetEvenement->sous_type : "inconnu";
        $echange_hprim->_message = $messageServeurActes;
        $echange_hprim->_acquittement = $messageAcquittement;
        $echange_hprim->statut_acquittement = "erreur";
        $echange_hprim->message_valide = 0;
        $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        $echange_hprim->store();
  
        return $messageAcquittement;
      }
    
      // R�cup�re l'initiateur du message s'il existe
      if (CAppUI::conf('sip server')) {
        $echange_hprim->identifiant_emetteur = intval($data['identifiantMessage']);
        $echange_hprim->loadMatchingObject();
      }
      if (!$echange_hprim->_id) {
        $echange_hprim->emetteur       = $dest_hprim->_id;
        $echange_hprim->destinataire   = CAppUI::conf('mb_id');
        $echange_hprim->group_id       = CGroups::loadCurrent()->_id;
        $echange_hprim->identifiant_emetteur = $data['identifiantMessage'];
        $echange_hprim->type           = "pmsi";
        $echange_hprim->sous_type      = $domGetEvenement->sous_type;
        $echange_hprim->_message        = $messageServeurActes;
        $echange_hprim->message_valide = 1;
      }
      $echange_hprim->date_production = mbDateTime();
      $echange_hprim->store();
  
      $data = array_merge($data, $domGetEvenement->getContentsXML());
      $echange_hprim->id_permanent = $data['idSourceVenue'];
      $messageAcquittement = $domGetEvenement->serveurActes($domAcquittement, $echange_hprim, $data);
     
      return $messageAcquittement;
    } catch (Exception $e) {
      /*$domAcquittement = new CHPrimXMLAcquittementsServeurActes();
      $domAcquittement->identifiant = $data['identifiantMessage'];
      $domAcquittement->destinataire = $data['idClient'];
      $domAcquittement->destinataire_libelle = $data['libelleClient'];
      $domAcquittement->_sous_type_evt = "evenementServeurActe";
      
      $messageAcquittement = $domAcquittement->generateAcquittements("erreur", "E009", $e->getMessage());
      $doc_valid = $domAcquittement->schemaValidate();
      
      $echange_hprim->_acquittement = $messageAcquittement;
      $echange_hprim->statut_acquittement = "erreur";
      $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
      $echange_hprim->date_echange = mbDateTime();
      $echange_hprim->store();*/
      
      return $messageAcquittement;
    }
  }
  
  /**
   * Diagnostics CIM vers les syst�mes de facturation
   * @param CHPrimXMLEvenementsPmsi messagePmsi
   * @return CHPrimXMLAcquittementsPmsi messageAcquittement 
   **/
  function evenementPmsi($messagePmsi) {
    // Cr�ation de l'�change
    $echange_hprim = new CEchangeHprim();
    $messageAcquittement = null;
    $data = array();
    
    // Gestion de l'acquittement
    $domAcquittement = new CHPrimXMLAcquittementsPmsi();
    
    
    
    return $messageAcquittement;
  }
}

CHprimSoapHandler::$paramSpecs += CSoapHandler::$paramSpecs;

?>