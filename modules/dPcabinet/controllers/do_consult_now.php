<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $m;

// Permissions ?
//$module = CModule::getInstalled($m);
//$canModule = $module->canDo();
//$canModule->needsEdit();

$prat_id       = CValue::post("prat_id");
$patient_id    = CValue::post("patient_id");
$_operation_id = CValue::post("_operation_id");
$_datetime     = CValue::post("_datetime");

if (!$_datetime || $_datetime == "now") {
  $_datetime = mbDateTime();
}

$sejour = new CSejour();
$sejour->load(CValue::post("sejour_id"));

// Cas des urgences
if ($sejour->type === "urg") {
  if ($_datetime < $sejour->entree || $_datetime > $sejour->sortie) {
    CAppUI::setMsg("La prise en charge doit �tre dans les bornes du s�jour", UI_MSG_ERROR);
    CAppUI::redirect("m=dPurgences");
  }
  
	$sejour->loadRefsConsultations();
	if ($sejour->_ref_consult_atu->_id) {
    CAppUI::setMsg("Patient d�j� pris en charge par un praticien", UI_MSG_ERROR);
    CAppUI::redirect("m=dPurgences");
  }
  
  // Changement de praticien pour le sejour
  if (CAppUI::conf("dPurgences pec_change_prat")) {
    $sejour->praticien_id = $prat_id;
    if($msg = $sejour->store()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
      CAppUI::redirect("m=dPurgences");
    }
  }
}


$chir = new CMediusers;
$chir->load($prat_id);
if(!$chir->_id) {
  CAppUI::setMsg("Vous devez choisir un praticien pour la consultation", UI_MSG_ERROR);
}


$day_now  = mbTransformTime(null, $_datetime, "%Y-%m-%d");
$time_now = mbTransformTime(null, $_datetime, "%H:%M:00");
$hour_now = mbTransformTime(null, $_datetime, "%H:00:00");
$hour_next = mbTime("+1 HOUR", $hour_now);

$plage = new CPlageconsult();
$plageBefore = new CPlageconsult();
$plageAfter = new CPlageconsult();

// Cas ou une plage correspond
$where = array();
$where["chir_id"] = "= '$chir->_id'";
$where["date"]    = "= '$day_now'";
$where["debut"]   = "<= '$time_now'";
$where["fin"]     = "> '$time_now'";
$plage->loadObject($where);

if(!$plage->_id) {
  // Cas ou on a des plage en collision
	$where = array();
	$where["chir_id"] = "= '$chir->_id'";
	$where["date"]    = "= '$day_now'";
	$where["debut"]   = "<= '$hour_now'";
	$where["fin"]     = ">= '$hour_now'";
  $plageBefore->loadObject($where);
	$where["debut"]   = "<= '$hour_next'";
	$where["fin"]     = ">= '$hour_next'";
  $plageAfter->loadObject($where);
  if($plageBefore->_id) {
    if($plageAfter->_id) {
      $plageBefore->fin = $plageAfter->debut;
    } else {
      $plageBefore->fin = max($plageBefore->fin, $hour_next);
    }
    $plage =& $plageBefore;
  } elseif($plageAfter->_id) {
    $plageAfter->debut = min($plageAfter->debut, $hour_now);
    $plage =& $plageAfter;
  } else {
    $plage->chir_id = $chir->_id;
    $plage->date    = $day_now;
    $plage->freq    = "00:".CPlageconsult::$minutes_interval.":00";
    $plage->debut   = $hour_now;
    $plage->fin     = $hour_next;
    $plage->libelle = "automatique";
  }
  $plage->updateFormFields();
  if($msg = $plage->store()) {
    CAppUI::setMsg($msg, UI_MSG_ERROR);
  }
}

$plage->loadRefsFwd();

$ref_chir = $plage->_ref_chir;

$consult = new CConsultation;
$consult->plageconsult_id = $plage->_id;
$consult->sejour_id = $sejour->_id;
$consult->patient_id = $patient_id;
$consult->heure = $time_now;
$consult->arrivee = "$day_now $time_now";
$consult->duree = 1;
$consult->chrono = CConsultation::PATIENT_ARRIVE;
$consult->accident_travail = CValue::post("accident_travail");

// Cas standard
$consult->motif = CValue::post("motif", "Consultation imm�diate");

// Cas des urgences
if ($sejour->type == "urg") {
  // Motif de la consultation
  $consult->motif = "";
  if (CAppUI::conf('dPurgences motif_rpu_view')) {
	  $consult->motif .= "RPU: ";
  }
	$sejour->loadRefRPU();
  $consult->motif.= $sejour->_ref_rpu->diag_infirmier;
} 

if ($msg = $consult->store()) {
  CAppUI::setMsg($msg, UI_MSG_ERROR);
}

CAppUI::setMsg("CConsultation-msg-create", UI_MSG_OK);

if ($ref_chir->isFromType(array("Anesth�siste"))) {
  // Un Anesthesiste a �t� choisi 
  $consultAnesth = new CConsultAnesth;
  $where = array();
  $where["consultation_id"] = "= '".$consult->consultation_id."'";
  $consultAnesth->loadObject($where);  
  $consultAnesth->consultation_id = $consult->consultation_id;
  $consultAnesth->operation_id = $_operation_id;      
  if($msg = $consultAnesth->store()) {
    CAppUI::setMsg($msg, UI_MSG_WARNING);
  }
}

// Redirect final
if($ajax) {
  echo CAppUI::getMsg();
  CApp::rip();
}
if($current_m = CValue::post("_m_redirect")) {
  CAppUI::redirect("m=$current_m");
} else {
  $current_m = ($sejour->type == "urg") ? "dPurgences" : "dPcabinet";
  CAppUI::redirect("m=$current_m&tab=edit_consultation&selConsult=$consult->consultation_id&chirSel=$chir->user_id");
}

?>