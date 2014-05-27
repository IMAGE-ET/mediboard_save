<?php

/**
 * Patient Registry Get Demographics Query
 * A user initiates a query to a patient registry requesting demographic information for a specific patient
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v3EventPRPAIN201307UV02
 * Patient Registry Get Demographics Query
 */
class CHL7v3EventPRPAIN201307UV02 extends CHL7v3EventPRPA implements CHL7EventPRPAST201317UV02 {

  /** @var string */
  public $interaction_id = "IN201307UV02";

  /**
   * Get interaction
   *
   * @return string|void
   */
  function getInteractionID() {
    return "{$this->event_type}_{$this->interaction_id}";
  }

  /**
   * Build IN201307UV02 event
   *
   * @param CPatient $patient Person
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($patient) {
    parent::build($patient);

    $this->dom->dirschemaname = $this->getInteractionID();

    $this->addControlActProcess($patient);

    $this->message = $this->dom->saveXML();

    // Modification de l'�change
    $this->updateExchange();
  }

  /**
   * @see parent::addControlActProcess()
   */
  function addControlActProcess(CPatient $patient) {
    $dom = $this->dom;

    $controlActProcess = parent::addControlActProcess($patient);

    // reasonCode
    $reasonCode = $dom->addElement($controlActProcess, "reasonCode");
    $this->setCode($reasonCode, "TEST_EXST", "1.2.250.1.213.1.1.4.11", "Test d'existence de dossier");

    // queryByParameter
    $queryByParameter = $dom->addElement($controlActProcess, "queryByParameter");

    // queryId
    $queryId = $dom->addElement($queryByParameter, "queryId");
    $this->setII($queryId, $this->_exchange_hl7v3->_id, CMbOID::getOIDFromClass($this->_exchange_hl7v3, $this->_receiver));

    // statusCode
    $statusCode = $dom->addElement($queryByParameter, "statusCode");
    $dom->addAttribute($statusCode, "code", "new");

    // parameterList
    $parameterList = $dom->addElement($queryByParameter, "parameterList");

    // patientIdentifer
    $patientIdentifer = $dom->addElement($parameterList, "patientIdentifier");
    $value = $dom->addElement($patientIdentifer, "value");
    $this->setII($value, $patient->_ref_last_ins->ins, "1.2.250.1.213.1.4.2");

    $dom->addElement($patientIdentifer, "semanticsText", "Patient.id");
  }
}