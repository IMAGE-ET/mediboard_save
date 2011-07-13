<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage sip
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */
 
CCanDo::checkAdmin();

if (!CAppUI::conf("sip export_dest")) {
  CAppUI::stepAjax("Aucun destinataire de d�fini pour l'export.", UI_MSG_ERROR);
}

// Si pas de tag patient
if (!CAppUI::conf("dPpatients CPatient tag_ipp")) {
  CAppUI::stepAjax("Aucun tag patient de d�fini.", UI_MSG_ERROR);
}

// Filtre sur les enregistrements
$patient = new CPatient();
$action = CValue::get("action", "start");

// Tous les d�parts possibles
$idMins = array(
  "start"    => "000000",
  "continue" => CValue::getOrSession("idContinue"),
  "retry"    => CValue::getOrSession("idRetry"),
);

$idMin = CValue::first(@$idMins[$action], "000000");
CValue::setSession("idRetry", $idMin);

// Requ�tes
$where = array();
$where[$patient->_spec->key] = "> '$idMin'";

$sip_config = CAppUI::conf("sip");

// Bornes
if ($export_id_min = $sip_config["export_id_min"]) {
  $where[] = $patient->_spec->key." >= '$export_id_min'";
}
if ($export_id_max = $sip_config["export_id_max"]) {
  $where[] = $patient->_spec->key." <= '$export_id_max'";
}

// Comptage
$count = $patient->countList($where);
$max = $sip_config["export_segment"];
$max = min($max, $count);
CAppUI::stepAjax("Export de $max sur $count objets de type 'CPatient' � partir de l'ID '$idMin'", UI_MSG_OK);

// Time limit
$seconds = max($max / 20, 120);
CAppUI::stepAjax("Limite de temps du script positionn� � '$seconds' secondes", UI_MSG_OK);
set_time_limit($seconds);

// Export r�el
$errors = 0;
$patients = $patient->loadList($where, $patient->_spec->key, "0, $max");

$echange = 0;
foreach ($patients as $patient) {
  $patient->loadIPP();
  $patient->loadRefsSejours();
  $patient->_ref_last_log->type = "create";
  $dest_hprim = new CDestinataireHprim();
	$dest_hprim->message = "patients";
  $dest_hprim->loadMatchingObject();
  $dest_hprim->loadConfigValues();
  
  if (!$patient->_IPP) {
    $IPP = new CIdSante400();
    //Param�trage de l'id 400
    $IPP->object_class = "CPatient";
    $IPP->object_id = $patient->_id;
    $IPP->tag = $dest_hprim->_tag_patient;
    $IPP->loadMatchingObject();

    $patient->_IPP = $IPP->id400;
  }

  if ((CAppUI::conf("sip pat_no_ipp") && $patient->_IPP  && ($patient->_IPP != "-")) || 
      (!$dest_hprim->_configs["send_all_patients"] && empty($patient->_ref_sejours))) {
  	continue;
  }

  $domEvenement = new CHPrimXMLEnregistrementPatient();
  $domEvenement->emetteur     = CAppUI::conf('mb_id');
  $domEvenement->destinataire = $dest_hprim->nom;
  $domEvenement->group_id     = $dest_hprim->group_id;
  
  $messageEvtPatient = $domEvenement->generateTypeEvenement($patient);
  $doc_valid = $domEvenement->schemaValidate();
  
  if (!$doc_valid) {
    $errors++;
  	trigger_error("Cr�ation de l'�v�nement patient impossible.", E_USER_WARNING);
    CAppUI::stepAjax("Import de '$patient->_view' �chou�", UI_MSG_WARNING);
  }
  $echange++;
}

// Enregistrement du dernier identifiant dans la session
if (@$patient->_id) {
  CValue::setSession("idContinue", $patient->_id);
  CAppUI::stepAjax("Dernier ID trait� : '$patient->_id'", UI_MSG_OK);
  CAppUI::stepAjax("$echange de cr��s", UI_MSG_OK);
}

CAppUI::stepAjax("Import termin� avec  '$errors' erreurs", $errors ? UI_MSG_WARNING : UI_MSG_OK);

?>