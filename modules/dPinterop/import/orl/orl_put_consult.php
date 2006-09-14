<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPinterop
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

$limit = mbGetValueFromGetOrSession("limit", 0);

if ($limit == -1) {
  return;	
}

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

// Param�tres
$freq = "00:15:00";
$freqs = array (
  "00:15:00" => 1,
  "00:30:00" => 2,
  "00:45:00" => 3);

// R�cup�ration des consultations
$sql = "SELECT " .
    "\nimport_consultations1.*, " .
    "\nimport_consultations2.*, " .
    "\nimport_praticiens.mb_id AS prat_mb_id, " .
    "\nimport_patients.mb_id AS patient_mb_id" .
    "\nFROM " .
    "\n`import_consultations1`, " .
    "\n`import_consultations2`, " .
    "\n`import_praticiens`, " .
    "\n`import_patients`" .
    "\nWHERE import_consultations1.chir_id = import_praticiens.praticien_id" .
    "\nAND import_consultations1.consultation1_id = import_consultations2.plageconsult_id" .
    "\nAND import_consultations2.patient_id = import_patients.patient_id" .
    "\nLIMIT $limit, 1000";
$res = db_exec($sql);
$consults = array();
while ($row = db_fetch_object($res)) {
  $consults[] = $row;
}

$nbPlagesCreees = 0;
$nbPlagesChargees = 0;
$nbConsultationsCreees = 0;
$nbConsultationsChargees = 0;

foreach ($consults as $consult) {
  // v�rification de l'existence de la plage
  $plage = new CPlageconsult();
  $where = array(
    "chir_id" => "= '$consult->prat_mb_id'",
    "date"    => "= '$consult->date'");
  $plage->loadObject($where);

  if ($plage->plageconsult_id == null) {
    $plage->chir_id = $consult->prat_mb_id;
    $plage->date    = $consult->date;
    $plage->freq    = $freq;
    $plage->debut   = "09:00:00";
    $plage->fin     = "20:00:00";
    $plage->libelle = "Import Cobalys";
    $plage->store();
    $nbPlagesCreees++;
  } else {
    $nbPlagesChargees++;
  }
  
  // Cr�ation de la consultation
  $consultation = new CConsultation;
  $sql = "SELECT consultation.*, plageconsult.*
        FROM consultation, plageconsult
        WHERE consultation.plageconsult_id = plageconsult.plageconsult_id
        AND consultation.patient_id = '$consult->patient_mb_id'
        AND plageconsult.date = '$consult->date'
        AND plageconsult.chir_id = '$consult->prat_mb_id'";
  $result = db_loadlist($sql);
  if(count($result))
    $consultation->load($result[0]["consultation_id"]);
  
  if ($consultation->consultation_id == null) {
    $consultation->plageconsult_id = $plage->plageconsult_id;
    $consultation->patient_id = $consult->patient_mb_id;
    
    $consultation->heure = $consult->debut;
    $consultation->duree = @$freqs[$consult->freq] or 1;
    $consultation->secteur1 = $consult->secteur1;
    $consultation->secteur2 = $consult->secteur2;
    $consultation->chrono = strftime("%Y-%m-%d") > $consult->date ? CC_TERMINE : CC_PLANIFIE;
    $consultation->annule = 0;
    $consultation->paye = strftime("%Y-%m-%d") > $consult->date ? 1 : 0;
    $consultation->cr_valide = 0;
    $consultation->motif = $consult->motif;
    $consultation->rques = $consult->rques;
    $consultation->examen = $consult->examen;
    $consultation->traitement = $consult->traitement;
    $consultation->compte_rendu = null;
    $consultation->premiere = $consult->premiere;
    $consultation->tarif = $consult->tarif;
    $consultation->type_tarif = $consult->type_tarif;

    $consultation->store();
    $nbConsultationsCreees++;
  } else {
    $nbConsultationsChargees++;
  }
   
}

mbTrace($limit, "limit start");
mbTrace($nbPlagesCreees, "nbPlagesCreees");
mbTrace($nbPlagesChargees, "nbPlagesChargees");
mbTrace($nbConsultationsCreees, "nbConsultationsCreees");
mbTrace($nbConsultationsChargees, "nbConsultationsChargees");

$limit = count($consults) ? $limit + 1000 : -1;
mbSetValueToSession("limit", $limit);
header( 'refresh: 0; url=index.php?m=dPinterop&dialog=1&a=put_consult' );
?>