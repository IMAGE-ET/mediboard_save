<?php

/**
 * Request find candidates
 *
 * @category SIP
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id: ajax_refresh_exchange.php 15880 2012-06-15 08:14:36Z phenxdesign $
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

// R�cuperation des patients recherch�s
$patient_nom                 = CValue::request("nom");
$patient_prenom              = CValue::request("prenom");
$patient_jeuneFille          = CValue::request("nom_jeune_fille");
$patient_sexe                = CValue::request("sexe");
$patient_adresse             = CValue::request("adresse");
$patient_ville               = CValue::request("ville");
$patient_cp                  = CValue::request("cp");
$patient_day                 = CValue::request("Date_Day");
$patient_month               = CValue::request("Date_Month");
$patient_year                = CValue::request("Date_Year");
$quantity_limited_request    = CValue::request("quantity_limited_request");
$pointer                     = CValue::request("pointer");

$person_id_number            = CValue::request("person_id_number");
$person_namespace_id         = CValue::request("person_namespace_id");
$person_universal_id         = CValue::request("person_universal_id");
$person_universal_id_type    = CValue::request("person_universal_id_type");
$person_identifier_type_code = CValue::request("person_identifier_type_code");

// Donn�es de s�jour
$admit_class                 = CValue::request("admit_class");
$admit_service               = CValue::request("admit_service");
$admit_room                  = CValue::request("admit_room");
$admit_bed                   = CValue::request("admit_bed");
$admit_attending_doctor      = CValue::request("admit_attending_doctor");
$admit_referring_doctor      = CValue::request("admit_referring_doctor");
$admit_consulting_doctor     = CValue::request("admit_consulting_doctor");
$admit_admitting_doctor      = CValue::request("admit_admitting_doctor");

$admit_id_number             = CValue::request("admit_id_number");
$admit_namespace_id          = CValue::request("admit_namespace_id");
$admit_universal_id          = CValue::request("admit_universal_id");
$admit_universal_id_type     = CValue::request("admit_universal_id_type");
$admit_identifier_type_code  = CValue::request("admit_identifier_type_code");

$continue                           = CValue::request("continue");
$domains_returned_namespace_id      = CValue::request("domains_returned_namespace_id");
$domains_returned_universal_id      = CValue::request("domains_returned_universal_id");
$domains_returned_universal_id_type = CValue::request("domains_returned_universal_id_type");
$quantity_limited_request           = CValue::request("quantity_limited_request");
$pointer                            = CValue::request("pointer");

$patient_naissance = null;
if ($patient_year || $patient_month || $patient_day) {
  $patient_naissance = "on";
}

$naissance = null;
if ($patient_naissance == "on") {
  $year  = $patient_year  ? "$patient_year-"  : "____-";
  $month = $patient_month ? "$patient_month-" : "__-";
  $day   = $patient_day   ? "$patient_day"    : "__";

  if ($day != "__") {
    $day = str_pad($day, 2, "0", STR_PAD_LEFT);
  }

  $naissance = $year.$month.$day;
}

$patient = new CPatient();
$patient->nom             = $patient_nom;
$patient->prenom          = $patient_prenom;
$patient->nom_jeune_fille = $patient_jeuneFille;
$patient->naissance       = $naissance;
$patient->adresse         = $patient_adresse;
$patient->ville           = $patient_ville;
$patient->cp              = $patient_cp;
$patient->sexe            = $patient_sexe;

$sejour = new CSejour();
$sejour->_admission = $admit_class;
$sejour->_service   = $admit_service;
$sejour->_chambre   = $admit_room;
$sejour->_lit       = $admit_bed;


$sejour->_praticien_attending  = $admit_attending_doctor; // Praticien
$sejour->_praticien_referring  = $admit_referring_doctor; // Nom du m�decin adressant

$sejour->_praticien_admitting  = $admit_admitting_doctor; // M�decin traitant
$sejour->_praticien_consulting = $admit_consulting_doctor;

$receiver_hl7v2           = new CReceiverHL7v2();
$receiver_hl7v2->actif    = 1;
$receiver_hl7v2->group_id = CGroups::loadCurrent()->_id;
$receivers = $receiver_hl7v2->loadMatchingList();

$profil      = "PDQ";
$transaction = "ITI21";
$message     = "QBP";
$code        = "Q22";

if (
    $admit_class ||
    $admit_service ||
    $admit_room ||
    $admit_bed ||
    $admit_attending_doctor || // praticien_id
    $admit_referring_doctor || // adresse_par_prat_id
    $admit_consulting_doctor || // not used
    $admit_admitting_doctor // not used
) {
  $transaction = "ITI22";
  $code = "ZV1";
}

// PV1.17.2.1 = medecin ayant admis le patient (praticien_id=)
// PV1.8.2.1 = medecin referent (adresse_par)

$ack_data    = null;

// Si on continue pas le pointer est r�initialis�
if (!$continue) {
  $pointer = null;
}

$iti_handler = new CITIDelegatedHandler();
foreach ($receivers as $_receiver) {
  if (!$iti_handler->isMessageSupported($transaction, $message, $code, $_receiver)) {
    continue;
  }

  $patient->_receiver                = $_receiver;
  $patient->_patient_identifier_list = array(
    "person_id_number"            => $person_id_number,
    "person_namespace_id"         => $person_namespace_id,
    "person_universal_id"         => $person_universal_id,
    "person_universal_id_type"    => $person_universal_id_type,
    "person_identifier_type_code" => $person_identifier_type_code
  );
  $patient->_domains_returned  = array(
    "domains_returned_namespace_id"      => $domains_returned_namespace_id,
    "domains_returned_universal_id"      => $domains_returned_universal_id,
    "domains_returned_universal_id_type" => $domains_returned_universal_id_type,
  );
  $patient->_sejour = $sejour;
  $patient->_sejour->_sejour_identifier_list = array(
    "admit_id_number"            => $admit_id_number,
    "admit_namespace_id"         => $admit_namespace_id,
    "admit_universal_id"         => $admit_universal_id,
    "admit_universal_id_type"    => $admit_universal_id_type,
    "admit_identifier_type_code" => $admit_identifier_type_code
  );

  $patient->_quantity_limited_request = $quantity_limited_request;
  $patient->_pointer                  = $pointer;

  // Envoi de l'�v�nement
  $ack_data = $iti_handler->sendITI($profil, $transaction, $message, $code, $patient);
}

$objects = array();
$pointer  = null;

if ($ack_data) {
  if ($code == "Q22") {
    $ack_event = new CHL7v2EventQBPK22();
  }
  else {
    $ack_event = new CHL7v2EventQBPZV2();
  }

  $objects  = $ack_event->handle($ack_data)->handle();

  if (array_key_exists("pointer", $objects)) {
    $pointer = $objects["pointer"];
  }

  unset($objects["pointer"]);
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("patient"                 , $patient);
$smarty->assign("objects"                 , $objects);
$smarty->assign("quantity_limited_request", $quantity_limited_request);
$smarty->assign("pointer"                 , $pointer);

if ($code == "Q22") {
  $smarty->display("inc_list_patients.tpl");
}
else {
  $smarty->display("inc_list_sejours.tpl");
}

CApp::rip();