<?php

/**
 * Mouvement patient
 *
 * @category Hprimxml
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHPrimXMLMouvementPatient
 * Mouvement patient
 */

class CHPrimXMLMouvementPatient extends CHPrimXMLEvenementsPatients { 
  public $actions = array(
    'cr�ation'     => "cr�ation",
    'remplacement' => "remplacement",
    'modification' => "modification",
  );

  /**
   * Construct
   *
   * @return CHPrimXMLMouvementPatient
   */
  function __construct() {    
    $this->sous_type = "mouvementPatient";
            
    parent::__construct();
  }

  /**
   * Generate content message
   *
   * @param CAffectation $affectation Movement
   * @param bool         $referent    Is referring ?
   *
   * @return void
   */
  function generateFromOperation(CAffectation $affectation, $referent) {
    $evenementsPatients = $this->documentElement;
    $evenementPatient   = $this->addElement($evenementsPatients, "evenementPatient");
    
    $mouvementPatient = $this->addElement($evenementPatient, "mouvementPatient");
    $actionConversion = array (
      "create" => "cr�ation",
      "store"  => "modification",
      "delete" => "suppression"
    );
    $affectation->loadLastLog();
    $action = $affectation->_ref_last_log->type ? $affectation->_ref_last_log->type : "create";
    $this->addAttribute($mouvementPatient, "action", $actionConversion[$action]);

    $affectation->loadRefSejour();
    $affectation->_ref_sejour->loadNDA();
    $affectation->_ref_sejour->loadRefPatient();
    $affectation->_ref_sejour->loadRefPraticien();

    $patient = $this->addElement($mouvementPatient, "patient");
    // Ajout du patient   
    $this->addPatient($patient, $affectation->_ref_sejour->_ref_patient, $referent);
    
    $venue = $this->addElement($mouvementPatient, "venue"); 
    // Ajout de la venue   
    $this->addVenue($venue, $affectation->_ref_sejour, $referent);
    
    // Ajout du mouvement (1 seul dans notre cas pas l'historique)
    $mouvements = $this->addElement($mouvementPatient, "mouvements"); 
    $this->addMouvement($mouvements, $affectation);

    // Traitement final
    $this->purgeEmptyElements();
  }

  /**
   * Get content XML
   *
   * @return array
   */
  function getContentsXML() {
    $xpath = new CHPrimXPath($this);

    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $mouvementPatient = $xpath->queryUniqueNode("hprim:mouvementPatient"  , $evenementPatient);

    $data['action']   = $this->getActionEvenement("hprim:mouvementPatient", $evenementPatient);
  
    $data['patient']  = $xpath->queryUniqueNode("hprim:patient", $mouvementPatient);
    $data['idSourcePatient'] = $this->getIdSource($data['patient']);
    $data['idCiblePatient']  = $this->getIdCible($data['patient']);
    
    $data['venue']         = $xpath->queryUniqueNode("hprim:venue", $mouvementPatient);
    $data['idSourceVenue'] = $this->getIdSource($data['venue']);
    $data['idCibleVenue']  = $this->getIdCible($data['venue']);
    
    $data['priseEnCharge'] = $xpath->queryUniqueNode("hprim:priseEnCharge", $mouvementPatient);
    $data['mouvements']    = $xpath->queryUniqueNode("hprim:mouvements"   , $mouvementPatient);
    $data['voletMedical']  = $xpath->queryUniqueNode("hprim:voletMedical" , $mouvementPatient);
    $data['dossierResume'] = $xpath->queryUniqueNode("hprim:dossierResume", $mouvementPatient);
        
    return $data;
  }
  
  /**
   * Fusion and recording a stay with an num_dos in the system
   *
   * @param CHPrimXMLAcquittementsPatients $dom_acq    Acquittement
   * @param CPatient                       $newPatient Patient
   * @param array                          $data       Data
   *
   * @return string acquittement 
   **/
  function mouvementPatient(CHPrimXMLAcquittementsPatients $dom_acq, CPatient $newPatient, $data) {
    $echg_hprim = $this->_ref_echange_hprim;
    $sender = $echg_hprim->_ref_sender;
    $sender->loadConfigValues();

    $this->_ref_sender = $sender;
    
    // Traitement de la venue
    $newVenue        = new CSejour();
    $domVenuePatient = new CHPrimXMLVenuePatient();
    $domVenuePatient->_ref_echange_hprim = $echg_hprim;
    $msgAcq = $domVenuePatient->venuePatient($dom_acq, $newPatient, $data, $newVenue);
    if ($echg_hprim->statut_acquittement != "OK") {
      return $msgAcq;
    }
    
    $dom_acq = new CHPrimXMLAcquittementsPatients();
    $dom_acq->_identifiant_acquitte = $data['identifiantMessage'];
    $dom_acq->_sous_type_evt        = $this->sous_type;
    $dom_acq->_ref_echange_hprim    = $echg_hprim;

    // Mapping venue - cr�ation impossible
    if (!$this->admitFound($newVenue, $data)) {
      return $echg_hprim->setAckError($dom_acq, "E014", null, $newVenue);
    }

    $codes = array();
    $avertissement = $comment = null;

    if (!CAppUI::conf("hprimxml mvtComplet") || CAppUI::conf('smp server')) {
      return $echg_hprim->setAck($dom_acq, $codes, $avertissement, $comment, $newVenue);
    }

    // Mapping des mouvements
    $msgMovement = $this->mappingMouvements($data['mouvements'], $newVenue);

    // Notifier les autres destinataires
    $newVenue->_eai_sender_guid = $sender->_guid;
    $newVenue->store();

    $codes = array ($msgMovement ? "A301" : "I301");

    if ($msgMovement) {
      $avertissement = $msgMovement." ";
    }

    $comment = CEAISejour::getComment($newVenue);

    return $echg_hprim->setAck($dom_acq, $codes, $avertissement, $comment, $newVenue);
  } 
}