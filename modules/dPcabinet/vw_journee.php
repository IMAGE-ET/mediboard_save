<?php

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision:
* @author Romain Ollivier
*/

CCanDo::checkRead();

$mediuser = new CMediusers;
$mediuser->load(CAppUI::$instance->user_id);

//Initialisations des variables
$cabinet_id   = CValue::getOrSession("cabinet_id", $mediuser->function_id);
$date         = CValue::getOrSession("date", mbDate());

$canceled       = CValue::getOrSession("canceled", false);
$finished       = CValue::getOrSession("finished", true);
$paid           = CValue::getOrSession("paid"    , true);
$empty          = CValue::getOrSession("empty"   , true);
$mode_vue       = CValue::getOrSession("mode_vue", "vertical");

/*
$canceled       = 0;
$finished       = 1;
$paid           = 1;
$empty          = 1;
*/

$mode_urgence = CValue::get("mode_urgence", false);
$offline      = CValue::get("offline"     , false);

$hour         = mbTime(null);
$board        = CValue::get("board", 1);
$boardItem    = CValue::get("boardItem", 1);
$consult      = new CConsultation();

$cabinets = CMediusers::loadFonctions(PERM_EDIT, null, "cabinet");

if ($mode_urgence) {
  $group = CGroups::loadCurrent();
  $cabinet_id = $group->service_urgences_id;
}

// Récupération de la liste des praticiens
$praticiens = array();
$cabinet = new CFunctions;

if ($cabinet_id) {
  $praticiens = CAppUI::pref("pratOnlyForConsult", 1) ? 
  $mediuser->loadPraticiens(PERM_READ, $cabinet_id) :
  $mediuser->loadProfessionnelDeSante(PERM_READ, $cabinet_id);
    
  $cabinet->load($cabinet_id);
}

// Praticiens disponibles ??????????????????
$all_prats = $praticiens;

if ($consult->_id) {
  $date = $consult->_ref_plageconsult->date;
  CValue::setSession("date", $date);
}

// Récupération des plages de consultation du jour et chargement des références
$listPlages = array();
foreach($praticiens as $prat) {
  $listPlage = new CPlageconsult();
  $where = array();
  $where["chir_id"] = "= '$prat->_id'";
  $where["date"] = "= '$date'";
  $order = "debut";
  $listPlage = $listPlage->loadList($where, $order);
  if(!count($listPlage)) {
    unset($praticiens[$prat->_id]);
  } 
  else {
    $listPlages[$prat->_id]["prat"] = $prat;
    $listPlages[$prat->_id]["plages"] = $listPlage;
    $listPlages[$prat->_id]["destinations"] = array();    
  }
}

// Destinations : plages des autres praticiens
foreach ($listPlages as $key_prat => $infos_by_prat) {
  foreach ($listPlages as $key_other_prat => $infos_other_prat) {
    if ($infos_by_prat["prat"]->_id != $infos_other_prat["prat"]->_id) {
      foreach ($listPlages[$key_other_prat]["plages"] as $key_plage => $other_plage) {
        $listPlages[$key_prat]["destinations"][] = $other_plage;
      }
    }
  }
}


$nb_attente = 0;
$nb_a_venir = 0;
$patients_fetch = array();

$heure_min = null;

foreach ($listPlages as $key_prat => $infos_by_prat) {
  foreach ($infos_by_prat["plages"] as $key_plage => $plage) {
    $plage->_ref_chir = $infos_by_prat["prat"];
    $plage->loadRefsConsultations($canceled, $finished);
    if(!$paid) {
      foreach($plage->_ref_consultations as $key_consult => $_consult) {
        if($_consult->patient_date_reglement) {
          unset($plage->_ref_consultations[$key_consult]);
        }
      }
    }
    if(!count($plage->_ref_consultations) && !$empty) {
      unset($listPlages[$key_prat]["plages"][$key_plage]);
      continue;
    }
    $plage->loadRefsNotes();
    if (count($plage->_ref_consultations) && $mode_vue == "horizontal") {
      $plage->_ref_consultations = array_combine(range(0, count($plage->_ref_consultations)-1),$plage->_ref_consultations);
    }
    
    foreach ($plage->_ref_consultations as $consultation) {
      if ($mode_urgence){
        if ($consultation->loadRefSejour()->_id && $consultation->_ref_sejour->type == "urg"){
          unset($plage->_ref_consultations[$consultation->_id]);
          continue;
        }
      }
      if ($heure_min === null) {
      $heure_min = $consultation->heure;
    }
    if ($consultation->heure < $heure_min) {
      $heure_min = $consultation->heure;
    }
      if ($consultation->chrono < CConsultation::TERMINE) {
        $nb_a_venir++;
      }
      if ($consultation->chrono == CConsultation::PATIENT_ARRIVE) {
        $nb_attente++;
      }
      $consultation->loadRefSejour();
      $consultation->loadRefPatient();
      $consultation->loadRefCategorie();
      $consultation->countDocItems();
      
      if ($offline && $consultation->patient_id && !isset($patients_fetch[$consultation->patient_id])) {
        $args = array(
          "object_guid" => $consultation->_ref_patient->_guid,
          "ajax" => 1,
        );
        $patients_fetch[$consultation->patient_id] = CApp::fetch("system", "httpreq_vw_complete_object", $args);
      }
    }
  }
  if(!count($infos_by_prat["plages"]) && !$empty) {
    unset($listPlages[$key_prat]);
    unset($praticiens[$key_prat]);
  }
}

// Création du template
$smarty = new CSmartyDP();
$smarty->assign("offline"       , $offline);
$smarty->assign("cabinet_id"    , $cabinet_id);
$smarty->assign("cabinet"       , $cabinet);
$smarty->assign("patients_fetch", $patients_fetch);
$smarty->assign("consult"       , $consult);
$smarty->assign("listPlages"    , $listPlages);
$smarty->assign("empty"         , $empty);
$smarty->assign("canceled"      , $canceled);
$smarty->assign("paid"          , $paid);
$smarty->assign("finished"      , $finished);
$smarty->assign("date"          , $date);
$smarty->assign("hour"          , $hour);
$smarty->assign("praticiens"    , $praticiens);
$smarty->assign("all_prats"     , $all_prats);
$smarty->assign("cabinets"      , $cabinets);
$smarty->assign("board"         , $board);
$smarty->assign("boardItem"     , $boardItem);
$smarty->assign("canCabinet"    , CModule::getCanDo("dPcabinet"));
$smarty->assign("mode_urgence"  , $mode_urgence);
$smarty->assign("nb_attente"    , $nb_attente);
$smarty->assign("nb_a_venir"    , $nb_a_venir);
$smarty->assign("mode_vue"      , $mode_vue);
$smarty->assign("heure_min"     , $heure_min);
$smarty->display("vw_journee.tpl");


?>
