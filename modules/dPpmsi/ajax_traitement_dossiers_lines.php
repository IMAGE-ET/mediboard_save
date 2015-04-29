<?php 

/**
 * $Id$
 *  
 * @category Pmsi
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @link     http://www.mediboard.org */

CCanDo::checkRead();
$group = CGroups::loadCurrent();
$filterFunction = CValue::get("filterFunction");
$date           = CValue::getOrSession("date");
CValue::setSession("date", $date);

$type           = CValue::get("type");
$service_id     = CValue::get("service_id");
$service_id     = explode(",", $service_id);
CMbArray::removeValue("", $service_id);
$prat_id        = CValue::get("prat_id");
$order_way      = CValue::get("order_way", "ASC");
$order_col      = CValue::get("order_col", "sortie_reelle");
$tri_recept     = CValue::get("tri_recept");
$tri_complet    = CValue::get("tri_complet");
$period         = CValue::get("period");

$date_actuelle = CMbDT::dateTime("00:00:00");
$date_demain   = CMbDT::dateTime("00:00:00", "+ 1 day");
$hier     = CMbDT::date("- 1 day", $date);
$demain   = CMbDT::date("+ 1 day", $date);
$date_min = CMbDT::dateTime("00:00:00", $date);
$date_max = CMbDT::dateTime("23:59:59", $date);

// Entr�es de la journ�e
$sejour = new CSejour();

// Lien avec les patients et les praticiens
$ljoin["patients"] = "sejour.patient_id = patients.patient_id";
$ljoin["users"] = "sejour.praticien_id = users.user_id";

// Filtre sur les services
if (count($service_id)) {
  $ljoin["affectation"] = "affectation.sejour_id = sejour.sejour_id AND affectation.sortie = sejour.sortie_reelle";
  $in_services = CSQLDataSource::prepareIn($service_id);
  $where["sejour.service_id"] = " $in_services OR affectation.service_id $in_services";
}

// Filtre sur le type du s�jour
if ($type == "ambucomp") {
  $where["sejour.type"] = " = 'ambu' OR `sejour`.`type` = 'comp'";
}
elseif ($type) {
  $where["sejour.type"] = " = '$type'";
}
else {
  $where["sejour.type"] = " != 'urg' AND `sejour`.`type` != 'seances'";
}

// Filtre sur le praticien
if ($prat_id) {
  $where["sejour.praticien_id"] = " = '$prat_id'";
}

if ($period) {
  $hour = CAppUI::conf("dPadmissions hour_matin_soir");
  if ($period == "matin") {
    $date_max = CMbDT::dateTime($hour, $date);
  }
  else {
    $date_min = CMbDT::dateTime($hour, $date);
  }
}

if ($tri_recept) {
  $where["sejour.reception_sortie"] = " IS NULL";
}

if ($tri_complet) {
  $where["sejour.completion_sortie"] = " IS NULL";
}

$where["sejour.group_id"] = "= '$group->_id'";
$where["sejour.sortie_reelle"]   = "BETWEEN '$date_min' AND '$date_max'";
$where["sejour.annule"]   = "= '0'";

if ($order_col != "patient_id" && $order_col != "sortie_reelle" && $order_col != "praticien_id") {
  $order_col = "patient_id";
}

if ($order_col == "patient_id") {
  $order = "patients.nom $order_way, patients.prenom $order_way, sejour.entree_prevue";
}

if ($order_col == "sortie_reelle") {
  $order = "sejour.sortie_reelle $order_way, patients.nom, patients.prenom";
}

if ($order_col == "praticien_id") {
  $order = "users.user_last_name $order_way, users.user_first_name";
}

/** @var CSejour[] $sejours */
$sejours = $sejour->loadList($where, $order, null, "sejour_id", $ljoin);

// Mass preloading
$patients   = CStoredObject::massLoadFwdRef($sejours, "patient_id");
$praticiens = CStoredObject::massLoadFwdRef($sejours, "praticien_id");
$functions  = CStoredObject::massLoadFwdRef($praticiens, "function_id");

// Chargement des NDA
CSejour::massLoadNDA($sejours);
foreach ($sejours as $sejour_id => $_sejour) {
  $_sejour->loadRefPatient();
  $praticien = $_sejour->loadRefPraticien();

  $_sejour->loadRefTraitementDossier();
  if ($filterFunction && $filterFunction != $praticien->function_id) {
    unset($sejours[$sejour_id]);
    continue;
  }
}

// Si la fonction selectionn�e n'est pas dans la liste des fonction, on la rajoute
if ($filterFunction && !array_key_exists($filterFunction, $functions)) {
  $_function = new CFunctions();
  $_function->load($filterFunction);
  $functions[$filterFunction] = $_function;
}

// Groupage en temps r�el
$groupage = new CGroupageTemporaire();
$total = 0;
$sejour_groupes = 0;
if ($groupage->testFG()) {
  $groupage->loadResultGroupageTemporaire($sejours);

  foreach ($groupage->_refs_infos_ghs as $key =>$_ghs) {
    $total += $_ghs->ghs_pri;
    if ($_ghs->ghm_nro !== "90Z00Z") {
      $sejour_groupes ++;
    }
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("sejours"       , $sejours);
$smarty->assign("functions"     , $functions);
$smarty->assign("filterFunction", $filterFunction);
$smarty->assign("order_way"     , $order_way);
$smarty->assign("order_col"     , $order_col);
$smarty->assign('date'          , $date);
$smarty->assign("hier"          , $hier);
$smarty->assign("demain"        , $demain);
$smarty->assign("date_min"      , $date_min);
$smarty->assign("date_max"      , $date_max);
$smarty->assign("date_demain"   , $date_demain);
$smarty->assign("date_actuelle" , $date_actuelle);
$smarty->assign("period"        , $period);
$smarty->assign("groupage"      , $groupage);
$smarty->assign("sejour_groupes", $sejour_groupes);
$smarty->assign("total"         , $total);

$smarty->display("traitement_dossiers/inc_traitement_dossiers_lines.tpl");