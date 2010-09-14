<?php /* $Id: evenementspmsi.class.php 9209 2010-06-15 13:10:19Z lryo $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 9209 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hprimxml", "evenementsserveuractivitepmsi");

class CHPrimXMLEvenementsFraisDivers extends CHPrimXMLEvenementsServeurActivitePmsi {
  function __construct() {
    $this->sous_type = "evenementFraisDivers";
    $this->evenement = "evt_frais_divers";
    
    parent::__construct("evenementFraisDivers", "msgEvenementsFraisDivers");
  }
  
  function generateEnteteMessage() {
    parent::generateEnteteMessage("evenementsFraisDivers");
  }
  
  function generateFromOperation(CSejour $mbSejour) {
    $evenementsFraisDivers = $this->documentElement;

    $evenementFraisDivers = $this->addElement($evenementsFraisDivers, "evenementFraisDivers");

    // Ajout du patient (light)
    $mbPatient =& $mbSejour->_ref_patient;
    $patient = $this->addElement($evenementFraisDivers, "patient");
    $this->addPatient($patient, $mbPatient, false, true);
    
    // Ajout de la venue, c'est-�-dire le s�jour (light)
    $venue = $this->addElement($evenementFraisDivers, "venue");
    $this->addVenue($venue, $mbSejour, false, true);

    // Ajout des frais divers
    $mbSejour->loadRefsFraisDivers();
    foreach ($mbSejour->_ref_frais_divers as $_mb_frais_divers) {
      $_mb_frais_divers->loadRefType();
      $_mb_frais_divers->loadRefExecutant();
      $_mb_frais_divers->loadExecution();
      
      $this->addFraisDivers($evenementFraisDivers, $_mb_frais_divers);
    }
    
    if ($mbSejour->_ref_consultations) {
      foreach ($mbSejour->_ref_consultations as $_consultation) {
        $_consultation->loadRefsFraisDivers();
        foreach ($_consultation->_ref_frais_divers as $_mb_frais_divers) {
          $_mb_frais_divers->loadRefType();
          $_mb_frais_divers->loadExecution();
          $_mb_frais_divers->loadRefExecutant();
          
          $this->addFraisDivers($evenementFraisDivers, $_mb_frais_divers);
        }
      }
    }
        
    // Traitement final
    $this->purgeEmptyElements();
  }
  
  function getContentsXML() {
    $data = array();
    $xpath = new CHPrimXPath($this);   
    
    $evenementFraisDivers = $xpath->queryUniqueNode("/hprim:evenementsFraisDivers/hprim:evenementFraisDivers");
    
    $data['patient']         = $xpath->queryUniqueNode("hprim:patient", $evenementFraisDivers);
    $data['idSourcePatient'] = $this->getIdSource($data['patient']);
    $data['idCiblePatient']  = $this->getIdCible($data['patient']);
    
    $data['venue']           = $xpath->queryUniqueNode("hprim:venue", $evenementFraisDivers);
    $data['idSourceVenue']   = $this->getIdSource($data['venue']);
    $data['idCibleVenue']    = $this->getIdCible($data['venue']);
    
    return $data; 
  }
}
?>