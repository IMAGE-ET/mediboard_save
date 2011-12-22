<?php /* $Id: ajax_list_sorties.php $ */

/**
 * @package Mediboard
 * @subpackage dPpersonnel
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */


$type           = CValue::get("type");
$type_mouvement = CValue::get("type_mouvement");
$vue            = CValue::getOrSession("vue"         , 0);
$praticien_id   = CValue::getOrSession("praticien_id", null);
$service_id     = CValue::getOrSession("service_id"  , null);
$order_way      = CValue::getOrSession("order_way"   , "ASC");
$order_col      = CValue::getOrSession("order_col"   , "_patient");

$group = CGroups::loadCurrent();

$types_hospi = array("comp","ambu","urg","ssr","psy");
$type_hospi  = CValue::getOrSession("type_hospi", null);

$entrees = array();
$sorties = array();

// R�cup�ration de la liste des services
$where = array();
$where["externe"] = "= '0'";
$where["group_id"] = "= '$group->_id'";

$service  = new CService();
$services = $service->loadListWithPerms(PERM_READ, $where);

// R�cup�ration de la journ�e � afficher
$date  = CValue::getOrSession("date" , mbDate());
$limit1 = $date." 00:00:00";
$limit2 = $date." 23:59:59";

// Patients plac�s
$affectation                 = new CAffectation();
$ljoin                       = array();
$ljoin["sejour"]             = "sejour.sejour_id = affectation.sejour_id";
$ljoin["patients"]           = "sejour.patient_id = patients.patient_id";
$ljoin["users"]              = "sejour.praticien_id = users.user_id";
$ljoin["lit"]                = "lit.lit_id = affectation.lit_id";
$ljoin["chambre"]            = "chambre.chambre_id = lit.chambre_id";
$ljoin["service"]            = "service.service_id = chambre.service_id";
$where                       = array();
$where["service.group_id"]   = "= '$group->_id'";
$where["service.service_id"] = CSQLDataSource::prepareIn(array_keys($services)   , $service_id);
$where["sejour.type"]        = CSQLDataSource::prepareIn($types_hospi, $type_hospi);
if($praticien_id) {
  $where["sejour.praticien_id"] = "= '$praticien_id'";
}

// Patients non plac�s
$sejour                                = new CSejour();
$ljoinNP                               = array();
$ljoinNP["affectation"]                = "sejour.sejour_id    = affectation.sejour_id";
$ljoinNP["patients"]                   = "sejour.patient_id   = patients.patient_id";
$ljoinNP["users"]                      = "sejour.praticien_id = users.user_id";
$whereNP                               = array();
$whereNP["sejour.group_id"]            = "= '$group->_id'";
$whereNP["sejour.type"]                = CSQLDataSource::prepareIn($types_hospi, $type_hospi);
$whereNP["affectation.affectation_id"] = "IS NULL";
if($service_id) {
  $whereNP["sejour.service_id"] = "= '$service_id'";
}
if($praticien_id) {
  $whereNP["sejour.praticien_id"] = "= '$praticien_id'";
}

$order = $orderNP = null;
if($order_col == "_patient"){
  $order = $orderNP = "patients.nom $order_way, patients.prenom, sejour.entree";
}
if($order_col == "_praticien"){
  $order = $orderNP = "users.user_last_name $order_way, users.user_first_name";
}
if($order_col == "_chambre"){
  $order = "chambre.nom $order_way, patients.nom, patients.prenom";
  $orderNP = "patients.nom ASC, patients.prenom, sejour.entree";
}
if($order_col == "sortie"){
  $order   = "affectation.sortie $order_way, patients.nom, patients.prenom";
  $orderNP = "sejour.sortie $order_way, patients.nom, patients.prenom";
}

// R�cup�ration des pr�sents du jour
if($type == 'presents') {
  // Patients plac�s
  $where[] = "'$date' BETWEEN DATE(affectation.entree) AND DATE(affectation.sortie)";
  if ($vue) {
    $where["confirme"] = " = '0'";
  }
  $presents = $affectation->loadList($where, $order, null, null, $ljoin);
  
  // Patients non plac�s
  $whereNP[]  = "'$date' BETWEEN DATE(sejour.entree) AND DATE(sejour.sortie)";
  $presentsNP = $sejour->loadList($whereNP, $orderNP, null, null, $ljoinNP);
  
  // Chargements des d�tails des s�jours
  foreach($presents as $_present) {
    $_present->loadRefsFwd();
    $sejour = $_present->_ref_sejour;
    $sejour->loadRefPatient(1);
    $sejour->loadRefPraticien(1);
    $sejour->checkDaysRelative($date);
    $sejour->loadRefsOperations();
    $_present->_ref_next->loadRefLit(1)->loadCompleteView();
  }
  foreach($presentsNP as $sejour) {
    $sejour->loadRefPatient(1);
    $sejour->loadRefPraticien(1);
    $sejour->checkDaysRelative($date);
    $sejour->loadRefsOperations();
  }
  
// R�cup�ration des d�placements du jour
} elseif ($type == "deplacements") {
  $where["affectation.sortie"] = "BETWEEN '$limit1' AND '$limit2'";
  $where["service.externe"]    = "= '0'";
  $where["sejour.sortie"]      = "!= affectation.sortie";
  if ($vue) {
    $where["effectue"] = "= '0'";
  }
  $deplacements = $affectation->loadList($where, $order, null, null, $ljoin);
  $sejours      = CMbObject::massLoadFwdRef($deplacements, "sejour_id");
  $patients     = CMbObject::massLoadFwdRef($sejours, "patient_id");
  $praticiens   = CMbObject::massLoadFwdRef($sejours, "praticien_id");
  CMbObject::massLoadFwdRef($praticiens, "function_id");
  CMbObject::massLoadFwdRef($deplacements, "lit_id");
  
  foreach($deplacements as $_deplacement) {
    $_deplacement->loadRefsFwd();
    $sejour = $_deplacement->_ref_sejour; 
    $sejour->loadRefPatient(1);
    $sejour->loadRefPraticien(1);
    $_deplacement->_ref_next->loadRefLit()->loadCompleteView();
  }

// R�cup�ration des entr�es du jour
} elseif($type_mouvement == "entrees") {
  // Patients plac�s
  $where["affectation.entree"] = "BETWEEN '$limit1' AND '$limit2'";
  $where["sejour.entree"] = "= affectation.entree";
  $where["sejour.type"] = " = '$type'";
  $mouvements = $affectation->loadList($where, $order, null, null, $ljoin);
  
  // Chargements des d�tails des s�jours
  foreach($mouvements as $_mouvement) {
    $_mouvement->loadRefsFwd();
    $sejour = $_mouvement->_ref_sejour;
    $sejour->loadRefPatient(1);
    $sejour->loadRefPraticien(1);
    $sejour->checkDaysRelative($date);
    $sejour->loadRefsOperations();
    $_mouvement->_ref_next->loadRefLit(1)->loadCompleteView();
  }
  
  // Patients non plac�s
  $whereNP["sejour.entree"] = "BETWEEN '$limit1' AND '$limit2'";
  $whereNP["sejour.type"]   = " = '$type'";
  $mouvementsNP = $sejour->loadList($whereNP, $orderNP, null, null, $ljoinNP);
  
  // Chargements des d�tails des s�jours
  foreach($mouvementsNP as $sejour) {
    $sejour->loadRefPatient(1);
    $sejour->loadRefPraticien(1);
    $sejour->checkDaysRelative($date);
    $sejour->loadRefsOperations();
  }
// R�cup�ration des sorties du jour
} else {
  // Patients plac�s
  $where["affectation.sortie"] = "BETWEEN '$limit1' AND '$limit2'";
  $where["sejour.sortie"] = "= affectation.sortie";
  $where["sejour.type"] = " = '$type'";
  if ($vue) {
    $where["confirme"] = " = '0'";
  }
  $mouvements = $affectation->loadList($where, $order, null, null, $ljoin);
  
  // Chargements des d�tails des s�jours
  foreach($mouvements as $_mouvement) {
    $_mouvement->loadRefsFwd();
    $sejour = $_sortie->_ref_sejour;
    $sejour->loadRefPatient(1);
    $sejour->loadRefPraticien(1);
    $sejour->checkDaysRelative($date);
    $sejour->loadRefsOperations();
    $_mouvement->_ref_next->loadRefLit(1)->loadCompleteView();
  }
  
  // Patients non plac�s
  $whereNP["sejour.sortie"] = "BETWEEN '$limit1' AND '$limit2'";
  $whereNP["sejour.type"]   = " = '$type'";
  $mouvementsNP = $sejour->loadList($whereNP, $orderNP, null, null, $ljoinNP);
  
  // Chargements des d�tails des s�jours
  foreach($mouvementsNP as $sejour) {
    $sejour->loadRefPatient(1);
    $sejour->loadRefPraticien(1);
    $sejour->checkDaysRelative($date);
    $sejour->loadRefsOperations();
  }
}


// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("type"          , $type);
$smarty->assign("type_mouvement", $type_mouvement);
$smarty->assign("type_hospi"    , $type_hospi);
$smarty->assign("order_way"     , $order_way);
$smarty->assign("order_col"     , $order_col);
$smarty->assign("date"          , $date);
if ($type == "deplacements") {
  $smarty->assign("deplacements", $deplacements);
  $smarty->assign("update_count", count($deplacements));
}
elseif($type == "presents") {
  $smarty->assign("mouvements"  , $presents);
  $smarty->assign("mouvementsNP", $presentsNP);
  $smarty->assign("update_count", count($presents)."/".count($presentsNP));
} else {
  $smarty->assign("mouvements"  , $mouvements);
  $smarty->assign("mouvementsNP", $mouvementsNP);
  $smarty->assign("update_count", count($mouvements)."/".count($mouvementsNP));
}
$smarty->assign("vue"          , $vue);
$smarty->assign("canPlanningOp", CModule::getCanDo("dPplanningOp"));

$smarty->display("inc_list_sorties.tpl");

?>