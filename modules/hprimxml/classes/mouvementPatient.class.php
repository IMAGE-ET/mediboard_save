<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hprimxml", "evenementspatients");

class CHPrimXMLMouvementPatient extends CHPrimXMLEvenementsPatients { 
  var $actions = array(
    'cr�ation' => "cr�ation",
    'remplacement' => "remplacement",
    'modification' => "modification",
  );
  
  function __construct() {    
    $this->sous_type = "mouvementPatient";
            
    parent::__construct();
  }
  
  function generateFromOperation(CAffectation $newMouvement, $referent) {  
    $evenementsPatients = $this->documentElement;
    $evenementPatient = $this->addElement($evenementsPatients, "evenementPatient");
    
    $mouvementPatient = $this->addElement($evenementPatient, "mouvementPatient");
    $actionConversion = array (
      "create" => "cr�ation",
      "store"  => "modification",
      "delete" => "suppression"
    );
    $action = $newMouvement->_ref_last_log->type ? $newMouvement->_ref_last_log->type : "create";
    $this->addAttribute($mouvementPatient, "action", $actionConversion[$action]);
    
    $patient = $this->addElement($mouvementPatient, "patient");
    // Ajout du patient   
    $this->addPatient($patient, $newMouvement->_ref_sejour->_ref_patient, $referent);
    
    $venue = $this->addElement($mouvementPatient, "venue"); 
    // Ajout de la venue   
    $this->addVenue($venue, $newMouvement->_ref_sejour, $referent);
    
    // Ajout du mouvement (1 seul dans notre cas pas l'historique)
    $mouvements = $this->addElement($mouvementPatient, "mouvements"); 
    $this->addMouvement($mouvements, $newMouvement, $referent);

    // Traitement final
    $this->purgeEmptyElements();
  }
  
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
   * @param CHPrimXMLAcquittementsPatients $domAcquittement
   * @param CEchangeHprim $echange_hprim.
   * @param CPatient $newPatient
   * @param array $data
   * @return string acquittement 
   **/
  function mouvementPatient($domAcquittement, $newPatient, $data) {
    $echange_hprim = $this->_ref_echange_hprim;
    
    // Traitement de la venue
    $newVenue        = new CSejour();
    $domVenuePatient = new CHPrimXMLVenuePatient();
    $domVenuePatient->_ref_echange_hprim = $echange_hprim;
    $messageAcquittement = $domVenuePatient->venuePatient($domAcquittement, $newPatient, $data, $newVenue);
    if ($echange_hprim->statut_acquittement != "OK") {
      return $messageAcquittement;
    }
    
    $domAcquittement = new CHPrimXMLAcquittementsPatients();
    $domAcquittement->_identifiant_acquitte = $data['identifiantMessage'];
    $domAcquittement->_sous_type_evt        = $this->sous_type;
    $domAcquittement->_ref_echange_hprim    = $echange_hprim;
    
    // Si CIP
    if (!CAppUI::conf('sip server')) { 
      $dest_hprim = $echange_hprim->_ref_emetteur;
      
      $avertissement = null;
      
      // Mapping des mouvements
      $newVenue = $this->mappingMouvements($data['mouvements'], $newVenue);

      // Notifier les autres destinataires
      $newVenue->_hprim_initiateur_group_id = $dest_hprim->group_id;
      $msgVenue = $newVenue->store();
      
      $codes = array ($msgVenue ? "A103" : "I102");
      
      if ($msgVenue) {
        $avertissement = $msgVenue." ";
      } else {
        $newVenue->loadLogs();
        $modified_fields = "";
        if (is_array($newVenue->_ref_last_log->_fields)) {
          foreach ($newVenue->_ref_last_log->_fields as $field) {
            $modified_fields .= "$field \n";
          }
        }
      
        $commentaire = "S�jour modifi�e : $newVenue->_id. Les champs mis � jour sont les suivants : $modified_fields.";
      }
      
      $messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : substr($commentaire, 0, 4000)); 
      $doc_valid = $domAcquittement->schemaValidate();
      $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
      $echange_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
    }
    
    $echange_hprim->_acquittement = $messageAcquittement;
    $echange_hprim->date_echange = mbDateTime();
    $echange_hprim->setObjectIdClass("CSejour", $data['idCibleVenue'] ? $data['idCibleVenue'] : $newVenue->_id);
    $echange_hprim->store();

    return $messageAcquittement;
  } 
}
?>