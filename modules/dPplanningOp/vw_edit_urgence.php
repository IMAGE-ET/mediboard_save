<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $can, $m, $tab;

CCanDo::checkRead();

$hors_plage = new CIntervHorsPlage();
if(!$hors_plage->canRead()) {
  $can->redirect();
}
// Liste des Etablissements selon Permissions
$etablissements = CMediusers::loadEtablissements(PERM_READ);

// Chargement des prestations
$prestations = CPrestation::loadCurrentList();

$operation_id = CValue::getOrSession("operation_id");
$chir_id      = CValue::getOrSession("chir_id");
$sejour_id    = CValue::get("sejour_id");
$patient_id   = CValue::get("pat_id");
$today        = mbDate();
$tomorow      = mbDate("+1 DAY");

// L'utilisateur est-il un praticien
$chir = CMediusers::get();
if ($chir->isPraticien() and !$chir_id) {
  $chir_id = $chir->user_id;
}

// Chargement du praticien
$chir = new CMediusers;
if ($chir_id) {
  $testChir = new CMediusers();
  $testChir->load($chir_id);
  if($testChir->isPraticien()) {
    $chir = $testChir;
  }
}
$chir->loadRefFunction();
$prat = $chir;

// Chargement du patient
$patient = new CPatient;
if ($patient_id && !$operation_id && !$sejour_id) {
  $patient->load($patient_id);
  $patient->loadRefsSejours();
}

// V�rification des droits sur les praticiens
$listPraticiens = $chir->loadPraticiens(PERM_EDIT);
$categorie_prat = array();
foreach($listPraticiens as $keyPrat =>$prat){
  $prat->loadRefsFwd();
  $categorie_prat[$keyPrat] = $prat->_ref_discipline->categorie;
}

// On r�cup�re le s�jour
$sejour = new CSejour;
if($sejour_id && !$operation_id) {
  $sejour->load($sejour_id);
  $sejour->loadRefsFwd();
  if(!$chir_id) {
    $chir =& $sejour->_ref_praticien;
  }
  $patient =& $sejour->_ref_patient;
}

// On r�cup�re l'op�ration
$op = new COperation;
$op->load($operation_id);
if ($op->_id) {
  $op->loadRefs();
  $op->loadRefsNotes();
  $op->_ref_chir->loadRefFunction();

  // On v�rifie que l'utilisateur a les droits sur l'operation
  if (!array_key_exists($op->chir_id, $listPraticiens)) {
    CAppUI::setMsg("Vous n'avez pas acc�s � cette op�ration", UI_MSG_WARNING);
    CAppUI::redirect("m=$m&tab=$tab&operation_id=0");
  }

  $op->loadRefs();
  foreach($op->_ref_actes_ccam as $acte) {
    $acte->loadRefExecutant();
  }	
	
  $sejour =& $op->_ref_sejour;
  $sejour->loadRefsFwd();
  $sejour->makeCancelAlerts($op->_id);
  $chir =& $op->_ref_chir;
  $patient =& $sejour->_ref_patient;
}

// Liste des types d'anesth�sie
$listAnesthType = new CTypeAnesth;
$orderanesth = "name";
$listAnesthType = $listAnesthType->loadList(null,$orderanesth);

// Liste des anesth�sistes
$anesthesistes = $chir->loadAnesthesistes(PERM_READ);

// Compl�ments de chargement du s�jour
$sejour->makeDatesOperations();
$sejour->loadNDA();
$sejour->loadRefsNotes();

// Chargements de chargement du patient
$patient->loadRefsSejours();
$patient->loadRefsFwd();
$patient->loadRefsCorrespondants();

$correspondantsMedicaux = array();
if ($patient->_ref_medecin_traitant->_id) {
  $correspondantsMedicaux["traitant"] = $patient->_ref_medecin_traitant;
}
foreach($patient->_ref_medecins_correspondants as $correspondant) {
  $correspondantsMedicaux["correspondants"][] = $correspondant->_ref_medecin;
}

$medecin_adresse_par = "";
if ($sejour->adresse_par_prat_id && ($sejour->adresse_par_prat_id != $patient->_ref_medecin_traitant->_id)) {
  $medecin_adresse_par = new CMedecin();
  $medecin_adresse_par->load($sejour->adresse_par_prat_id);
}

// Chargement des etablissements externes
$order = "nom";
$etab = new CEtabExterne();
$listEtab = $etab->loadList(null, $order);

$sejours =& $patient->_ref_sejours;

$config = CAppUI::conf("dPplanningOp CSejour");
$hours = range($config["heure_deb"], $config["heure_fin"]);
$mins = range(0, 59, $config["min_intervalle"]);

$config = CAppUI::conf("dPplanningOp COperation");
$hours_duree = range($config["duree_deb"], $config["duree_fin"]);
$hours_urgence = range($config["hour_urgence_deb"], $config["hour_urgence_fin"]);
$mins_duree = range(0, 59, $config["min_intervalle"]);

$config = CAppUI::conf("dPplanningOp CSejour");
$heure_sortie_ambu   = $config["heure_sortie_ambu"];
$heure_sortie_autre  = $config["heure_sortie_autre"];
$heure_entree_veille = $config["heure_entree_veille"];
$heure_entree_jour   = $config["heure_entree_jour"];

// R�cup�ration de la liste des services
$where = array();
$where["externe"]  = "= '0'";
$service = new CService;
$services = $service->loadGroupList($where);

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

$smarty->assign("modurgence", 1);
$smarty->assign("today"     , $today);
$smarty->assign("tomorow"   , $tomorow);

$smarty->assign("categorie_prat", $categorie_prat);
$smarty->assign("listPraticiens", $listPraticiens);
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

$smarty->assign("correspondantsMedicaux", $correspondantsMedicaux);
$smarty->assign("listEtab", $listEtab);
$smarty->assign("medecin_adresse_par", $medecin_adresse_par);

$smarty->display("vw_edit_planning.tpl");

?>