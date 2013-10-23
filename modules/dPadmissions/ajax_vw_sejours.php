<?php

/**
 * $Id$
 *
 * @category Admissions
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

global $m;

// On sauvegarde le module pour que les mises en session des param�tes se fassent
// dans le module depuis lequel on acc�de � la ressource
$save_m = $m;

// Type d'admission
$current_m      = CValue::get("current_m");

$m = $current_m;

$service_id     = CValue::getOrSession("service_id");
$prat_id        = CValue::getOrSession("prat_id");
$recuse         = CValue::getOrSession("recuse", "-1");
$envoi_mail     = CValue::getOrSession("envoi_mail", "0");
$order_col      = CValue::getOrSession("order_col", "patient_id");
$order_way      = CValue::getOrSession("order_way", "ASC");
$date           = CValue::getOrSession("date", CMbDT::date());
$next           = CMbDT::date("+1 DAY", $date);
$filterFunction = CValue::getOrSession("filterFunction");

$date_actuelle = CMbDT::dateTime("00:00:00");
$date_demain   = CMbDT::dateTime("00:00:00", "+ 1 day");

$hier   = CMbDT::date("- 1 day", $date);
$demain = CMbDT::date("+ 1 day", $date);

$date_min = CMbDT::dateTime("00:00:00", $date);
$date_max = CMbDT::dateTime("23:59:00", $date);

// Chargement des prestations
$prestations = CPrestation::loadCurrentList();

// Entr�es de la journ�e
$sejour = new CSejour();

$group = CGroups::loadCurrent();

// Lien avec les patients et les praticiens
$ljoin["patients"] = "sejour.patient_id = patients.patient_id";
$ljoin["users"]    = "sejour.praticien_id = users.user_id";

// Filtre sur les services
if ($service_id) {
  $ljoin["affectation"]        = "affectation.sejour_id = sejour.sejour_id AND affectation.sortie = sejour.sortie_prevue";
  $where["affectation.service_id"] = "= '$service_id'";
}

// Filtre sur le type du s�jour
if ($current_m == "ssr") {
  $where["type"] = "= 'ssr'";
}
// Filtre sur le praticien
if ($prat_id) {
  $where["sejour.praticien_id"] = " = '$prat_id'";
}

$where["sejour.group_id"] = "= '$group->_id'";
$where["sejour.entree"]   = "BETWEEN '$date_min' AND '$date_max'";

if ($envoi_mail == 1) {
  $ljoin["operations"] = "operations.sejour_id = sejour.sejour_id";
  $where["operations.envoi_mail"] = "IS NOT NULL";
}
else {
  $where["sejour.recuse"]   = "= '$recuse'";
  if ($recuse != 1) {
    $where["sejour.annule"]   = "= '0'";
  }
}
if ($order_col != "patient_id" && $order_col != "entree_prevue" && $order_col != "praticien_id") {
  $order_col = "patient_id";
}

if ($order_col == "patient_id") {
  $order = "patients.nom $order_way, patients.prenom $order_way, sejour.entree_prevue";
}

if ($order_col == "entree_prevue") {
  $order = "sejour.entree_prevue $order_way, patients.nom, patients.prenom";
}

if ($order_col == "praticien_id") {
  $order = "users.user_last_name $order_way, users.user_first_name";
}

/** @var CSejour[] $sejours */
$sejours = $sejour->loadList($where, $order, null, null, $ljoin);

CMbObject::massLoadFwdRef($sejours, "patient_id");
$praticiens = CMbObject::massLoadFwdRef($sejours, "praticien_id");
$functions  = CMbObject::massLoadFwdRef($praticiens, "function_id");

// Pour l'envoi de mail, on instancie une nouvelle op�ration
$operation = new COperation;

foreach ($sejours as $sejour_id => $_sejour) {
  $praticien = $_sejour->loadRefPraticien();
  $_sejour->loadRefFicheAutonomie();
  
  if ($filterFunction && $filterFunction != $praticien->function_id) {
    unset($sejours[$sejour_id]);
    continue;
  }
  
  // Chargement du patient
  $_sejour->loadRefPatient(1);
  $_sejour->_ref_patient->loadIPP();
  
  // Chargment du num�ro de dossier
  $_sejour->loadNDA();
  $whereOperations = array("annulee" => "= '0'");

  // Chargement de l'affectation
  $_sejour->loadRefsAffectations();
  $affectation =& $_sejour->_ref_first_affectation;
  if ($affectation->_id) {
    $affectation->loadRefLit(1);
    $affectation->_ref_lit->loadCompleteView();
  }
  
  // Pour l'envoi de mail, afficher une enveloppe pour les interventions modifi�es par le chirurgien
  if ($envoi_mail) {
    $where = array(
      "sejour.sejour_id" => "= '$_sejour->_id'",
      "user_log.user_id" => "= operations.chir_id"
    );
    $ljoin = array(
     "sejour" => "sejour.sejour_id = operations.sejour_id",
     "user_log" => "user_log.object_id = operations.operation_id AND user_log.object_class = 'COperation'"
    );
    // @todo d�claration de la variable � r�aliser
    $_sejour->_envoi_mail = $operation->countList($where, null, $ljoin);
  }
}

// Si la fonction selectionn�e n'est pas dans la liste des fonction, on la rajoute
if ($filterFunction && !array_key_exists($filterFunction, $functions)) {
  $_function = new CFunctions();
  $_function->load($filterFunction);
  $functions[$filterFunction] = $_function;
}


$m = $save_m;

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("current_m"     , $current_m);
$smarty->assign("hier"          , $hier);
$smarty->assign("demain"        , $demain);
$smarty->assign("date_min"      , $date_min);
$smarty->assign("date_max"      , $date_max);
$smarty->assign("date_demain"   , $date_demain);
$smarty->assign("date_actuelle" , $date_actuelle);
$smarty->assign("date"          , $date);
$smarty->assign("recuse"        , $recuse);
$smarty->assign("order_col"     , $order_col);
$smarty->assign("order_way"     , $order_way);
$smarty->assign("sejours"       , $sejours);
$smarty->assign("prestations"   , $prestations);
$smarty->assign("canAdmissions" , CModule::getCanDo("dPadmissions"));
$smarty->assign("canPatients"   , CModule::getCanDo("dPpatients"));
$smarty->assign("canPlanningOp" , CModule::getCanDo("dPplanningOp"));
$smarty->assign("functions"     , $functions);
$smarty->assign("filterFunction", $filterFunction);

$smarty->display("inc_vw_sejours.tpl");
