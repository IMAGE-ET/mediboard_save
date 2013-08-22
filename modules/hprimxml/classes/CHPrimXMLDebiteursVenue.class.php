<?php

/**
 * �v�nement li� aux d�biteurs de la venue
 *
 * @package    Mediboard
 * @subpackage hprimxml
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision: 20171 $
 */

/**
 * Class CHPrimXMLDebiteursVenue
 */
class CHPrimXMLDebiteursVenue extends CHPrimXMLEvenementsPatients { 
  public $actions = array(
    'cr�ation' => "cr�ation",
    'remplacement' => "remplacement",
    'modification' => "modification",
  );

  /**
   * @see parent::__construct
   */
  function __construct() {    
    $this->sous_type = "debiteursVenue";
            
    parent::__construct();
  }

  /**
   * @see parent::generateFromOperation
   */
  function generateFromOperation(CSejour $mbVenue, $referent) {
    $evenementsPatients = $this->documentElement;
    $evenementPatient = $this->addElement($evenementsPatients, "evenementPatient");
    
    $debiteursVenue = $this->addElement($evenementPatient, "debiteursVenue");
    $actionConversion = array (
      "create" => "cr�ation",
      "store"  => "modification",
      "delete" => "suppression"
    );
    $this->addAttribute($debiteursVenue, "action", $actionConversion[$mbVenue->_ref_last_log->type]);
    
    $patient = $this->addElement($debiteursVenue, "patient");
    // Ajout du patient   
    $this->addPatient($patient, $mbVenue->_ref_patient, $referent, true);
    
    $venue = $this->addElement($debiteursVenue, "venue"); 
    // Ajout de la venue   
    $this->addVenue($venue, $mbVenue, $referent, true);

    // Ajout des d�biteurs
    $debiteurs = $this->addElement($debiteursVenue, "debiteurs");
    $this->addDebiteurs($debiteurs, $mbVenue->_ref_patient);
    
    // Traitement final
    $this->purgeEmptyElements();
  }

  /**
   * @see parent::getContentsXML
   */
  function getContentsXML() {
    $xpath = new CHPrimXPath($this);

    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $debiteursVenue = $xpath->queryUniqueNode("hprim:debiteursVenue", $evenementPatient);

    $data['action'] = $this->getActionEvenement("hprim:debiteursVenue", $evenementPatient);

    $data['patient'] = $xpath->queryUniqueNode("hprim:patient", $debiteursVenue);
    $data['venue'] = $xpath->queryUniqueNode("hprim:venue", $debiteursVenue);
    $data['employeurs'] = $xpath->queryUniqueNode("hprim:employeurs", $debiteursVenue);
    $data['debiteurs'] = $xpath->queryUniqueNode("hprim:debiteurs", $debiteursVenue);

    $data['idSourcePatient'] = $this->getIdSource($data['patient']);
    $data['idCiblePatient']  = $this->getIdCible($data['patient']);
    
    $data['idSourceVenue'] = $this->getIdSource($data['venue']);
    $data['idCibleVenue'] = $this->getIdCible($data['venue']);
    
    return $data;
  }
  
  /**
   * Gestion des d�biteurs d'une venue de patient
   *
   * @param CHPrimXMLAcquittementsPatients $dom_acq    Acquittement
   * @param CPatient                       $newPatient Patient
   * @param array                          $data       Datas
   *
   * @return CHPrimXMLAcquittementsPatients $msgAcq 
   **/
  function debiteursVenue($dom_acq, $newPatient, $data) {
    $echg_hprim = $this->_ref_echange_hprim;
    
    // Traitement du patient
    $domEnregistrementPatient = new CHPrimXMLEnregistrementPatient();
    $domEnregistrementPatient->_ref_echange_hprim = $echg_hprim;
    $msgAcq = $domEnregistrementPatient->enregistrementPatient($dom_acq, $newPatient, $data);
    if ($echg_hprim->statut_acquittement != "OK") {
      return $msgAcq;
    }
    
    $dom_acq = new CHPrimXMLAcquittementsPatients();
    $dom_acq->_identifiant_acquitte = $data['identifiantMessage'];
    $dom_acq->_sous_type_evt        = $this->sous_type;
    $dom_acq->_ref_echange_hprim    = $echg_hprim;

    $codes = array();
    $avertissement = $commentaire = null;

    // Si CIP
    if (!CAppUI::conf('sip server')) { 
      $sender = $echg_hprim->_ref_sender;
      
      // Mapping des mouvements
      $newPatient = $this->mappingDebiteurs($data['debiteurs'], $newPatient);
      $newPatient->repair();
      
      $msgPatient  = CEAIPatient::storePatient($newPatient, $sender);
      $commentaire = CEAIPatient::getComment($newPatient);
      
      $codes = array ($msgPatient ? "A003" : "I002");
      
      if ($msgPatient) {
        $avertissement = $msgPatient." ";
      }
    }

    return $echg_hprim->setAck($dom_acq, $codes, $avertissement, $commentaire, $newPatient);
  }
}

