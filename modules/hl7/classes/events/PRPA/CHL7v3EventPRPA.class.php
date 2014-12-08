<?php

/**
 * Patient Administration HL7v3
 *
 * @category HL7
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

/**
 * Class CHL7v3EventPRPA
 * Patient Administration HL7v3
 */
class CHL7v3EventPRPA extends CHL7v3Event implements CHL7EventPRPA {
  public $_event_name;

  /**
   * Construct
   *
   * @return \CHL7v3EventPRPA
   */
  function __construct() {
    parent::__construct();

    $this->event_type = "PRPA";
  }

  /**
   * Build event
   *
   * @param CMbObject $object Object
   *
   * @see parent::build()
   *
   * @return void
   */
  function build($object) {
    parent::build($object);

    // Header
    $this->addHeader();

    // Receiver
    $this->addReceiver();

    // Sender
    $this->addSender();
  }

  /**
   * Get interaction
   *
   * @return string|void
   */
  function getInteractionID() {
  }

  /**
   * Add header
   *
   * @return void
   */
  function addHeader() {
    $dom            = $this->dom;

    $root = $dom->addElement($dom, $this->getInteractionID());
    $dom->addNameSpaces();

    // id
    $id = $dom->addElement($root, "id");
    $this->setII($id, $this->_exchange_hl7v3->_id, CMbOID::getOIDFromClass($this->_exchange_hl7v3, $this->_receiver));

    // creationTime
    $creationTime = $dom->addElement($root, "creationTime");
    $dom->addAttribute($creationTime, "value", CHL7v3MessageXML::dateTime());

    // interactionId
    $interactionId = $dom->addElement($root, "interactionId");
    $this->setII($interactionId, $this->getInteractionID(), "2.16.840.1.113883.1.6");

    // processingCode
    $processingCode = $dom->addElement($root, "processingCode");
    $instance_role  = CAppUI::conf("instance_role") == "prod" ? "P" : "D";
    $dom->addAttribute($processingCode, "code", $instance_role);

    // processingModeCode
    $processingModeCode = $dom->addElement($root, "processingModeCode");
    $dom->addAttribute($processingModeCode, "code", "T");

    // acceptAckCode
    $acceptAckCode = $dom->addElement($root, "acceptAckCode");
    $dom->addAttribute($acceptAckCode, "code", "AL");
  }

  /**
   * Add receiver
   *
   * @return void
   */
  function addReceiver() {
    $dom  = $this->dom;
    $root = $dom->documentElement;

    $receiver = $dom->addElement($root, "receiver");
    $this->setTypeCode($receiver, "RCV");

    $this->addDevice($receiver, $this->_receiver);
  }

  /**
   * Add sender
   *
   * @return void
   */
  function addSender() {
    $dom  = $this->dom;
    $root = $dom->documentElement;

    $sender = $dom->addElement($root, "sender");
    $this->setTypeCode($sender, "SND");

    $this->addDevice($sender);
  }

  /**
   * Add device
   *
   * @param DOMNode       $elParent Parent element
   * @param CInteropActor $actor    Actor
   *
   * @return void
   */
  function addDevice(DOMNode $elParent, CInteropActor $actor = null) {
    $dom = $this->dom;

    // device
    $device = $dom->addElement($elParent, "device");
    $dom->addAttribute($device, "classCode", "DEV");
    $dom->addAttribute($device, "determinerCode", "INSTANCE");

    // id
    $id = $dom->addElement($device, "id");
    $dom->addAttribute($id, "root", $actor ? $actor->OID : CAppUI::conf("mb_oid"));

    // softwareName
    $dom->addElement($device, "softwareName", $actor ? $actor->nom : CAppUI::conf("mb_id"));
  }

  /**
   * Add control act process
   *
   * @return DOMElement
   */
  function addControlActProcess() {
    $dom  = $this->dom;
    $root = $dom->documentElement;

    $controlActProcess = $dom->addElement($root, "controlActProcess");
    $dom->addAttribute($controlActProcess, "classCode", "CACT");
    $dom->addAttribute($controlActProcess, "moodCode", "EVN");

    return $controlActProcess;
  }

  /**
   * Add subject of
   *
   * @param DOMNode $elParent      Parent element
   * @param string  $code          Code
   * @param string  $codeSystem    Code system
   * @param string  $displayName   Display name
   * @param string  $value         Value
   * @param bool    $effectiveTime Effective time
   *
   * @return void
   */
  function addSubjectOf(DOMNode $elParent, $code, $codeSystem, $displayName, $value, $effectiveTime = false) {
    $dom = $this->dom;

    $subjectOf = $dom->addElement($elParent, "subjectOf");
    $this->setTypeCode($subjectOf, "SBJ");

    $administrativeObservation = $dom->addElement($subjectOf, "administrativeObservation");
    $this->setClassMoodCode($administrativeObservation, "OBS", "EVN");

    $code_elt = $dom->addElement($administrativeObservation, "code");
    $this->setCode($code_elt, $code, $codeSystem, $displayName);

    if ($effectiveTime) {
      $date = $this->getDateToFormatCDA(CMbDT::date());
      $effectiveTime = $dom->addElement($administrativeObservation, "effectiveTime");
      $dom->addAttribute($effectiveTime, "value", $date);
    }

    $value_elt = $dom->addElement($administrativeObservation, "value");
    $value_elt->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
    $value_elt->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'xsi:type', 'BL');
    $dom->addAttribute($value_elt, "value", $value);
  }

  /**
   * Transforme une chaine date au format time CDA
   *
   * @param String $dateTime String
   *
   * @return string
   */
  function setUtcToTime($dateTime) {
    $timezone_local = new DateTimeZone(CAppUI::conf("timezone"));
    $timezone_utc = new DateTimeZone("UTC");

    $date = new DateTime($dateTime, $timezone_utc);
    $date->setTimezone($timezone_local);

    return $date->format("d-m-Y H:i");
  }

  /**
   * Transforme une chaine date au format date CDA
   *
   * @param String  $date                 Date
   * @param Boolean $transform_lunar_date Apply the algo for change lunar date to date
   *
   * @return string
   */
  function getDateToFormatCDA($date, $transform_lunar_date = false) {
    if (!$date) {
      return null;
    }

    list($year, $month, $day) = explode("-", $date);

    if ($transform_lunar_date && !checkdate($month, $day, $year)) {
      if ($month > 12) {
        $month = 12;
      }
      $last_day = date("t", strtotime("$year-$month-01"));
      if ($day > $last_day) {
        $day = $last_day;
      }
    }

    return $year.$month.$day;
  }

  /**
   * Set class code
   *
   * @param DOMNode $elParent  Parent element
   * @param string  $classCode Class code
   *
   * @return void
   */
  function setClassCode(DOMNode $elParent, $classCode) {
    $dom = $this->dom;

    $dom->addAttribute($elParent, "classCode", $classCode);
  }

  /**
   * Set class code
   *
   * @param DOMNode $elParent Parent element
   * @param string  $typeCode Type code
   *
   * @return void
   */
  function setTypeCode(DOMNode $elParent, $typeCode) {
    $dom = $this->dom;

    $dom->addAttribute($elParent, "typeCode", $typeCode);
  }

  /**
   * Set class and determiner code
   *
   * @param DOMNode $elParent       Parent element
   * @param string  $classCode      Class code
   * @param string  $determinerCode Determiner code
   *
   * @return void
   */
  function setClassDeterminerCode(DOMNode $elParent, $classCode, $determinerCode) {
    $dom = $this->dom;

    $this->setClassCode($elParent, $classCode);
    $dom->addAttribute($elParent, "determinerCode", $determinerCode);
  }

  /**
   * Set II
   *
   * @param DOMNode $elParent  Parent element
   * @param string  $extension Extension
   * @param string  $root      Root
   *
   * @return void
   */
  function setII(DOMNode $elParent, $extension, $root) {
    $dom = $this->dom;

    $dom->addAttribute($elParent, "extension", $extension);
    $dom->addAttribute($elParent, "root", $root);
  }

  /**
   * Set code
   *
   * @param DOMNode $elParent    Parent element
   * @param string  $code        Code
   * @param string  $codeSystem  Code system
   * @param string  $displayName Display name
   *
   * @return void
   */
  function setCode(DOMNode $elParent, $code, $codeSystem, $displayName = null) {
    $dom = $this->dom;

    $dom->addAttribute($elParent, "code", $code);
    $dom->addAttribute($elParent, "codeSystem", $codeSystem);

    if ($displayName) {
      $dom->addAttribute($elParent, "displayName", $displayName);
    }
  }

  /**
   * Set class and mood code
   *
   * @param DOMNode $elParent  Parent element
   * @param string  $classCode Class code
   * @param string  $moodCode  Mood code
   *
   * @return void
   */
  function setClassMoodCode(DOMNode $elParent, $classCode, $moodCode) {
    $dom = $this->dom;

    $dom->addAttribute($elParent, "classCode", $classCode);
    $dom->addAttribute($elParent, "moodCode", $moodCode);
  }

  /**
   * Set qualifier
   *
   * @param DOMNode $elParent  Parent element
   * @param string  $qualifier Qualifier
   *
   * @return void
   */
  function setQualifier(DOMNode $elParent, $qualifier) {
    $dom = $this->dom;

    $dom->addAttribute($elParent, "qualifier", $qualifier);
  }

  /**
   * Add value
   *
   * @param DOMNode $elParent Parent element
   * @param string  $value    Value
   * @param string  $use      Use value
   *
   * @return void
   */
  function addValue(DOMNode $elParent, $value = null, $use = null) {
    $dom = $this->dom;

    if (!$value) {
      return;
    }

    if ($use) {
      $dom->addAttribute($elParent, "use", $use);
    }

    $dom->addAttribute($elParent, "value", $value);
  }

  /**
   * Add name
   *
   * @param DOMNode  $elParent Parent element
   * @param CPatient $patient  Patient
   *
   * @return void
   */
  function addName(DOMNode $elParent, CPatient $patient) {
    $dom  = $this->dom;

    $name = $dom->addElement($elParent, "name");

    $civilite = null;
    if ($patient->civilite == "m" || $patient->civilite == "mme" || $patient->civilite == "mlle") {
      $civilite = ucfirst($patient->civilite);
    }
    else {
      $civilite = $patient->sexe == "m" ? "M" : "Mme";
    }
    $dom->addElement($name, "prefix", $civilite);

    if ($patient->_p_maiden_name) {
      $family = $dom->addElement($name, "family", $patient->_p_last_name);
      $this->setQualifier($family, "SP");

      $family = $dom->addElement($name, "family", $patient->_p_maiden_name);
      $this->setQualifier($family, "BR");
    }
    else {
      $family = $dom->addElement($name, "family", $patient->_p_last_name);
      $this->setQualifier($family, "SP");
    }

    $dom->addElement($name, "given", $patient->_p_first_name);

    if ($patient->prenom_2) {
      $dom->addElement($name, "given", $patient->prenom_2);
    }
    if ($patient->prenom_3) {
      $dom->addElement($name, "given", $patient->prenom_3);
    }
    if ($patient->prenom_4) {
      $dom->addElement($name, "given", $patient->prenom_4);
    }
  }

  /**
   * Add Telecom
   *
   * @param DOMNode  $elParent Parent element
   * @param CPatient $patient  Patient
   *
   * @return void
   */
  function addTelecom(DOMNode $elParent, CPatient $patient) {
    $dom = $this->dom;

    $patientPhoneNumber       = $patient->_p_phone_number;
    $patientMobilePhoneNumber = $patient->_p_mobile_phone_number;
    $patientEmail             = $patient->_p_email;

    $telecom = $dom->addElement($elParent, "telecom");
    $this->addValue($telecom, ($patientPhoneNumber ? "tel:$patientPhoneNumber" : null), "HP");

    $telecom = $dom->addElement($elParent, "telecom");
    $this->addValue($telecom, ($patientMobilePhoneNumber ? "tel:$patientMobilePhoneNumber" : null), "MC");

    $telecom = $dom->addElement($elParent, "telecom");
    $this->addValue($telecom, ($patientEmail ? "mailto:$patientEmail" : null));
  }

  /**
   * Add Adress
   *
   * @param DOMNode  $elParent Parent element
   * @param CPatient $patient  Patient
   *
   * @return void
   */
  function addAdress(DOMNode $elParent, CPatient $patient) {
    $dom = $this->dom;

    $addr = $dom->addElement($elParent, "addr");
    $addresses = preg_split("#[\t\n\v\f\r]+#", $patient->_p_street_address, -1, PREG_SPLIT_NO_EMPTY);
    foreach ($addresses as $_addr) {
      $dom->addElement($addr, "streetAddressLine", $_addr);
    }
    $dom->addElement($addr, "postalCode", $patient->_p_postal_code);
    $dom->addElement($addr, "city", $patient->_p_city);
    $dom->addElement($addr, "country", $patient->_p_country);
  }

  /**
   * Add birthplace
   *
   * @param DOMNode  $elParent Parent element
   * @param CPatient $patient  Patient
   *
   * @return void
   */
  function addBirthPlace(DOMNode $elParent, CPatient $patient) {
    if (!$patient->cp_naissance && !$patient->_pays_naissance_insee && !$patient->lieu_naissance) {
      return;
    }

    $dom = $this->dom;

    $birthplace = $dom->addElement($elParent, "birthPlace");
    $dom->addAttribute($birthplace, "classCode", "BIRTHPL");

    $patient->updateNomPaysInsee();

    $addr = $dom->addElement($birthplace, "addr");
    $dom->addElement($addr, "postalCode", $patient->cp_naissance);
    $dom->addElement($addr, "city", $patient->lieu_naissance);
    $dom->addElement($addr, "country", $patient->_pays_naissance_insee);
  }

  /**
   * Add represented organization
   *
   * @param DOMNode $elParent Parent element
   * @param CGroups $group    Group
   *
   * @return void
   */
  function addRepresentedOrganizationGroup(DOMNode $elParent, CGroups $group) {
    $identifiant = null;
    if ($group->siret) {
      $identifiant = "3$group->siret";
    }

    if ($group->finess) {
      $identifiant = "1$group->finess";
    }

    $this->addRepresentedOrganization($elParent, $identifiant, $group->text);
  }

  /**
   * Add represented organization
   *
   * @param DOMNode $elParent    Parent element
   * @param String  $identifiant Identifiant
   * @param String  $name        Name
   *
   * @return void
   */
  function addRepresentedOrganization(DOMNode $elParent, $identifiant, $name) {
    $dom = $this->dom;

    $representedOrganization = $dom->addElement($elParent, "representedOrganization");
    $this->setClassDeterminerCode($representedOrganization, "ORG", "INSTANCE");

    $id = $dom->addElement($representedOrganization, "id");
    $this->setII($id, $identifiant, "1.2.250.1.71.4.2.2");

    $dom->addElement($representedOrganization, "name", $name);

    $contactParty = $dom->addElement($representedOrganization, "contactParty");
    $this->setClassCode($contactParty, "CON");
  }

  /**
   * Add a represented contact party
   *
   * @param DOMNode  $elParent Parent element
   * @param CPatient $patient  Patient
   *
   * @return void
   */
  function addContactParty(DOMNode $elParent, CPatient $patient) {
    $dom = $this->dom;
    $contactParty = $dom->addElement($elParent, "contactParty");
    $this->setClassCode($contactParty, "CON");

    $code = $dom->addElement($contactParty, "code");
    $this->setCode($code, "CARTE_SESAM_VITALE", "1.2.250.1.213.4.1.2.5");

    $telecom = $dom->addElement($contactParty, "telecom");
    $dom->addAttribute($telecom, "nullFlavor", "NA");

    $contactPerson = $dom->addElement($contactParty, "contactPerson");
    $name = $dom->addElement($contactPerson, "name");

    $family = $dom->addElement($name, "family", $patient->_vitale_lastname);
    $this->setQualifier($family, "SP");

    $family = $dom->addElement($name, "family", $patient->_vitale_birthname);
    $this->setQualifier($family, "BR");

    $dom->addElement($name, "given", $patient->_vitale_firstname);

    $birthTime = $dom->addElement($contactPerson, "birthTime");
    $date = $patient->_vitale_birthdate;
    if (strlen($date) > 6) {
      list($day, $month, $year, $year2) = str_split($date, 2);
      $date = $day.$month.$year2;
    }
    $dom->addAttribute($birthTime, "value", $date);

  }
}