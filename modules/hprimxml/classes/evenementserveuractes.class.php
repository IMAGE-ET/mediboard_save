<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hprimxml", "evenementsserveuractivitepmsi");

class CHPrimXMLEvenementsServeurActes extends CHPrimXMLEvenementsServeurActivitePmsi {
  function __construct() {
    $this->sous_type = "evenementServeurActe";
    $this->evenement = "evt_serveuractes";
    
    parent::__construct("serveurActes", "msgEvenementsServeurActes");
  }

  function generateEnteteMessage() {
    parent::generateEnteteMessage("evenementsServeurActes");
  }
  
  function generateFromOperation($mbOp) {
    $evenementsServeurActes = $this->documentElement;

    $evenementServeurActe = $this->addElement($evenementsServeurActes, "evenementServeurActe");
    $this->addDateTimeElement($evenementServeurActe, "dateAction");

    // Ajout du patient
    $mbPatient =& $mbOp->_ref_sejour->_ref_patient;
    $patient = $this->addElement($evenementServeurActe, "patient");
    $this->addPatient($patient, $mbPatient, false, true);
    
    // Ajout de la venue, c'est-�-dire le s�jour
    $mbSejour =& $mbOp->_ref_sejour;
    $venue = $this->addElement($evenementServeurActe, "venue");
    $this->addVenue($venue, $mbSejour, null, true);
    
    // Ajout de l'intervention
    $intervention = $this->addElement($evenementServeurActe, "intervention");
    $this->addIntervention($intervention, $mbOp);
    
    // Ajout des actes CCAM
    $actesCCAM = $this->addElement($evenementServeurActe, "actesCCAM");
    foreach ($mbOp->_ref_actes_ccam as $mbActe) {
      if ((CAppUI::conf("dPpmsi transmission_actes") == "signature") && (!$mbActe->signe || $mbActe->sent)) {
        continue;
      }
      $this->addActeCCAM($actesCCAM, $mbActe, $mbOp);
    }

    // Traitement final
    $this->purgeEmptyElements();
  }
  
  function getContentsXML() {
    $data = array();
    $xpath = new CHPrimXPath($this);   
    
    $evenementServeurActe = $xpath->queryUniqueNode("/hprim:evenementsServeurActes/hprim:evenementServeurActe");
    
    $data['patient']         = $xpath->queryUniqueNode("hprim:patient", $evenementServeurActe);
    $data['idSourcePatient'] = $this->getIdSource($data['patient']);
    $data['idCiblePatient']  = $this->getIdCible($data['patient']);
    
    $data['venue']           = $xpath->queryUniqueNode("hprim:venue", $evenementServeurActe);
    $data['idSourceVenue']   = $this->getIdSource($data['venue']);
    $data['idCibleVenue']    = $this->getIdCible($data['venue']);
    
    $data['intervention']         = $xpath->queryUniqueNode("hprim:intervention", $evenementServeurActe);
    $data['idSourceIntervention'] = $this->getIdSource($data['intervention'], false);
    $data['idCibleIntervention']  = $this->getIdCible($data['intervention'], false);
    
    $data['actesCCAM']         = $xpath->queryUniqueNode("hprim:actesCCAM", $evenementServeurActe);  
    $data['idSourceActesCCAM'] = $this->getIdSource($data['actesCCAM'], false);
    $data['idCibleActesCCAM']  = $this->getIdCible($data['actesCCAM'], false);
    
    return $data; 
  }
  
  /**
   * Enregistrement des actes CCAM
   * @param CHPrimXMLAcquittementsServeurActes $domAcquittement
   * @param CEchangeHprim $echange_hprim
   * @param array $data
   * @return CHPrimXMLAcquittementsServeurActes $messageAcquittement 
   **/
  function serveurActes($domAcquittement, &$echange_hprim, $data) {
    $messageAcquittement = null;
    
     // Si pas Serveur d'Actes
    if (!CAppUI::conf('dPpmsi server')) { 
      $dest_hprim = new CDestinataireHprim();
      $dest_hprim->nom = $data['idClient'];
      $dest_hprim->loadMatchingObject();
      
      $avertissement = null;
      
      // R�cup�ration de l'�l�ment patient du message
      $elPatient  = $data['patient'];
      
      // Mapping actes CCAM
      $actesCCAM = $this->mappingActesCCAM($data);
          
      // Acquittement d'erreur : identifiants source du patient / s�jour non fournis
      if (!$data['idSourcePatient'] || !$data['idSourceVenue']) {
        $messageAcquittement = $domAcquittement->generateAcquittementsServeurActivitePmsi("err", "E206", $actesCCAM, $elPatient);
        $doc_valid = $domAcquittement->schemaValidate();
        
        $echange_hprim->setAckError($doc_valid, $messageAcquittement, "err");
        return $messageAcquittement;
      }
      
      // IPP non connu => message d'erreur
      $IPP = new CIdSante400();
      $IPP->object_class = "CPatient";
      $IPP->tag = $dest_hprim->_tag_patient;
      $IPP->id400 = $data['idSourcePatient'];

      if(!$IPP->loadMatchingObject()) {
        $messageAcquittement = $domAcquittement->generateAcquittementsServeurActivitePmsi("err", "E013", $actesCCAM, $elPatient);
        $doc_valid = $domAcquittement->schemaValidate();
        
        $echange_hprim->setAckError($doc_valid, $messageAcquittement, "err");
        return $messageAcquittement;    
      }
      
      // Chargement du patient
      $patient = new CPatient();   
      $patient->load($IPP->object_id);
      
      // Num dossier non connu => message d'erreur
      $num_dos = new CIdSante400();
      $num_dos->object_class = "CSejour";
      $num_dos->tag = $dest_hprim->_tag_sejour;
      $num_dos->id400 = $data['idSourceVenue'];
      
      if(!$num_dos->loadMatchingObject()) {mbTrace($num_dos, "num", true);
        $messageAcquittement = $domAcquittement->generateAcquittementsServeurActivitePmsi("err", "E014", $actesCCAM, $elPatient);
        $doc_valid = $domAcquittement->schemaValidate();
        
        $echange_hprim->setAckError($doc_valid, $messageAcquittement, "err");
        return $messageAcquittement;    
      }
      
      // Chargement du s�jour
      $sejour = new CSejour();
      $sejour->load($num_dos->object_id);
      
      // Si patient H'XML est diff�rent du s�jour
      if ($sejour->patient_id != $patient->_id) {
        $messageAcquittement = $domAcquittement->generateAcquittementsServeurActivitePmsi("err", "E015", $actesCCAM, $elPatient);
        $doc_valid = $domAcquittement->schemaValidate();
        
        $echange_hprim->setAckError($doc_valid, $messageAcquittement, "err");
        return $messageAcquittement;    
      }

      // Chargement du patient du s�jour
      $sejour->loadRefPatient();
      
      // R�cup�ration de la date de l'intervention
      $dateInterv = $this->getDateInterv($data['intervention']);
      
      // Chargement des interventions du s�jour
      $sejour->loadRefsOperations();  
      $operation = null;
      foreach ($sejour->_ref_operations as $_operation) {
        if (mbDate($_operation->_datetime)) {
          $operation = $_operation;
        }
      }
      
      /* @FIXME Penser � virer par la suite pour rattacher des actes � un s�jour... */
      if (!$operation) {
        $messageAcquittement = $domAcquittement->generateAcquittementsServeurActivitePmsi("err", "E201", $actesCCAM, $elPatient);
        $doc_valid = $domAcquittement->schemaValidate();
        
        $echange_hprim->setAckError($doc_valid, $messageAcquittement, "err");
        return $messageAcquittement; 
      }     

      $operation->loadRefsActesCCAM();
			$mbActesCCAM = $operation->_ref_actes_ccam;
			
			/*mbTrace($actesCCAM, "actesCCAM", true);
      mbTrace($mbActesCCAM, "mbActesCCAM", true);
      foreach () {
      
      }*/
      $messageAcquittement = $domAcquittement->generateAcquittementsServeurActivitePmsi("ok", "I201", $actesCCAM, $elPatient); 
   }
    
    $echange_hprim->_acquittement = $messageAcquittement;
    $echange_hprim->date_echange = mbDateTime();
    $echange_hprim->setObjectIdClass("CSejour", $data['idCibleVenue']);
    $echange_hprim->store();

    return $messageAcquittement;
  }
}
?>