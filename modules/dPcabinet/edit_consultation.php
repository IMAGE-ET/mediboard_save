<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $dPconfig;

$can->needsEdit();

$vue2_default = isset($AppUI->user_prefs["AFFCONSULT"]) ? $AppUI->user_prefs["AFFCONSULT"] : 0 ;

$date         = mbGetValueFromGetOrSession("date", mbDate());
$vue          = mbGetValueFromGetOrSession("vue2", $vue2_default);
$today        = mbDate();
$hour         = mbTime(null);

$prat_id      = mbGetValueFromGetOrSession("chirSel", $AppUI->user_id);
$selConsult   = mbGetValueFromGetOrSession("selConsult", null);


$etablissements = CMediusers::loadEtablissements(PERM_EDIT);
$consult = new CConsultation();

if(isset($_GET["date"])) {
  $selConsult = null;
  mbSetValueToSession("selConsult", null);
}

// Test compliqu� afin de savoir quelle consultation charger
if(isset($_GET["selConsult"])) {
  if($consult->load($selConsult) && $consult->patient_id) {
    $consult->loadRefsFwd();
    $prat_id = $consult->_ref_plageconsult->chir_id;
    mbSetValueToSession("chirSel", $prat_id);
  } else {
    $consult = new CConsultation();
    $selConsult = null;
    mbSetValueToSession("selConsult");
  }
} else {
  if($consult->load($selConsult) && $consult->patient_id) {
    $consult->loadRefsFwd();
    if($prat_id !== $consult->_ref_plageconsult->chir_id) {
      $consult = new CConsultation();
      $selConsult = null;
      mbSetValueToSession("selConsult");
    }
  }
}

// On charge le praticien
$userSel = new CMediusers;
$userSel->load($prat_id);
$userSel->loadRefs();
$canUserSel = $userSel->canDo();

// V�rification des droits sur les praticiens
$listChir = $userSel->loadPraticiens(PERM_EDIT);

if (!$userSel->isPraticien()) {
  $AppUI->setMsg("Vous devez selectionner un praticien", UI_MSG_ALERT);
  $AppUI->redirect("m=dPcabinet&tab=0");
}

$canUserSel->needsEdit(array("chirSel"=>0));

$anesth = new CTypeAnesth;
$orderanesth = "name";
$anesth = $anesth->loadList(null,$orderanesth);

// Consultation courante
$consult->_ref_chir =& $userSel;
if($consult->consultation_id) {
  $consult->loadRefs();
  $consult->loadAides($userSel->user_id);
  $consult->_ref_consult_anesth->loadAides($userSel->user_id);
  if($consult->_ref_consult_anesth->consultation_anesth_id) {
    $consult->_ref_consult_anesth->loadRefs();
    if($consult->_ref_consult_anesth->_ref_operation->operation_id) {
      $consult->_ref_consult_anesth->_ref_operation->loadAides($userSel->user_id);
    }
  }
  $patient =& $consult->_ref_patient;
  $patient->loadRefs();
  $patient->loadStaticCIM10($userSel->user_id);
  foreach($patient->_ref_consultations as $key => $curr_cons) {
    $patient->_ref_consultations[$key]->loadRefsFwd();
  }
  foreach($patient->_ref_sejours as $key => $curr_sejour) {
    $patient->_ref_sejours[$key]->loadRefsFwd();
    $patient->_ref_sejours[$key]->loadRefsOperations();
    foreach($patient->_ref_sejours[$key]->_ref_operations as $keyOp => $op) {
      $patient->_ref_sejours[$key]->_ref_operations[$keyOp]->loadRefsFwd();
    }
  }
  // Affecter la date de la consultation
  $date = $consult->_ref_plageconsult->date;
  $codePraticienEc = null;
  $urlDHE = "#";
  $urlDHEParams = array();
  if(CModule::getInstalled("dPsante400") && ($dPconfig["interop"]["mode_compat"] == "medicap")) {
    $tmpEtab = array();
    foreach($etablissements as $etab) {
      $idExt = new CIdSante400;
      $idExt->loadLatestFor($etab);
      if($idExt->id400) {
        $tmpEtab[$idExt->id400] = $etab;
      }
    }
    $etablissements = $tmpEtab;
    
    // Construction de l'URL
    $urlDHE = $dPconfig["interop"]["base_url"];
    $urlDHEParams["logineCap"]       = "";
    $urlDHEParams["codeAppliExt"]    = "mediboard";
    $urlDHEParams["patIdentLogExt"]  = $patient->patient_id;
    $urlDHEParams["patNom"]          = $patient->nom;
    $urlDHEParams["patPrenom"]       = $patient->prenom;
    $urlDHEParams["patNomJF"]        = $patient->nom_jeune_fille;
    $urlDHEParams["patSexe"]         = $patient->sexe == "m" ? "1" : "2";
    $urlDHEParams["patDateNaiss"]    = $patient->_naissance;
    $urlDHEParams["patAd1"]          = $patient->adresse;
    $urlDHEParams["patCP"]           = $patient->cp;
    $urlDHEParams["patVille"]        = $patient->ville;
    $urlDHEParams["patTel1"]         = $patient->tel;
    $urlDHEParams["patTel2"]         = $patient->tel2;
    $urlDHEParams["patTel3"]         = "";
    $urlDHEParams["patNumSecu"]      = substr($patient->matricule, 0, 13);
    $urlDHEParams["patCleNumSecu"]   = substr($patient->matricule, 13, 2);
    $urlDHEParams["interDatePrevue"] = "";

    $idExt = new CIdSante400;
    $idExt->loadLatestFor($patient);
    $patIdentEc = $idExt->id400;
    $urlDHEParams["patIdentEc"]      = $patIdentEc;

    $idExt = new CIdSante400;
    $idExt->loadLatestFor($userSel);
    $codePraticienEc = $idExt->id400;
    $urlDHEParams["codePraticienEc"] = $codePraticienEc;
  }
} else {
  $consult->_ref_consult_anesth->consultation_anesth_id = 0;
  $codePraticienEc = null;
  $urlDHE = "#";
  $urlDHEParams = array();
}

//mbTrace($urlDHE, "URL DHE");
//mbTrace($urlDHEParams, "URL DHE Params");


// R�cup�ration des mod�les
$whereCommon = array();
$whereCommon["object_id"] = "IS NULL";
if($consult->_ref_consult_anesth->consultation_anesth_id){
  $whereCommon[] = "`object_class` = 'CConsultAnesth'";
}else{
  $whereCommon[] = "`object_class` = 'CConsultation'";
}

$order = "nom";

// Mod�les de l'utilisateur
$listModelePrat = array();
if ($userSel->user_id) {
  $where = $whereCommon;
  $where["chir_id"] = db_prepare("= %", $userSel->user_id);
  $listModelePrat = new CCompteRendu;
  $listModelePrat = $listModelePrat->loadlist($where, $order);
}

// Mod�les de la fonction
$listModeleFunc = array();
if ($userSel->user_id) {
  $where = $whereCommon;
  $where["function_id"] = db_prepare("= %", $userSel->function_id);
  $listModeleFunc = new CCompteRendu;
  $listModeleFunc = $listModeleFunc->loadlist($where, $order);
}

// R�cup�ration des tarifs
$order = "description";
$where = array();
$where["chir_id"] = "= '$userSel->user_id'";
$tarifsChir = new CTarif;
$tarifsChir = $tarifsChir->loadList($where, $order);
$where = array();
$where["function_id"] = "= '$userSel->function_id'";
$tarifsCab = new CTarif;
$tarifsCab = $tarifsCab->loadList($where, $order);

// Chargement des aides � la saisie
$antecedent = new CAntecedent();
$antecedent->loadAides($userSel->user_id);

$addiction = new CAddiction();
$addiction->loadAides($userSel->user_id);

$traitement = new CTraitement();
$traitement->loadAides($userSel->user_id);

$techniquesComp = new CTechniqueComp();
$techniquesComp->loadAides($userSel->user_id);

$examComp = new CExamComp();
$examComp->loadAides($userSel->user_id);

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("codePraticienEc", $codePraticienEc);
$smarty->assign("urlDHE"         , $urlDHE);
$smarty->assign("urlDHEParams"   , $urlDHEParams);
$smarty->assign("etablissements" , $etablissements);
$smarty->assign("date"           , $date);
$smarty->assign("hour"           , $hour);
$smarty->assign("vue"            , $vue);
$smarty->assign("today"          , $today);
$smarty->assign("userSel"        , $userSel);
$smarty->assign("listModelePrat" , $listModelePrat);
$smarty->assign("listModeleFunc" , $listModeleFunc);
$smarty->assign("tarifsChir"     , $tarifsChir);
$smarty->assign("tarifsCab"      , $tarifsCab);
$smarty->assign("anesth"         , $anesth);
$smarty->assign("consult"        , $consult);
$smarty->assign("antecedent"     , $antecedent);
$smarty->assign("traitement"     , $traitement);
$smarty->assign("addiction"      , $addiction);
$smarty->assign("techniquesComp" , $techniquesComp);
$smarty->assign("examComp"       , $examComp);
$smarty->assign("_is_anesth"     , $consult->_is_anesth);  
$smarty->assign("noReglement"    , 0);

if($consult->_is_anesth) {
  $secs = array();
  for ($i = 0; $i < 60; $i++) {
    $secs[] = $i;
  }
  $mins = array();
  for ($i = 0; $i < 15; $i++) {
    $mins[] = $i;
  }
  
  $smarty->assign("secs"          , $secs);
  $smarty->assign("mins"          , $mins);
  $smarty->assign("consult_anesth", $consult->_ref_consult_anesth);
  $smarty->display("edit_consultation_anesth.tpl");
} else {
	$vue_accord = isset($AppUI->user_prefs["MODCONSULT"]) ? $AppUI->user_prefs["MODCONSULT"] : 0 ;
  if($vue_accord){
    $smarty->display("edit_consultation_accord.tpl");
  }else{  
    $smarty->display("edit_consultation_classique.tpl");
  }
}
?>