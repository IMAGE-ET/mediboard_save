<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/


global $m;

$user = CUser::get();

$date  = CValue::getOrSession("date", mbDate());
$vue   = CValue::getOrSession("vue2", CAppUI::pref("AFFCONSULT", 0));
$today = mbDate();
$hour  = mbTime(null);

$now = mbDateTime();

CMbObject::$useObjectCache = false;

if(!isset($current_m)){
  $current_m = CValue::get("current_m", $m);
}

$prat_id      = CValue::getOrSession("chirSel", $user->_id);
$selConsult   = CValue::getOrSession("selConsult", null);

$listChirs = new CMediusers;
if(CAppUI::pref("pratOnlyForConsult", 1)) {
  $listChirs = $listChirs->loadPraticiens(null);
} else {
  $listChirs = $listChirs->loadProfessionnelDeSante(null);
}

$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes();

$consult = new CConsultation();

$listPrats = array();

if($current_m == "dPurgences"){
  if (!$selConsult) {
    CAppUI::setMsg("Vous devez selectionner une consultation", UI_MSG_ALERT);
    CAppUI::redirect("m=dPurgences&tab=0");
  }
  $user = CAppUI::$user;
  $group = CGroups::loadCurrent();
  $listPrats = $user->loadPraticiens(PERM_READ, $group->service_urgences_id);
}

$tabSejour = array();

// Chargement des banques
$orderBanque = "nom ASC";
$banque = new CBanque();
$banques = $banque->loadList(null,$orderBanque);

if(isset($_GET["date"])) {
  $selConsult = null;
  CValue::setSession("selConsult", null);
}

// Test compliqu� afin de savoir quelle consultation charger
if (isset($_GET["selConsult"])) {
  if ($consult->load($selConsult) && $consult->patient_id) {
    $consult->loadRefPlageConsult();
    $prat_id = $consult->_ref_plageconsult->chir_id;
    CValue::setSession("chirSel", $prat_id);
  } else {
    $consult = new CConsultation();
    $selConsult = null;
    CValue::setSession("selConsult");
  }
} 
else {
  if ($consult->load($selConsult) && $consult->patient_id) {
    $consult->loadRefPlageConsult();
    if($prat_id !== $consult->_ref_plageconsult->chir_id) {
      $consult = new CConsultation();
      $selConsult = null;
      CValue::setSession("selConsult");
    }
  }
}

// On charge le praticien
$userSel = new CMediusers;
$userSel->load($prat_id);
$userSel->loadRefs();
$canUserSel = $userSel->canDo();

if ((!$userSel->isMedical()) && ($current_m != "dPurgences")) {
  CAppUI::setMsg("Vous devez selectionner un professionnel de sant�", UI_MSG_ALERT);
  CAppUI::redirect("m=dPcabinet&tab=0");
}

$anesth = new CTypeAnesth;
$orderanesth = "name";
$anesth = $anesth->loadList(null,$orderanesth);

$consultAnesth =& $consult->_ref_consult_anesth;

// Consultation courante
$consult->_ref_chir =& $userSel;

// Chargement de la consultation
if ($consult->_id) {
  $consult->loadRefs();  
  
  // Chargment de la consultation d'anesth�sie
  if ($consultAnesth->_id) {
    $consultAnesth->loadRefs();
    if ($consultAnesth->_ref_operation->_id || $consultAnesth->_ref_sejour->_id) {
      $consultAnesth->_ref_operation->loadExtCodesCCAM();
      $consultAnesth->_ref_operation->loadRefs();
      $consultAnesth->_ref_sejour->loadRefPraticien();
    }
  }
 
  // Chargement du patient
  $patient =& $consult->_ref_patient;
  $patient->loadRefs();
  $patient->loadRefsNotes();  
  $patient->loadRefPhotoIdentite();
  
  // Chargement de ses consultations
  foreach ($patient->_ref_consultations as &$_consultation) {
    $_consultation->loadRefsFwd();
    $_consultation->_ref_chir->loadRefFunction();
    
  }
  
  // Chargement de ses s�jours
  foreach ($patient->_ref_sejours as &$_sejour) {
    $_sejour->loadRefsFwd();
    $_sejour->loadRefsOperations();
    foreach ($_sejour->_ref_operations as &$_operation) {
      $_operation->loadRefsFwd();
      $_operation->_ref_chir->loadRefFunction();
      // Tableaux de correspondances operation_id => sejour_id
      $tabSejour[$_operation->_id] = $_sejour->_id;
    }
  }
  
  // Affecter la date de la consultation
  $date = $consult->_ref_plageconsult->date;
} else {
  $consultAnesth->consultation_anesth_id = 0;
}

if ($consult->_id){
  $consult->canDo();
}

if ($consult->_id && CModule::getActive("fse")){
  // Chargement des identifiants LogicMax
  $fse = CFseFactory::createFSE();
  if ($fse) {
    $fse->loadIdsFSE($consult);
    $fse->makeFSE($consult);
    
    $cps = CFseFactory::createCPS()->loadIdCPS($consult->_ref_chir);
    
    CFseFactory::createCV()->loadIdVitale($consult->_ref_patient);
  }
}

$antecedent = new CAntecedent();
$traitement = new CTraitement();
$techniquesComp = new CTechniqueComp();
$examComp = new CExamComp();

$consult->loadExtCodesCCAM();
$consult->getAssociationCodesActes();
$consult->loadPossibleActes();
$consult->_ref_chir->loadRefFunction();

// Chargement du dossier medical du patient de la consultation
if ($consult->patient_id) {
  $consult->_ref_patient->loadRefDossierMedical();
  $consult->_ref_patient->_ref_dossier_medical->updateFormFields();
}

// Chargement des actes NGAP
$consult->loadRefsActesNGAP();

// Chargement du medecin adress� par
if ($consult->adresse_par_prat_id) {
  $medecin_adresse_par = new CMedecin();
  $medecin_adresse_par->load($consult->adresse_par_prat_id);
  $consult->_ref_adresse_par_prat = $medecin_adresse_par;
}

$listServicesUrgence = array();

if ($consult->sejour_id) {
  $consult->loadRefSejour();
}

// Chargement du sejour
if ($consult->_ref_sejour && $consult->_ref_sejour->_id){
  $consult->_ref_sejour->loadExtDiagnostics();
  $consult->_ref_sejour->loadRefDossierMedical();
  $consult->_ref_sejour->loadNDA();
  
  $consult->_ref_chir->isUrgentiste();
  if ($consult->_ref_chir->_is_urgentiste) {
    // Mise en session du rpu_id
    $_SESSION["dPurgences"]["rpu_id"] = $consult->_ref_sejour->_ref_rpu->_id;
    $consult->_ref_sejour->_ref_rpu->loadRefSejourMutation();
    
    // Chargement des boxes d'urgences
    $listServicesUrgence = CService::loadServicesUrgence();
  }
}

// Initialisation d'un acte NGAP
$acte_ngap = new CActeNGAP();
$acte_ngap->quantite = 1;
$acte_ngap->coefficient = 1;
$acte_ngap->loadListExecutants();

$acte_tarmed = null;
//Si le module Tarmed est install� chargement d'un acte
if(CModule::getInstalled("tarmed")){
  //Initialisation d'un acte Tarmed
  $acte_tarmed = new CActeTarmed();
  $acte_tarmed->quantite = 1;
  $acte_tarmed->loadListExecutants();
  $acte_tarmed->loadRefExecutant();
}
// Tableau de contraintes pour les champs du RPU
// Contraintes sur le mode d'entree / provenance
//$contrainteProvenance[6] = array("", 1, 2, 3, 4);
$contrainteProvenance[7] = array("", 1, 2, 3, 4);
$contrainteProvenance[8] = array("", 5, 8);

// Contraintes sur le mode de sortie / destination
$contrainteDestination["mutation"]  = array("", 1, 2, 3, 4);
$contrainteDestination["transfert"] = array("", 1, 2, 3, 4);
$contrainteDestination["normal"] = array("", 6, 7);

// Contraintes sur le mode de sortie / orientation
$contrainteOrientation["mutation"] = array("", "HDT", "HO", "SC", "SI", "REA", "UHCD", "MED", "CHIR", "OBST");
$contrainteOrientation["transfert"] = array("", "HDT", "HO", "SC", "SI", "REA", "UHCD", "MED", "CHIR", "OBST");
$contrainteOrientation["normal"] = array("", "FUGUE", "SCAM", "PSA", "REO");

$list_etat_dents = array();
if ($consult->_id) {
  if ($consult->_ref_patient->_ref_dossier_medical->_id) {
    $consult->_ref_patient->_ref_dossier_medical->loadRefsEtatsDents();
    $etat_dents = $consult->_ref_patient->_ref_dossier_medical->_ref_etats_dents;
    foreach ($etat_dents as $etat) {
      $list_etat_dents[$etat->dent] = $etat->etat;
    }
  }
}

$consult->loadRefsActesTarmed();
$soustotal_base = 0;
$soustotal_dh   = 0;
if ($consult->_ref_actes_tarmed) {
  foreach($consult->_ref_actes_tarmed as $acte){
    $soustotal_base += $acte->montant_base;
    $soustotal_dh   += $acte->montant_depassement; 
  }
}
$total = $soustotal_base + $soustotal_dh;
$total = round($total,2);

// Tout utilisateur peut consulter en lecture seule une consultation de s�jour
$consult->canEdit();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("isPrescriptionInstalled", CModule::getActive("dPprescription"));
$smarty->assign("contrainteProvenance" , $contrainteProvenance );
$smarty->assign("contrainteDestination", $contrainteDestination);
$smarty->assign("contrainteOrientation", $contrainteOrientation);

$smarty->assign("listServicesUrgence", $listServicesUrgence);

$smarty->assign("acte_ngap"      , $acte_ngap);
$smarty->assign("acte_tarmed"    , $acte_tarmed);
$smarty->assign("tabSejour"      , $tabSejour);
$smarty->assign("banques"        , $banques);
$smarty->assign("listAnesths"    , $listAnesths);
$smarty->assign("listChirs"      , $listChirs);
$smarty->assign("date"           , $date);
$smarty->assign("hour"           , $hour);
$smarty->assign("vue"            , $vue);
$smarty->assign("today"          , $today);
$smarty->assign("userSel"        , $userSel);
$smarty->assign("anesth"         , $anesth);
$smarty->assign("consult"        , $consult);
$smarty->assign("antecedent"     , $antecedent);
$smarty->assign("traitement"     , $traitement);
$smarty->assign("techniquesComp" , $techniquesComp);
$smarty->assign("examComp"       , $examComp);
$smarty->assign("_is_anesth"     , $consult->_is_anesth);
$smarty->assign("_is_dentiste"   , $consult->_is_dentiste);
$smarty->assign("current_m"      , $current_m);
$smarty->assign("list_etat_dents", $list_etat_dents);
$smarty->assign("now"            , $now);
$smarty->assign("listPrats"      , $listPrats);

if(CModule::getActive("dPprescription")){
  $smarty->assign("line"           , new CPrescriptionLineMedicament());
}

$smarty->assign("soustotal_base" , $soustotal_base);
$smarty->assign("soustotal_dh"   , $soustotal_dh);
$smarty->assign("total"          , $total);
if ($consult->_is_dentiste) {
  $devenirs_dentaires = $consult->_ref_patient->loadRefsDevenirDentaire();
  
  foreach ($devenirs_dentaires as &$devenir_dentaire) {
    $etudiant = $devenir_dentaire->loadRefEtudiant();
    $etudiant->loadRefFunction();
    $actes_dentaires  = $devenir_dentaire->countRefsActesDentaires();
  }
  
  $smarty->assign("devenirs_dentaires", $devenirs_dentaires);
}

if($consult->_is_anesth) {
  $nextSejourAndOperation = $consult->_ref_patient->getNextSejourAndOperation($consult->_ref_plageconsult->date);
  $secs = range(0, 60-1, 1);
  $mins = range(0, 15-1, 1);
  
  $smarty->assign("nextSejourAndOperation", $nextSejourAndOperation);
  $smarty->assign("secs"                  , $secs);
  $smarty->assign("mins"                  , $mins);
  $smarty->assign("consult_anesth"        , $consultAnesth);
  $smarty->display("../../dPcabinet/templates/edit_consultation_anesth.tpl");  
} else {
  if(CAppUI::pref("MODCONSULT")){
    $smarty->display("../../dPcabinet/templates/edit_consultation_accord.tpl");
  } else{  
    $smarty->display("../../dPcabinet/templates/edit_consultation_classique.tpl");
  }
}
?>