<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage soins
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CAppUI::requireModuleFile("dPhospi", "inc_vw_affectations");

CCanDo::checkRead();

$group = CGroups::loadCurrent();

// Filtres
$date           = CValue::getOrSession("date", mbDate());
$datetime       = mbDateTime(); 
$mode           = CValue::getOrSession("mode", 0);
$service_id     = CValue::getOrSession("service_id");
$praticien_id   = CValue::getOrSession("praticien_id");
$_active_tab    = CValue::get("_active_tab");
$type_admission = CValue::getOrSession("type");

$tab_sejour = array();

// Chargement de l'utilisateur courant
$userCourant = CMediusers::get();

if(CModule::getActive("dPprescription")){
  $prescription_sejour = new CPrescription();
}

$_is_praticien = $userCourant->isPraticien();

// Preselection du praticien_id
if($_is_praticien && !$service_id && !$praticien_id) {
  $praticien_id = $userCourant->user_id;
}

// R�cup�ration de la liste des services
$where = array();
$where["externe"]  = "= '0'";
$service = new CService;
$services = $service->loadGroupList($where);
  
$changeSejour = CValue::get("service_id") || CValue::get("praticien_id");
$changeSejour = $changeSejour || (!$service_id && !$praticien_id);

if($changeSejour) {
  $sejour_id = null;
  CValue::setSession("sejour_id");
} else {
  $sejour_id = CValue::getOrSession("sejour_id");
}  

// R�cup�ration du service � ajouter/�diter
$totalLits = 0;

// A passer en variable de configuration
$heureLimit = "16:00:00";

// Initialisation
$service = new CService;
$groupSejourNonAffectes = array();
$sejoursParService = array();

// Chargement de la liste de praticiens
$prat = new CMediusers();
$praticiens = $prat->loadPraticiens(PERM_READ);

// Restructuration minimal des services
global $sejoursParService;
$sejoursParService = array();
function cacheLit($affectation) {
  
  // Cache des lits
  $lit_id = $affectation->lit_id;
  static $lits = array();
  if (!array_key_exists($lit_id, $lits)) {
    $lit = new CLit();
    $lit->load($lit_id);
    $lits[$lit_id] = $lit;     
  }
  
  $lit = $lits[$lit_id];
  $lit->_ref_affectations[$affectation->_id] = $affectation;

  // Cache des chambres
  $chambre_id = $lit->chambre_id;
  static $chambres = array();
  if (!array_key_exists($chambre_id, $chambres)) {
    $chambre = new CChambre();
    $chambre->load($chambre_id);
    $chambres[$chambre_id] = $chambre;
  }

  $chambre = $chambres[$chambre_id];
  $chambre->_ref_lits[$lit_id] = $lit;
  
  // Cache de services
  global $sejoursParService;
  $service_id = $chambre->service_id;
  if (!array_key_exists($service_id, $sejoursParService)) {
    $service = new CService();
    $service->load($service_id);
    $sejoursParService[$service_id] = $service;
  }

  $service = $sejoursParService[$service_id];
  $service->_ref_chambres[$chambre_id] = $chambre;
}

// Chargement du praticien
if ($praticien_id){
  $praticien = new CMediusers();
  $praticien->load($praticien_id);
}

$anesth = new CMediusers();
$anesthesistes = array_keys($anesth->loadAnesthesistes());

// Si seulement le praticien est indiqu�
if($praticien_id && !$service_id){
  $sejours = array();
  $sejour = new CSejour();
  $where = array();
  $where["group_id"] = "= '$group->_id'";

  if ($praticien->isAnesth()) {
    $ljoin = array();
    $ljoin["operations"] = "operations.sejour_id = sejour.sejour_id";
    $ljoin["plagesop"]   = "operations.plageop_id = plagesop.plageop_id";
    $where[] = "operations.anesth_id = '$praticien_id' OR (operations.anesth_id IS NULL AND plagesop.anesth_id = '$praticien_id')";
  } else {
    $where["praticien_id"] = " = '$praticien_id'";
  }
  
  $where["entree_prevue"] = " <= '$date 23:59:59'";
  $where["sortie_prevue"] = " >= '$date 00:00:00'";
  $where["annule"] = " = '0'";
  $where[] = $type_admission ? "type = '$type_admission'" : "type != 'urg' AND type != 'exte'";
  
  if ($praticien->isAnesth()) {
    $sejours = $sejour->loadList($where, null, null, null, $ljoin);
  } else {
    $sejours = $sejour->loadList($where);
  }
    
  foreach($sejours as $_sejour){
    if($_is_praticien && $_sejour->praticien_id == $userCourant->user_id){
      $tab_sejour[$_sejour->_id]= $_sejour;
    }
    $affectations = array();
    $affectation = new CAffectation();
    $where = array();
    $where["sejour_id"] = " = '$_sejour->_id'";
    $where["entree"] = "<= '$date 23:59:59'";
    $where["sortie"] = ">= '$date 00:00:00'";
    $affectations = $affectation->loadList($where);

    if(count($affectations) >= 1){
      foreach($affectations as $_affectation){
        $_affectation->loadRefsAffectations();
        cacheLit($_affectation);
      }
    } else {
      $_sejour->loadRefsPrescriptions();
       $_sejour->loadRefPatient();
      $_sejour->loadRefPraticien();
      $_sejour->_ref_praticien->loadRefFunction();
      $_sejour->loadNDA();
      $sejoursParService["NP"][$_sejour->_id] = $_sejour;
    }
  }
}

foreach ($sejoursParService as $key => $_service) {
  if($key != "NP"){ 
    $sorter = CMbArray::pluck($_service->_ref_chambres, "nom");
    array_multisort($sorter, SORT_ASC, $_service->_ref_chambres);
  
    foreach ($_service->_ref_chambres as $_chambre) {
      foreach ($_chambre->_ref_lits as $_lit) {
        foreach ($_lit->_ref_affectations as $_affectation) {
          $_affectation->loadRefsAffectations();
          $_affectation->loadRefSejour();
          $_sejour = $_affectation->_ref_sejour;
          if($_is_praticien && $_sejour->praticien_id == $userCourant->user_id){
            $tab_sejour[$_sejour->_id]= $_sejour;
          }
          $_sejour->loadRefsPrescriptions();
          $_sejour->loadRefPatient();
          $_sejour->loadRefPraticien();
          $_sejour->_ref_praticien->loadRefFunction();
          $_sejour->loadNDA();
      
          if($_sejour->_ref_prescriptions){
            if(array_key_exists('sejour', $_sejour->_ref_prescriptions)){
               $prescription_sejour = $_sejour->_ref_prescriptions["sejour"];
               $prescription_sejour->countNoValideLines();
            }
          }
        }
      }
    }
  }
}


// Tri des sejours par services
ksort($sejoursParService);

// R�cuperation du sejour s�lectionn�
$sejour = new CSejour;
$sejour->load($sejour_id);
$sejour->loadRefs();
$sejour->loadRefsPrescriptions();
$sejour->loadRefsDocs();


$medecin_adresse_par = new CMedecin();
$medecin_adresse_par->load($sejour->adresse_par_prat_id);
$sejour->_adresse_par_prat = $medecin_adresse_par->_view;

$etab = new CEtabExterne();
$etab->load($sejour->etablissement_entree_id);
$sejour->_ref_etablissement_provenance = $etab->_view;

if($service_id){
  // Chargement des s�jours � afficher
  if($service_id == "NP") {

    // Liste des patients � placer
    $order = "entree_prevue ASC";
      
    // Admissions de la veille
    $dayBefore = mbDate("-1 days", $date);
    $where = array(
      "entree_prevue" => "BETWEEN '$dayBefore 00:00:00' AND '$date 00:00:00'",
      "type" => $type_admission ? " = '$type_admission'" : "!= 'exte'",
      "annule" => "= '0'"
    );
      
    $groupSejourNonAffectes["veille"] = loadSejourNonAffectes($where, $order, $praticien_id);
      
    // Admissions du matin
    $where = array(
      "entree_prevue" => "BETWEEN '$date 00:00:00' AND '$date ".mbTime("-1 second",$heureLimit)."'",
      "type" => $type_admission ? " = '$type_admission'" : "!= 'exte'",
      "annule" => "= '0'"
    );
      
    $groupSejourNonAffectes["matin"] = loadSejourNonAffectes($where, $order, $praticien_id);
      
    // Admissions du soir
    $where = array(
      "entree_prevue" => "BETWEEN '$date $heureLimit' AND '$date 23:59:59'",
      "type" => $type_admission ? " = '$type_admission'" : "!= 'exte'",
      "annule" => "= '0'"
    );
      
    $groupSejourNonAffectes["soir"] = loadSejourNonAffectes($where, $order, $praticien_id);
      
    // Admissions ant�rieures
    $twoDaysBefore = mbDate("-2 days", $date);
    $where = array(
      "entree_prevue" => "<= '$twoDaysBefore 23:59:59'",
      "sortie_prevue" => ">= '$date 00:00:00'",
      //"'$twoDaysBefore' BETWEEN entree_prevue AND sortie_prevue",
      "annule" => "= '0'",
      "type" => $type_admission ? " = '$type_admission'" : "!= 'exte'"
    );
      
    $groupSejourNonAffectes["avant"] = loadSejourNonAffectes($where, $order, $praticien_id);
    if($_is_praticien){
      foreach($groupSejourNonAffectes as $sejours_by_moment){
        foreach($sejours_by_moment as $_sejour){
          if($_sejour->praticien_id == $userCourant->user_id){
            $tab_sejour[$_sejour->_id] = $_sejour;
          }
        }
      }
    }
  } else {
    $service->load($service_id);
    loadServiceComplet($service, $date, $mode, $praticien_id, $type_admission);
  }
  
  if($service->_id){
    foreach($service->_ref_chambres as $_chambre){
      foreach($_chambre->_ref_lits as $_lits){
        foreach($_lits->_ref_affectations as $_affectation){
          if($_is_praticien && $_affectation->_ref_sejour->praticien_id == $userCourant->user_id){
            $tab_sejour[$_affectation->_ref_sejour->_id]= $_affectation->_ref_sejour;
          }
          $_affectation->_ref_sejour->loadRefsPrescriptions();
          $_affectation->_ref_sejour->_ref_praticien->loadRefFunction();
          $_affectation->loadRefsAffectations();
          if($_affectation->_ref_sejour->_ref_prescriptions){
            if(array_key_exists('sejour', $_affectation->_ref_sejour->_ref_prescriptions)){
              $prescription_sejour = $_affectation->_ref_sejour->_ref_prescriptions["sejour"];
              $prescription_sejour->countNoValideLines();
            }
          }
        }
      }
    }
  }
  $sejoursParService[$service->_id] = $service;
}

// Chargement des visites pour les s�jours courants
$visites = array(
  "effectuee" => array(), 
  "non_effectuee" => array()
);

if(count($tab_sejour)){
  foreach($tab_sejour as $_sejour){
    if($_sejour->countNotificationVisite($date)){
      $visites["effectuee"][] = $_sejour->_id;
    } else {
      $visites["non_effectuee"][] = $_sejour->_id; 
    }
  }
}

$can_view_dossier_medical = 
  CModule::getCanDo('dPcabinet')->edit ||
  CModule::getCanDo('dPbloc')->edit ||
  CModule::getCanDo('dPplanningOp')->edit || 
  $userCourant->isFromType(array("Infirmi�re"));

if ($type_admission) {
  $sejour->type = $type_admission;
}
// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("_active_tab", $_active_tab);
$smarty->assign("_is_praticien"               , $_is_praticien);
$smarty->assign("anesthesistes"           , $anesthesistes);
$smarty->assign("praticiens"              , $praticiens);
$smarty->assign("praticien_id"            , $praticien_id);
$smarty->assign("object"                  , $sejour);
$smarty->assign("mode"                    , $mode);
$smarty->assign("totalLits"               , $totalLits);
$smarty->assign("date"                    , $date);
$smarty->assign("isPrescriptionInstalled" , CModule::getActive("dPprescription"));
$smarty->assign("isImedsInstalled"        , (CModule::getActive("dPImeds") && CImeds::getTagCIDC(CGroups::loadCurrent())));
$smarty->assign("can_view_dossier_medical", $can_view_dossier_medical);
$smarty->assign("demain"                  , mbDate("+ 1 day", $date));
$smarty->assign("services"                , $services);
$smarty->assign("sejoursParService"       , $sejoursParService);
if(CModule::getActive("dPprescription")){
  $smarty->assign("prescription_sejour"     , $prescription_sejour);
}
$smarty->assign("service_id"              , $service_id);
$smarty->assign("groupSejourNonAffectes"  , $groupSejourNonAffectes);
$smarty->assign("tab_sejour"              , $tab_sejour);
$smarty->assign("visites"                 , $visites);
$smarty->assign("current_date"            , mbDate());
$smarty->display("vw_idx_sejour.tpl");


?>