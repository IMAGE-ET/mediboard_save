<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision:$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CAppUI::requireModuleClass("hprimxml", "evenementspatients");

class CHPrimXMLFusionPatient extends CHPrimXMLEvenementsPatients { 
  var $actions = array(
    'fusion' => "fusion"
  );
  
  function __construct() {        
  	$this->sous_type = "fusionPatient";
  	    
    parent::__construct();
  }
  
  function generateFromOperation($mbPatient, $referent) {  
    $evenementsPatients = $this->documentElement;
    $evenementPatient   = $this->addElement($evenementsPatients, "evenementPatient");
    
    $fusionPatient = $this->addElement($evenementPatient, "fusionPatient");
    $this->addAttribute($fusionPatient, "action", "fusion");
      
    $patient = $this->addElement($fusionPatient, "patient");
    // Ajout du nouveau patient   
    $this->addPatient($patient, $mbPatient, $referent);
      
    $patientElimine = $this->addElement($fusionPatient, "patientElimine");
    // Ajout du patient a eliminer
    $this->addPatient($patientElimine, $mbPatient->_patient_elimine, $referent);
        
    // Traitement final
    $this->purgeEmptyElements();
  }
  
  function getContentsXML() {
    $xpath = new CHPrimXPath($this);

    $query = "/hprim:evenementsPatients/hprim:evenementPatient";

    $evenementPatient = $xpath->queryUniqueNode($query);
    $fusionPatient = $xpath->queryUniqueNode("hprim:fusionPatient", $evenementPatient);

    $data['action'] = $this->getActionEvenement("hprim:fusionPatient", $evenementPatient);

    $data['patient'] = $xpath->queryUniqueNode("hprim:patient", $fusionPatient);
    $data['patientElimine'] = $xpath->queryUniqueNode("hprim:patientElimine", $fusionPatient);

    $data['idSourcePatient'] = $this->getIdSource($data['patient']);
    $data['idCiblePatient']  = $this->getIdCible($data['patient']);
    
    $data['idSourcePatientElimine']= $this->getIdSource($data['patientElimine']);
    $data['idCiblePatientElimine'] = $this->getIdCible($data['patientElimine']);
    
    return $data;
  }
  
  /**
   * Fusion and recording a patient with an IPP in the system
   * @param CHPrimXMLAcquittementsPatients $domAcquittement
   * @param CEchangeHprim $echange_hprim
   * @param CPatient $newPatient
   * @param array $data
   * @return string acquittement 
   **/
  function fusionPatient($domAcquittement, $newPatient, $data) {
    $echange_hprim = $this->_ref_echange_hprim;
    
    // Si CIP
    if (!CAppUI::conf('sip server')) {
      $mbPatientElimine = new CPatient();
      $mbPatient = new CPatient();
     
      $dest_hprim = $echange_hprim->_ref_emetteur;

      // Acquittement d'erreur : identifiants source et cible non fournis pour le patient / patientElimine
      if (!$data['idSourcePatient'] && !$data['idCiblePatient'] && 
			    !$data['idSourcePatientElimine'] && !$data['idCiblePatientElimine']) {
        $messageAcquittement = $domAcquittement->generateAcquittementsPatients("erreur", "E005", null, $newPatient);
        $doc_valid = $domAcquittement->schemaValidate();
				
        $echange_hprim->setAckError($doc_valid, $messageAcquittement, "erreur");
        return $messageAcquittement;
      }
      
      $id400Patient = CIdSante400::getMatch("CPatient", $dest_hprim->_tag_patient, $data['idSourcePatient']);
      if ($mbPatient->load($data['idCiblePatient'])) {
        if ($mbPatient->_id != $id400Patient->object_id) {
          $commentaire = "L'identifiant source fait r�f�rence au patient : $id400Patient->object_id et l'identifiant cible au patient : $mbPatient->_id.";
          return $domAcquittement->generateAcquittementsError("E004", $commentaire, $newPatient);
        }
      } 
      if (!$mbPatient->_id) {
        $mbPatient->load($id400Patient->object_id);
      }
      
      $id400PatientElimine = CIdSante400::getMatch("CPatient", $dest_hprim->_tag_patient, $data['idSourcePatientElimine']);
      if ($mbPatientElimine->load($data['idCiblePatientElimine'])) {
        if ($mbPatientElimine->_id != $id400PatientElimine->object_id) {
          $commentaire = "L'identifiant source fait r�f�rence au patient : $id400PatientElimine->object_id et l'identifiant cible au patient : $mbPatientElimine->_id.";
          return $domAcquittement->generateAcquittementsError("E041", $commentaire, $newPatient);
        }
      }
      if (!$mbPatientElimine->_id) {
        $mbPatientElimine->load($id400PatientElimine->object_id);
      }
      
      if (!$mbPatient->_id || !$mbPatientElimine->_id) {
        $commentaire = !$mbPatient->_id ? "Le patient $mbPatient->_id est inconnu dans Mediboard." : "Le patient $mbPatientElimine->_id est inconnu dans Mediboard.";
        return $domAcquittement->generateAcquittementsError("E012", $commentaire, $newPatient);
      }

      // Passage en trash de l'IPP du patient a �liminer
      $id400PatientElimine->tag = CAppUI::conf('dPpatients CPatient tag_ipp_trash').$dest_hprim->_tag_patient;
      $id400PatientElimine->store();
      
      $messages = array();
      $avertissement = null;
            
      $patientsElimine_array = array($mbPatientElimine);
      $first_patient_id = $mbPatient->_id;

      $checkMerge = $mbPatient->checkMerge($patientsElimine_array);
      // Erreur sur le check du merge
      if ($checkMerge) {
        $commentaire = "La fusion de ces deux patients n'est pas possible � cause des probl�mes suivants : $checkMerge";
        return $domAcquittement->generateAcquittementsError("E010", $commentaire, $newPatient);
      }
      
      if ($msg = $mbPatient->mergeDBFields($patientsElimine_array)) {
        $commentaire = "La fusion des donn�es des patients a �chou� : $msg";
        return $domAcquittement->generateAcquittementsError("E011", $commentaire, $newPatient);
      }
      $mbPatientElimine_id = $mbPatientElimine->_id;
      
      /** @todo mergeDBfields resets the _id */
      $mbPatient->_id = $first_patient_id;
      
      // Notifier les autres destinataires
      $mbPatient->_hprim_initiateur_group_id = $dest_hprim->group_id;
      $mbPatient->_merging = CMbArray::pluck($patientsElimine_array, "_id");
      $msg = $mbPatient->merge($patientsElimine_array);
      
      $codes = array ($msg ? "A010" : "I010");
        
      if ($msg) {
        $avertissement = $msg." ";
      } else {
        $commentaire = "Le patient $mbPatient->_id a �t� fusionn� avec le patient $mbPatientElimine_id.";
      }
        
      $messageAcquittement = $domAcquittement->generateAcquittementsPatients($avertissement ? "avertissement" : "OK", $codes, $avertissement ? $avertissement : substr($commentaire, 0, 4000), $newPatient); 
      $doc_valid = $domAcquittement->schemaValidate();
      $echange_hprim->acquittement_valide = $doc_valid ? 1 : 0;
        
      $echange_hprim->statut_acquittement = $avertissement ? "avertissement" : "OK";
    }
    $echange_hprim->_acquittement = $messageAcquittement;
    $echange_hprim->date_echange = mbDateTime();
    $echange_hprim->setObjectIdClass("CPatient", $data['idCiblePatient'] ? $data['idCiblePatient'] : $newPatient->_id);
    $echange_hprim->store();

    return $messageAcquittement;
  }
}
?>