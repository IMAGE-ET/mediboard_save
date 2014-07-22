<?php
/**
 * $Id$
 * 
 * @package    Mediboard
 * @subpackage hl7
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version    $Revision$
 */

/**
 * Class CHL7v2MergePersons 
 * Merge persons, message XML HL7
 */
class CHL7v2MergePersons extends CHL7v2MessageXML {
  static $event_codes = array ("A40");

  /**
   * Get data nodes
   *
   * @return array Get nodes
   */
  function getContentNodes() {
    $data = array();

    $exchange_hl7v2 = $this->_ref_exchange_hl7v2;
    $sender         = $exchange_hl7v2->_ref_sender;
    $sender->loadConfigValues();

    foreach ($this->queryNodes("ADT_A40.PATIENT") as $_patient_group) {
      $sub_data["PID"] = $PID = $this->queryNode("PID", $_patient_group);
    
      $sub_data["personIdentifiers"] = $this->getPersonIdentifiers("PID.3", $PID, $sender);
      
      $sub_data["PD1"] = $this->queryNode("PD1", $_patient_group);
      
      $sub_data["MRG"] = $MRG = $this->queryNode("MRG", $_patient_group);
      
      $sub_data["personElimineIdentifiers"] = $this->getPersonIdentifiers("MRG.1", $MRG, $sender);  
      
      $data["merge"][] = $sub_data;
    }

    return $data;
  }

  /**
   * Handle event
   *
   * @param CHL7Acknowledgment $ack        Acknowledgement
   * @param CPatient           $newPatient Person
   * @param array              $data       Nodes data
   *
   * @return null|string
   */
  function handle(CHL7Acknowledgment $ack, CPatient $newPatient, $data) {
    // Traitement du message des erreurs
    $comment = $warning = "";

    $exchange_hl7v2 = $this->_ref_exchange_hl7v2;
    $exchange_hl7v2->_ref_sender->loadConfigValues();
    $sender         = $exchange_hl7v2->_ref_sender;

    foreach ($data["merge"] as $_data_merge) {
      $data = $_data_merge;

      $mbPatient        = new CPatient();
      $mbPatientElimine = new CPatient();

      $patientPI        = CValue::read($data['personIdentifiers'], "PI");
      $patientRI        = CValue::read($data['personIdentifiers'], "RI");

      $patientEliminePI = CValue::read($data['personElimineIdentifiers'], "PI");
      $patientElimineRI = CValue::read($data['personElimineIdentifiers'], "RI");

      // Acquittement d'erreur : identifiants RI et PI non fournis
      if (!$patientRI && !$patientPI || !$patientElimineRI && !$patientEliminePI) {
        return $exchange_hl7v2->setAckAR($ack, "E100", null, $newPatient);
      }

      $idexPatient = CIdSante400::getMatch("CPatient", $sender->_tag_patient, $patientPI);
      if ($mbPatient->load($patientRI)) {
        if ($mbPatient->_id != $idexPatient->object_id) {
          $comment  = "L'identifiant source fait r�f�rence au patient : $idexPatient->object_id";
          $comment .= " et l'identifiant cible au patient : $mbPatient->_id.";
          return $exchange_hl7v2->setAckAR($ack, "E130", $comment, $newPatient);
        }
      }
      if (!$mbPatient->_id) {
        $mbPatient->load($idexPatient->object_id);
      }

      $idexPatientElimine = CIdSante400::getMatch("CPatient", $sender->_tag_patient, $patientEliminePI);
      if ($mbPatientElimine->load($patientElimineRI)) {
        if ($mbPatientElimine->_id != $idexPatientElimine->object_id) {
          $comment  = "L'identifiant source fait r�f�rence au patient : $idexPatientElimine->object_id";
          $comment .= "et l'identifiant cible au patient : $mbPatientElimine->_id.";
          return $exchange_hl7v2->setAckAR($ack, "E131", $comment, $newPatient);
        }
      }
      if (!$mbPatientElimine->_id) {
        $mbPatientElimine->load($idexPatientElimine->object_id);
      }

      if (!$mbPatient->_id || !$mbPatientElimine->_id) {
        $comment = !$mbPatient->_id ?
          "Le patient $mbPatient->_id est inconnu dans Mediboard." : "Le patient $mbPatientElimine->_id est inconnu dans Mediboard.";
        return $exchange_hl7v2->setAckAR($ack, "E120", $comment, $newPatient);
      }

      // Passage en trash de l'IPP du patient a �liminer
      $newPatient->trashIPP($idexPatientElimine);

      $patientsElimine_array = array($mbPatientElimine);
      $first_patient_id = $mbPatient->_id;

      $checkMerge = $mbPatient->checkMerge($patientsElimine_array);
      // Erreur sur le check du merge
      if ($checkMerge) {
        $comment = "La fusion de ces deux patients n'est pas possible � cause des probl�mes suivants : $checkMerge";
        return $exchange_hl7v2->setAckAR($ack, "E121", $comment, $newPatient);
      }

      $mbPatientElimine_id = $mbPatientElimine->_id;

      /** @todo mergePlainFields resets the _id */
      $mbPatient->_id = $first_patient_id;

      // Notifier les autres destinataires
      $mbPatient->_eai_sender_guid = $sender->_guid;
      $mbPatient->_merging = CMbArray::pluck($patientsElimine_array, "_id");
      if ($msg = $mbPatient->merge($patientsElimine_array)) {
        return $exchange_hl7v2->setAckAR($ack, "E103", $msg, $mbPatient);
      }

      $mbPatient->_mbPatientElimine_id = $mbPatientElimine_id;

      $comment = CEAIPatient::getComment($mbPatient, $mbPatientElimine);
    }

    return $exchange_hl7v2->setAckAA($ack, "I103", $comment, $mbPatient);
  }
}
