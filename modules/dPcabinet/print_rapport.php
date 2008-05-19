<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

// !! Attention, r�gression importante si ajout de type de paiement
global $AppUI, $can, $m, $utypes;

$ds = CSQLDataSource::get("std");
$today = mbDate();

// R�cup�ration des param�tres
$filter = new CPlageconsult();

$filter->_date_min = mbGetValueFromGetOrSession("_date_min", mbDate());
$filter->_date_max = mbGetValueFromGetOrSession("_date_max", mbDate());
$filter->_etat_reglement_patient = mbGetValueFromGetOrSession("_etat_reglement_patient");
$filter->_etat_reglement_tiers   = mbGetValueFromGetOrSession("_etat_reglement_tiers");

$filter->_mode_reglement = mbGetValueFromGetOrSession("mode", 0);
if($filter->_mode_reglement == null) {
	$filter->_mode_reglement = 0;
}
$filter->_type_affichage = mbGetValueFromGetOrSession("_type_affichage" , 1);
//Traduction pour le passage d'un enum en bool pour les requetes sur la base de donnee
if($filter->_type_affichage == "complete") {
	$filter->_type_affichage = 1;
} elseif ($filter->_type_affichage == "totaux"){
	$filter->_type_affichage = 0;
}

$mediuser = new CMediusers();
$mediuser->load($AppUI->user_id);
$mediuser->loadRefFunction();

$chir = mbGetValueFromGetOrSession("chir");
$chirSel = new CMediusers;
$chirSel->load($chir);

// Requ�te sur les consultations selon les crit�res

$consultation = new CConsultation();
$where = array();
$ljoin = array();

// Left join sur les plages de consult
$ljoin["plageconsult"] = "consultation.plageconsult_id = plageconsult.plageconsult_id";
// Tri sur les paiements
if($filter->_etat_reglement_patient) {
  if($filter->_etat_reglement_patient == "reglee") {
    $where["consultation.patient_date_reglement"] = "IS NOT NULL";
  } else {
    $where["consultation.patient_date_reglement"] = "IS NULL";
    $where["consultation.du_patient"] = "> 0";
  }
}
if($filter->_etat_reglement_tiers) {
  if($filter->_etat_reglement_tiers == "reglee") {
  $where["consultation.tiers_date_reglement"] = "IS NOT NULL";
  } else {
  $where["consultation.tiers_date_reglement"] = "IS NULL";
    $where["consultation.du_tiers"] = "> 0";
  }
}
$where["consultation.patient_id"] = "IS NOT NULL";
// Tri sur les dates
$where[] = "plageconsult.date >= '$filter->_date_min' AND plageconsult.date <= '$filter->_date_max'";
// Tri sur les praticiens
$listPrat = new CMediusers();
$is_admin = in_array($utypes[$mediuser->_user_type], array("Administrator"));
if($is_admin) {
  $listPrat = $listPrat->loadPraticiens(PERM_READ);
} else {
  $listPrat = $listPrat->loadPraticiens(PERM_READ, $mediuser->function_id);
}
$where["plageconsult.chir_id"] = $ds->prepareIn(array_keys($listPrat), $chir);

$order = "plageconsult.date, plageconsult.debut, plageconsult.chir_id";

$reglement = new CReglement();

// Chargement des banques
$orderBanque = "nom ASC";
$banque = new CBanque();
$banques = $banque->loadList(null,$orderBanque);

// Initialisation du tableau de reglements
$recapReglement["total"]      = array("nb_consultations"     => "0",
                                      "du_patient"           => "0",
                                      "reste_patient"        => "0",
                                      "du_tiers"             => "0",
                                      "reste_tiers"          => "0",
                                      "nb_reglement_patient" => "0",
                                      "nb_impayes_patient"   => "0",
                                      "nb_reglement_tiers"   => "0",
                                      "nb_impayes_tiers"     => "0",
                                      "secteur1"             => "0",
                                      "secteur2"             => "0");
foreach(explode("|", $reglement->_specs["mode"]->list) as $curr_type) {
  $recapReglement[$curr_type] = array("du_patient"           => "0",
                                      "du_tiers"             => "0",
                                      "nb_reglement_patient" => "0",
                                      "nb_reglement_tiers"   => "0");
}

// Chargement des consultations
$listConsults = $consultation->loadList($where, $order, null, null, $ljoin);
$listPlages = array();
foreach($listConsults as $consult) {
  $consult->loadRefsFwd();
  $consult->loadrefsReglements();
  $consult->_new_patient_reglement = new CReglement();
  $consult->_new_patient_reglement->mode = "especes";
  $consult->_new_patient_reglement->montant = $consult->_du_patient_restant;
  $consult->_new_tiers_reglement = new CReglement();
  $consult->_new_tiers_reglement->mode = "virement";
  $consult->_new_tiers_reglement->montant = $consult->_du_tiers_restant;
  $recapReglement["total"]["nb_consultations"]++;
  $recapReglement["total"]["du_patient"]      += $consult->_reglements_total_patient;
  $recapReglement["total"]["reste_patient"]   += $consult->_du_patient_restant;
  if($consult->_du_patient_restant) {
    $recapReglement["total"]["nb_impayes_patient"]++;
  }
  $recapReglement["total"]["du_tiers"]        += $consult->_reglements_total_tiers;
  $recapReglement["total"]["reste_tiers"]     += $consult->_du_tiers_restant;
  if($consult->_du_tiers_restant) {
    $recapReglement["total"]["nb_impayes_tiers"]++;
  }
  $recapReglement["total"]["nb_reglement_patient"] += count($consult->_ref_reglements_patient);
  $recapReglement["total"]["nb_reglement_tiers"]   += count($consult->_ref_reglements_tiers);
  $recapReglement["total"]["secteur1"]             += $consult->secteur1;
  $recapReglement["total"]["secteur2"]             += $consult->secteur2;
  foreach($consult->_ref_reglements_patient as $curr_reglement) {
    $recapReglement[$curr_reglement->mode]["du_patient"]          += $curr_reglement->montant;
    $recapReglement[$curr_reglement->mode]["nb_reglement_patient"]++;
  }
  foreach($consult->_ref_reglements_tiers as $curr_reglement) {
    $recapReglement[$curr_reglement->mode]["du_tiers"]          += $curr_reglement->montant;
    $recapReglement[$curr_reglement->mode]["nb_reglement_tiers"]++;
  }
  if(!isset($listPlages[$consult->plageconsult_id])) {
    $plageConsult = new CPlageconsult();
    $plageConsult->load($consult->plageconsult_id);
    $plageConsult->loadRefsFwd();
    $listPlages[$consult->plageconsult_id]["plage"] = $plageConsult;
    $listPlages[$consult->plageconsult_id]["total"]["secteur1"] = 0;
    $listPlages[$consult->plageconsult_id]["total"]["secteur2"] = 0;
    $listPlages[$consult->plageconsult_id]["total"]["total"]    = 0;
    $listPlages[$consult->plageconsult_id]["total"]["patient"]  = 0;
    $listPlages[$consult->plageconsult_id]["total"]["tiers"]    = 0;
  }
  $listPlages[$consult->plageconsult_id]["total"]["secteur1"] += $consult->secteur1;
  $listPlages[$consult->plageconsult_id]["total"]["secteur2"] += $consult->secteur2;
  $listPlages[$consult->plageconsult_id]["total"]["total"]    += $consult->secteur1 + $consult->secteur2;
  $listPlages[$consult->plageconsult_id]["total"]["patient"]  += $consult->_reglements_total_patient;
  $listPlages[$consult->plageconsult_id]["total"]["tiers"]    += $consult->_reglements_total_tiers;
  $listPlages[$consult->plageconsult_id]["consultations"][$consult->_id] = $consult;
}


// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("today"         , $today);
$smarty->assign("filter"        , $filter);
$smarty->assign("chirSel"       , $chirSel);
$smarty->assign("listPrat"      , $listPrat);
$smarty->assign("listPlages"    , $listPlages);
$smarty->assign("recapReglement", $recapReglement);
$smarty->assign("reglement"     , $reglement);
$smarty->assign("banques"       , $banques);

$smarty->display("print_rapport.tpl");

?>