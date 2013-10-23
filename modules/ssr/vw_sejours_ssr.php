<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

// Plateaux disponibles
$show_cancelled_services = CValue::getOrSession("show_cancelled_services", CAppUI::conf("ssr recusation view_services_inactifs"));
$date                    = CValue::getOrSession("date", CMbDT::date());
$order_way               = CValue::getOrSession("order_way", "ASC");
$order_col               = CValue::getOrSession("order_col", "patient_id");
$show                    = CValue::getOrSession("show", "all");
$group_by                = CValue::get("group_by");

// Filtre
$filter = new CSejour;
$filter->service_id   = CValue::getOrSession("service_id");
$filter->praticien_id = CValue::getOrSession("praticien_id");
$filter->referent_id  = CValue::getOrSession("referent_id");

// Chargement des sejours SSR pour la date selectionn�e
$group = CGroups::loadCurrent();
$group_id = $group->_id;
$where["type"]            = "= 'ssr'";
$where["sejour.group_id"] = "= '$group_id'";
$where["sejour.annule"]   = "= '0'";
$order = null;

if ($order_col == "entree") {
  $order = "sejour.entree $order_way, patients.nom, patients.prenom";
}

if ($order_col == "sortie") {
  $order = "sejour.sortie $order_way, patients.nom, patients.prenom";
}

$ljoin["patients"] = "sejour.patient_id = patients.patient_id";
if ($order_col == "patient_id") {
  $order = "patients.nom $order_way, patients.prenom, sejour.entree";
}

if ($order_col == "praticien_id") {
  $order = "sejour.praticien_id $order_way, patients.nom, patients.prenom";
}

if ($order_col == "libelle") {
  $order = "sejour.libelle $order_way, patients.nom, patients.prenom";
}

if ($order_col == "service_id") {
  $order = "sejour.service_id $order_way, patients.nom, patients.prenom";
}

if ($order_col == "lit_id") {
  $ljoin["affectation"] = "sejour.sejour_id = affectation.sejour_id AND '$date' BETWEEN affectation.entree AND affectation.sortie";
  $ljoin["lit"]         = "lit.lit_id = affectation.lit_id";
  $ljoin["chambre"]     = "lit.chambre_id = chambre.chambre_id";
  $ljoin["service"]     = "chambre.service_id = service.service_id";

  $order = "service.nom $order_way, chambre.nom $order_way, lit.nom $order_way, patients.nom, patients.prenom";
}

// Masquer les services inactifs
if (!$show_cancelled_services) {
  $service = new CService;
  $service->group_id = $group->_id;
  $service->cancelled = "1";
  $services = $service->loadMatchingList();
  $where["sejour.service_id"] = CSQLDataSource::prepareNotIn(array_keys($services));
}

$sejours = CSejour::loadListForDate($date, $where, $order, null, null, $ljoin);

// Filtre sur les services
$services = array();
$praticiens = array();
$kines = array();
$sejours_by_kine = array(
  // S�jours sans kin�s
  "" => array(),
);

// Chargement du d�tail des s�jour
foreach ($sejours as $_sejour) {
  // Filtre sur service
  $service = $_sejour->loadFwdRef("service_id", true);
  $services[$service->_id] = $service;
  if ($filter->service_id && $_sejour->service_id != $filter->service_id) {
    unset($sejours[$_sejour->_id]);
    continue;
  }

  // Filtre sur prescription, pas n�cessairement actif
  $prescription = $_sejour->loadRefPrescriptionSejour();
  if ($show == "nopresc" && $prescription && $prescription->_id) {
    unset($sejours[$_sejour->_id]);
    continue;
  }

  // Filtre sur praticien
  $praticien = $_sejour->loadRefPraticien();
  $praticiens[$praticien->_id] = $praticien;
  if ($filter->praticien_id && $_sejour->praticien_id != $filter->praticien_id) {
    unset($sejours[$_sejour->_id]);
    continue;
  }

  // Bilan SSR
  $bilan = $_sejour->loadRefBilanSSR();

  // Kin�s r�f�rent et journ�e
  $bilan->loadRefKineJournee($date);
  $kine_journee = $bilan->_ref_kine_journee;
  $kines[$kine_journee->_id] = $kine_journee;

  $kine_referent = $bilan->_ref_kine_referent;
  if (!$kine_journee->_id) {
    $kines[$kine_referent->_id] = $kine_referent;
  }

  if ($filter->referent_id && $kine_referent->_id != $filter->referent_id && $kine_journee->_id != $filter->referent_id) {
    unset($kines[$kine_journee->_id]);
    if (!$kine_journee->_id) {
      unset($kines[$kine_referent->_id]);
    }
    unset($sejours[$_sejour->_id]);
    continue;
  }

  // Regroupement par kine
  $sejours_by_kine[$kine_referent->_id][] = $_sejour;
  if ($kine_journee->_id && $kine_journee->_id != $kine_referent->_id) {
    $sejours_by_kine[$kine_journee->_id ][] = $_sejour;
  }

  // D�tail du s�jour
  $_sejour->checkDaysRelative($date);
  $_sejour->loadNDA();
  $_sejour->loadRefsNotes();

  // Patient
  $patient = $_sejour->loadRefPatient();
  $patient->loadIPP();

  // Modification des prescription
  if ($prescription) {
    $prescription->countFastRecentModif();
  }

  // Praticien demandeur
  $bilan->loadRefPraticienDemandeur();

  // Chargement du lit
  $_sejour->loadRefCurrAffectation();
}

// Ajustements services
$service = new CService;
$service->load($filter->service_id);
$services[$service->_id] = $service;
unset($services[""]);

// Ajustements kin�s
$kine = new CMediusers;
$kine->load($filter->referent_id);
$kine->loadRefFunction();
$kines[$kine->_id] = $kine;
unset($kines[""]);

// Tris a posteriori : d�truit les cl�s !
array_multisort(CMbArray::pluck($kines     , "_view"), SORT_ASC, $kines);
array_multisort(CMbArray::pluck($services  , "_view"), SORT_ASC, $services);
array_multisort(CMbArray::pluck($praticiens, "_view"), SORT_ASC, $praticiens);

// Couleurs
$colors = CColorLibelleSejour::loadAllFor(CMbArray::pluck($sejours, "libelle"));

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("date"   , $date);
$smarty->assign("filter" , $filter);
$smarty->assign("colors" , $colors);
$smarty->assign("sejours", $sejours);

$smarty->assign("sejours_by_kine"        , $sejours_by_kine);
$smarty->assign("kines"                  , $kines);
$smarty->assign("praticiens"             , $praticiens);
$smarty->assign("services"               , $services);
$smarty->assign("show"                   , $show);
$smarty->assign("group_by"               , $group_by);
$smarty->assign("show_cancelled_services", $show_cancelled_services);
$smarty->assign("order_way"              , $order_way);
$smarty->assign("order_col"              , $order_col);

$smarty->display("vw_sejours_ssr.tpl");
