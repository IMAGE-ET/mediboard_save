<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

global $can, $m, $tab;

CCanDo::checkRead();

$hors_plage = new CIntervHorsPlage();
if (!$hors_plage->canRead()) {
  $can->redirect();
}

// Toutes les salles des blocs
$listBlocs = CGroups::loadCurrent()->loadBlocs(PERM_READ);

// Les salles autoris�es
$salle = new CSalle();
$listSalles = $salle->loadListWithPerms(PERM_READ);


// Liste des Etablissements selon Permissions
$etablissements = CMediusers::loadEtablissements(PERM_READ);

// Chargement des prestations
$prestations = CPrestation::loadCurrentList();

$operation_id = CValue::getOrSession("operation_id");
$chir_id      = CAppUI::conf("dPplanningOp COperation use_session_praticien")
  ? CValue::getOrSession("chir_id") : CValue::get("chir_id");
$sejour_id    = CValue::get("sejour_id");
$hour_urgence = CValue::get("hour_urgence");
$min_urgence  = CValue::get("min_urgence");
$date_urgence = CValue::get("date_urgence");
$salle_id     = CValue::get("salle_id");
$patient_id   = CValue::get("pat_id");

// L'utilisateur est-il un praticien
$user = $chir = CMediusers::get();
if ($chir->isPraticien() and !$chir_id) {
  $chir_id = $chir->user_id;
}

// Chargement du praticien
$chir = new CMediusers();
if ($chir_id) {
  $testChir = new CMediusers();
  $testChir->load($chir_id);
  if ($testChir->isPraticien()) {
    $chir = $testChir;
  }
}
$chir->loadRefFunction();
$prat = $chir;

// Chargement du patient
$patient = new CPatient();
if ($patient_id && !$operation_id && !$sejour_id) {
  $patient->load($patient_id);
  $patient->loadRefsSejours();
}

// On r�cup�re le s�jour
$sejour = new CSejour();
if ($sejour_id && !$operation_id) {
  $sejour->load($sejour_id);
  $sejour->loadRefsFwd();
  if (!$chir_id) {
    $chir = $sejour->_ref_praticien;
  }
  // On ne change a priori pas le praticien du s�jour
  $sejour->_ref_praticien->canDo();
  $prat    = $sejour->_ref_praticien;
  $patient = $sejour->_ref_patient;
}

// On r�cup�re l'op�ration
$op = new COperation();
$op->load($operation_id);

if ($op->_id) {
  if (CAppUI::conf("dPplanningOp COperation use_session_praticien")) {
    CValue::setSession("chir_id", $op->chir_id);
  }
  // On v�rifie que l'utilisateur a les droits sur l'intervention
  if (!$op->canDo()->read) {
    global $m, $tab;
    CAppUI::setMsg("Vous n'avez pas acc�s � cette intervention hors plage", UI_MSG_WARNING);
    CAppUI::redirect("m=$m&tab=$tab&soperation_id=0");
  }

  // Chargement des r�g�rences
  $op->loadRefs();
  $op->loadRefsNotes();
  $op->_ref_chir->loadRefFunction();

  $op->loadRefs();
  foreach ($op->_ref_actes_ccam as $acte) {
    $acte->loadRefExecutant();
  }
  
  $sejour = $op->_ref_sejour;
  $sejour->loadRefsFwd();
  $sejour->loadRefCurrAffectation()->loadRefService();
  $sejour->_ref_praticien->canDo();
  $sejour->makeCancelAlerts($op->_id);
  $chir    = $op->_ref_chir;
  $patient = $sejour->_ref_patient;
}
else {
  if ($hour_urgence && isset($min_urgence)) {
    $hour = intval(substr($hour_urgence, 0, 2));
    $min = intval(substr($min_urgence, 0, 2));
    $op->_time_urgence = "$hour:$min:00";
  }

  $op->date = $op->_datetime = $date_urgence ? $date_urgence : CMbDT::date();
  $op->salle_id = $salle_id;
}
// Liste des types d'anesth�sie
$listAnesthType = new CTypeAnesth;
$orderanesth = "name";
$listAnesthType = $listAnesthType->loadList(null, $orderanesth);

// Liste des anesth�sistes
$anesthesistes = $user->loadAnesthesistes(PERM_READ);

// Compl�ments de chargement du s�jour
$sejour->makeDatesOperations();
$sejour->loadNDA();
$sejour->loadRefsNotes();

if (CModule::getActive("maternite")) {
  $sejour->loadRefGrossesse();
}

// Chargements de chargement du patient
$patient->loadRefsSejours();
$patient->loadRefsFwd();
$patient->loadRefsCorrespondants();
$patient->loadRefsCorrespondantsPatient();

$correspondantsMedicaux = array();
if ($patient->_ref_medecin_traitant->_id) {
  $correspondantsMedicaux["traitant"] = $patient->_ref_medecin_traitant;
}
foreach ($patient->_ref_medecins_correspondants as $correspondant) {
  $correspondantsMedicaux["correspondants"][] = $correspondant->_ref_medecin;
}

$medecin_adresse_par = "";
if ($sejour->adresse_par_prat_id && ($sejour->adresse_par_prat_id != $patient->_ref_medecin_traitant->_id)) {
  $medecin_adresse_par = new CMedecin();
  $medecin_adresse_par->load($sejour->adresse_par_prat_id);
}

// Chargement des etablissements externes
$etab = new CEtabExterne();
$count_etab_externe = $etab->countList();

$sejours =& $patient->_ref_sejours;

$config = CAppUI::conf("dPplanningOp CSejour");
$hours = range($config["heure_deb"], $config["heure_fin"]);
$mins = range(0, 59, $config["min_intervalle"]);

$config = CAppUI::conf("dPplanningOp COperation");
$hours_duree = array("deb" => $config["duree_deb"], "fin" =>$config["duree_fin"]);
$hours_urgence = array("deb" => $config["hour_urgence_deb"], "fin" => $config["hour_urgence_fin"]);
$mins_duree = $config["min_intervalle"];

$config = CAppUI::conf("dPplanningOp CSejour");
$heure_sortie_ambu   = $config["heure_sortie_ambu"];
$heure_sortie_autre  = $config["heure_sortie_autre"];
$heure_entree_veille = $config["heure_entree_veille"];
$heure_entree_jour   = $config["heure_entree_jour"];

// R�cup�ration de la liste des services
$where = array();
$where["externe"]   = "= '0'";
$where["cancelled"] = "= '0'";
$service = new CService();
$services = $service->loadGroupList($where);

$sortie_sejour = CMbDT::dateTime();
if ($sejour->sortie_reelle) {
  $sortie_sejour = $sejour->sortie_reelle;
}

$where = array();
$where["entree"] = "<= '".$sortie_sejour."'";
$where["sortie"] = ">= '".$sortie_sejour."'";
$where["function_id"] = "IS NOT NULL";

$affectatione = new CAffectation();
/** @var CAffectation[] $blocages_lit */
$blocages_lit = $affectatione->loadList($where);

$where["function_id"] = "IS NULL";

foreach ($blocages_lit as $key => $blocage) {
  $blocage->loadRefLit()->loadRefChambre()->loadRefService();
  $where["lit_id"] = "= '$blocage->lit_id'";
  if (!$sejour->_id && $affectatione->loadObject($where)) {
    $affectatione->loadRefSejour();
    $affectatione->_ref_sejour->loadRefPatient();
    $jusqua = CMbDT::transform($affectatione->sortie, null, "%Hh%Mmin %d-%m-%Y")." (".$affectatione->_ref_sejour->_ref_patient->_view;
    $blocage->_ref_lit->_view .= " indisponible jusqu'� ".$jusqua.")";
  }
}

$exchange_source = CExchangeSource::get("mediuser-" . CAppUI::$user->_id, "smtp");

$_functions = array();

if ($chir->_id) {
  $_functions = $chir->loadBackRefs("secondary_functions");
}

$op->loadRefChir2();
$op->loadRefChir3();
$op->loadRefChir4();

if (!$op->_id) {
  $op->_time_op = $op->temp_operation = "00:00:00";
  if ($hour_urgence && $min_urgence) {
    $time = "$hour_urgence:$min_urgence:00";
  }
  else {
    $time = str_pad($hours_urgence["deb"], 2, "0", STR_PAD_LEFT).":00:00";
  }
  $op->_time_urgence = $op->time_operation = $time;
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("sejours_collision", $patient->getSejoursCollisions());
$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));
$smarty->assign("canSante400", CModule::getCanDo("dPsante400"));
$smarty->assign("urgInstalled", CModule::getInstalled("dPurgences"));
$smarty->assign("heure_sortie_ambu"   , $heure_sortie_ambu);
$smarty->assign("heure_sortie_autre"  , $heure_sortie_autre);
$smarty->assign("heure_entree_veille" , $heure_entree_veille);
$smarty->assign("heure_entree_jour"   , $heure_entree_jour);

$smarty->assign("op"        , $op);
$smarty->assign("plage"     , $op->plageop_id ? $op->_ref_plageop : new CPlageOp );
$smarty->assign("sejour"    , $sejour);
$smarty->assign("chir"      , $chir);
$smarty->assign("praticien" , $chir);
$smarty->assign("patient"   , $patient );
$smarty->assign("sejours"   , $sejours);
$smarty->assign("ufs"       , CUniteFonctionnelle::getUFs());
$smarty->assign("_functions", $_functions);

$smarty->assign("modurgence", 1);
$smarty->assign("date_min", CMbDT::date());
$smarty->assign("date_max", CMbDT::date("+".CAppUI::conf("dPplanningOp COperation nb_jours_urgence")." days", CMbDT::date()));

$smarty->assign("listAnesthType", $listAnesthType);
$smarty->assign("anesthesistes" , $anesthesistes);
$smarty->assign("listServices"  , $services);
$smarty->assign("etablissements", $etablissements);

$smarty->assign("hours"        , $hours);
$smarty->assign("mins"         , $mins);
$smarty->assign("hours_duree"  , $hours_duree);
$smarty->assign("hours_urgence", $hours_urgence);
$smarty->assign("mins_duree"   , $mins_duree);

$smarty->assign("prestations", $prestations);
$smarty->assign("blocages_lit", $blocages_lit);

$smarty->assign("correspondantsMedicaux", $correspondantsMedicaux);
$smarty->assign("count_etab_externe", $count_etab_externe);
$smarty->assign("medecin_adresse_par", $medecin_adresse_par);

$smarty->assign("listBlocs",  $listBlocs);

$smarty->assign("exchange_source"       , $exchange_source);

$smarty->display("vw_edit_planning.tpl");
