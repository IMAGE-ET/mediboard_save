<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m, $tab, $dPconfig;

$can->needsEdit();

// Liste des Etablissements selon Permissions
$etablissements = CMediusers::loadEtablissements(PERM_READ);

$operation_id = mbGetValueFromGetOrSession("operation_id");
$sejour_id    = mbGetValueFromGetOrSession("sejour_id");
$chir_id      = mbGetValueFromGet("chir_id");
$patient_id   = mbGetValueFromGet("pat_id");
$today        = mbDate();
$tomorow      = mbDate("+1 DAY");

// L'utilisateur est-il un praticien
$chir = new CMediusers;
$chir->load($AppUI->user_id);
if ($chir->isPraticien() and !$chir_id) {
  $chir_id = $chir->user_id;
}

// Chargement du praticien
$chir = new CMediusers;
if ($chir_id) {
  $chir->load($chir_id);
}
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
foreach($listPraticiens as &$_prat){
  $_prat->loadRefsFwd();
  $categorie_prat[$_prat->_id] = $_prat->_ref_discipline->categorie;
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
if ($operation_id && $op->load($operation_id)) {
  $op->loadRefs();
  $sejour =& $op->_ref_sejour;
  $sejour->loadRefsFwd();
  $chir =& $op->_ref_chir;
  $prat =& $sejour->_ref_praticien;
  
  $patient =& $sejour->_ref_patient;
  // On v�rifie que l'utilisateur a les droits sur l'operation et le sejour
  /*if(!$op->canEdit() || !$sejour->canEdit()) {
    $AppUI->setMsg("Vous n'avez pas acc�s � cette op�ration", UI_MSG_WARNING);
    $AppUI->redirect("m=$m&tab=$tab&operation_id=0");
  }*/
  // Ancienne methode
  if (!array_key_exists($op->chir_id, $listPraticiens)) {
    $AppUI->setMsg("Vous n'avez pas acc�s � cette op�ration", UI_MSG_WARNING);
    $AppUI->redirect("m=$m&tab=$tab&operation_id=0");
  }
}

//mbTrace($sejour);

$patient->loadRefsSejours();
$sejours =& $patient->_ref_sejours;

//mbTrace($sejour);
// R�cup�ration des mod�les

// Mod�les de l'utilisateur
$listModelePrat = array();
$order = "nom";
if ($chir->user_id) {
  $where = array();
  $where["object_class"] = "= 'COperation'";
  $where["chir_id"] = "= '$chir->user_id'";
  $listModelePrat = CCompteRendu::loadModeleByCat("Hospitalisation", $where, $order, true);
}

// Mod�les de la fonction
$listModeleFunc = array();
if ($chir->user_id) {
  $where = array();
  $where["object_class"] = "= 'COperation'";
  $where["function_id"] = "= '$chir->function_id'";
  $listModeleFunc = CCompteRendu::loadModeleByCat("Hospitalisation", $where, $order, true);
}

// Packs d'hospitalisation
$listPack = array();
if($chir->user_id) {
  $where = array();
  $where["chir_id"] = "= '$chir->user_id'";
  $listPack = new CPack;
  $listPack = $listPack->loadlist($where, $order);
}

$config =& $dPconfig["dPplanningOp"]["CSejour"];
$hours = range($config["heure_deb"], $config["heure_fin"]);
$mins = range(0, 59, $config["min_intervalle"]);
$heure_sortie_ambu   = $config["heure_sortie_ambu"];
$heure_sortie_autre  = $config["heure_sortie_autre"];
$heure_entree_veille = $config["heure_entree_veille"];
$heure_entree_jour   = $config["heure_entree_jour"];



$config =& $dPconfig["dPplanningOp"]["COperation"];
$hours_duree = range($config["duree_deb"], $config["duree_fin"]);
$hours_urgence = range($config["hour_urgence_deb"], $config["hour_urgence_fin"]);
$mins_duree = range(0, 59, $config["min_intervalle"]);



// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("canSante400", CModule::getCanDo("dPsante400"));


$smarty->assign("heure_sortie_ambu",   $heure_sortie_ambu);
$smarty->assign("heure_sortie_autre",  $heure_sortie_autre);
$smarty->assign("heure_entree_veille", $heure_entree_veille);
$smarty->assign("heure_entree_jour",   $heure_entree_jour);

$smarty->assign("op"        , $op);
$smarty->assign("plage"     , $op->plageop_id ? $op->_ref_plageop : new CPlageOp );
$smarty->assign("sejour"    , $sejour);
$smarty->assign("chir"      , $chir);
$smarty->assign("praticien" , $prat);
$smarty->assign("patient"   , $patient );
$smarty->assign("sejours"   , $sejours);
$smarty->assign("modurgence", 0);
$smarty->assign("today"     , $today);
$smarty->assign("tomorow"   , $tomorow);
$smarty->assign("msg_alert" , "");

$smarty->assign("categorie_prat", $categorie_prat);
$smarty->assign("listPraticiens", $listPraticiens);
$smarty->assign("listModelePrat", $listModelePrat);
$smarty->assign("listModeleFunc", $listModeleFunc);
$smarty->assign("listPack"      , $listPack      );
$smarty->assign("etablissements", $etablissements);

$smarty->assign("hours"        , $hours);
$smarty->assign("mins"         , $mins);
$smarty->assign("hours_duree"  , $hours_duree);
$smarty->assign("hours_urgence", $hours_urgence);
$smarty->assign("mins_duree"   , $mins_duree);

$smarty->display("vw_edit_planning.tpl");

?>