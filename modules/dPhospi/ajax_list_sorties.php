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
$services_ids   = CValue::getOrSession("services_ids"  , null);
$order_way      = CValue::getOrSession("order_way"   , "ASC");
$order_col      = CValue::getOrSession("order_col"   , "_patient");
$show_duree_preop = CAppUI::conf("dPplanningOp COperation show_duree_preop");
$show_hour_anesth = CAppUI::conf("dPhospi show_hour_anesth_mvt");

if (is_array($services_ids)) {
  CMbArray::removeValue("", $services_ids);
}

$services = new CService();
$where = array();
$where["service_id"] = CSQLDataSource::prepareIn($services_ids);
$services = $services->loadList($where);

$update_count = "";

$praticien = new CMediusers();
$praticien->load($praticien_id);

$dmi_active = CModule::getActive("dmi");

$group = CGroups::loadCurrent();

$types_hospi = array("comp","ambu","urg","ssr","psy");
$type_hospi  = CValue::getOrSession("type_hospi", null);

$entrees = array();
$sorties = array();

// R�cup�ration de la liste des services
$where = array();
$where["externe"] = "= '0'";
$where["group_id"] = "= '$group->_id'";

// R�cup�ration de la journ�e � afficher
$date  = CValue::getOrSession("date" , CMbDT::date());
$limit1 = $date." 00:00:00";
$limit2 = $date." 23:59:59";

// Patients plac�s
$affectation                     = new CAffectation();
$ljoin                           = array();
$ljoin["sejour"]                 = "sejour.sejour_id = affectation.sejour_id";
$ljoin["patients"]               = "sejour.patient_id = patients.patient_id";
$ljoin["users"]                  = "sejour.praticien_id = users.user_id";
$ljoin["lit"]                    = "lit.lit_id = affectation.lit_id";
$ljoin["chambre"]                = "chambre.chambre_id = lit.chambre_id";
$ljoin["service"]                = "service.service_id = chambre.service_id";
$where                           = array();
$where["service.group_id"]       = "= '$group->_id'";
$where["affectation.service_id"] = CSQLDataSource::prepareIn($services_ids);
$where["sejour.type"]            = CSQLDataSource::prepareIn($types_hospi, $type_hospi);
if ($praticien_id) {
  $where["sejour.praticien_id"] = "= '$praticien->_id'";
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
$whereNP["sejour.annule"]              = "= '0'";

if (count($services_ids)) {
  // Tenir compte des affectations sans lit_id (dans le couloir du service)
  unset($whereNP["affectation.affectation_id"]);
  $whereNP[] = "((sejour.service_id " . CSQLDataSource::prepareIn($services_ids) . " OR sejour.service_id IS NULL) AND affectation.affectation_id IS NULL) OR "
              ."(affectation.lit_id IS NULL AND affectation.service_id " . CSQLDataSource::prepareIn($services_ids) . ")";
}
if ($praticien->_id) {
  $whereNP["sejour.praticien_id"] = "= '$praticien->_id'";
}

$order = $orderNP = null;
if ($order_col == "_patient"){
  $order = $orderNP = "patients.nom $order_way, patients.prenom, sejour.entree";
}
if ($order_col == "_praticien"){
  $order = $orderNP = "users.user_last_name $order_way, users.user_first_name";
}
if ($order_col == "_chambre"){
  $order = "chambre.nom $order_way, patients.nom, patients.prenom";
  $orderNP = "patients.nom ASC, patients.prenom, sejour.entree";
}
if ($order_col == "sortie"){
  $order   = "affectation.sortie $order_way, patients.nom, patients.prenom";
  $orderNP = "sejour.sortie $order_way, patients.nom, patients.prenom";
}
if ($order_col == "entree"){
  $order   = "affectation.entree $order_way, patients.nom, patients.prenom";
  $orderNP = "sejour.entree $order_way, patients.nom, patients.prenom";
}


// R�cup�ration des pr�sents du jour
if ($type == "presents") {
  // Patients plac�s
  $where[] = "'$date' BETWEEN DATE(affectation.entree) AND DATE(affectation.sortie)";
  if ($vue) {
    $where["sejour.confirme"] = " = '0'";
  }
  $presents = $affectation->loadList($where, $order, null, null, $ljoin);
  
  // Patients non plac�s
  $whereNP[]  = "'$date' BETWEEN DATE(sejour.entree) AND DATE(sejour.sortie)";
  $presentsNP = $sejour->loadList($whereNP, $orderNP, null, null, $ljoinNP);

  $update_count = count($presents)."/".count($presentsNP);

  // Chargements des d�tails des s�jours
  foreach ($presents as $_present) {
    $_present->loadRefsFwd();
    $sejour = $_present->_ref_sejour;
    $sejour->loadRefPatient(1);
    $sejour->loadRefPraticien(1);
    $sejour->checkDaysRelative($date);
    $sejour->loadRefsOperations();
    $sejour->loadNDA();
    
    if ($show_duree_preop || $show_hour_anesth) {
      $op = $sejour->loadRefCurrOperation($date);
      if ($show_duree_preop) {
        $op->updateHeureUS();
      }
      if ($show_hour_anesth) {
        $op->loadRefPlageOp();
        $op->_ref_anesth->loadRefFunction();
      }
    }
    
    $_present->_ref_next->loadRefLit(1)->loadCompleteView();
  }
  foreach ($presentsNP as $sejour) {
    $sejour->loadRefPatient(1);
    $sejour->loadRefPraticien(1);
    $sejour->checkDaysRelative($date);
    $sejour->loadRefsOperations();
    
    if ($show_duree_preop || $show_hour_anesth) {
      $op = $sejour->loadRefCurrOperation($date);
      if ($show_duree_preop) {
        $op->updateHeureUS();
      }
      if ($show_hour_anesth) {
        $op->loadRefPlageOp();
        $op->_ref_anesth->loadRefFunction();
      }
    }
  }
  
  $presents_by_service = array();
  $presentsNP_by_service = array();
  
  foreach ($presents as $_present) {
    if (!isset($presents_by_service[$_present->service_id])) {
      $presents_by_service[$_present->service_id] = array();
    }
    $presents_by_service[$_present->service_id][] = $_present;
  }
  
  $presents = $presents_by_service;
  
  foreach ($presentsNP as $_present) {
    if (!isset($presentsNP_by_service[$_present->service_id])) {
      $presentsNP_by_service[$_present->service_id] = array();
    }
    $presentsNP_by_service[$_present->service_id][] = $_present;
  }
  
  $presentsNP = $presentsNP_by_service;
  
// R�cup�ration des d�placements du jour
} elseif ($type == "deplacements") {
  if ($vue) {
    $where["effectue"] = "= '0'";
  }

  $whereEntrants = $whereSortants = $where;
  $whereSortants["affectation.sortie"] = "BETWEEN '$limit1' AND '$limit2'";
  $whereEntrants["affectation.entree"] = "BETWEEN '$limit1' AND '$limit2'";
  $whereEntrants["sejour.entree"] = "!= affectation.entree";
  $whereSortants["sejour.sortie"] = "!= affectation.sortie";

  // Tenir compte des affectations "dans les couloirs"
  // (avec service_id mais pas de lit_id)
  unset($whereEntrants["service.group_id"]);
  unset($whereSortants["service.group_id"]);
  unset($ljoin["lit"]);
  unset($ljoin["chambre"]);
  unset($ljoin["service"]);

  $whereSortants["sejour.group_id"]       = "= '$group->_id'";
  $whereSortants["sejour.group_id"]       = "= '$group->_id'";

  $dep_entrants = $affectation->loadList($whereEntrants, $order, null, null, $ljoin);
  $dep_sortants = $affectation->loadList($whereSortants, $order, null, null, $ljoin);
  $deplacements = array_merge($dep_entrants, $dep_sortants);
  $sejours      = CMbObject::massLoadFwdRef($deplacements, "sejour_id");
  $patients     = CMbObject::massLoadFwdRef($sejours, "patient_id");
  $praticiens   = CMbObject::massLoadFwdRef($sejours, "praticien_id");
  CMbObject::massLoadFwdRef($praticiens, "function_id");
  CMbObject::massLoadFwdRef($deplacements, "lit_id");

  $update_count = count($dep_entrants)."/".count($dep_sortants);

  foreach ($deplacements as $_deplacement) {
    $_deplacement->loadRefsFwd();
    $sejour = $_deplacement->_ref_sejour; 
    $sejour->loadRefPatient(1);
    $sejour->loadRefPraticien(1);
    $sejour->loadNDA();
    $_deplacement->_ref_next->loadRefLit()->loadCompleteView();
    $_deplacement->_ref_prev->loadRefLit()->loadCompleteView();

    if ($show_hour_anesth) {
      $op = $sejour->loadRefCurrOperation($date);
      $op->loadRefPlageOp();
      $op->_ref_anesth->loadRefFunction();
    }
  }
  
  $dep_entrants_by_service = array();
  $dep_sortants_by_service = array();
  
  foreach ($dep_entrants as $_dep_entrant) {
    if (!isset($dep_entrants_by_service[$_dep_entrant->service_id])) {
      $dep_entrants_by_service[$_dep_entrant->service_id] = array();
    }
    $dep_entrants_by_service[$_dep_entrant->service_id][] = $_dep_entrant;
  }
  
  $dep_entrants = $dep_entrants_by_service;
  
  foreach ($dep_sortants as $_dep_sortant) {
    if (!isset($dep_sortants_by_service[$_dep_sortant->service_id])) {
      $dep_sortants_by_service[$_dep_sortant->service_id] = array();
    }
    $dep_sortants_by_service[$_dep_sortant->service_id][] = $_dep_sortant;
  }
  
  $dep_sortants = $dep_sortants_by_service;
  
// R�cup�ration des entr�es du jour
} elseif ($type_mouvement == "entrees") {
  // Patients plac�s
  $where["affectation.entree"] = "BETWEEN '$limit1' AND '$limit2'";
  $where["sejour.entree"]      = "= affectation.entree";
  $where["sejour.type"]        = " = '$type'";
  $mouvements = $affectation->loadList($where, $order, null, null, $ljoin);
  
  // Chargements des d�tails des s�jours
  foreach ($mouvements as $_mouvement) {
    $_mouvement->loadRefsFwd();
    $sejour = $_mouvement->_ref_sejour;
    $sejour->loadRefPatient(1);
    $sejour->loadRefPraticien(1);
    $sejour->checkDaysRelative($date);
    $sejour->loadRefsOperations();
    $sejour->loadNDA();

    if ($show_duree_preop || $show_hour_anesth) {
      $op = $sejour->loadRefCurrOperation($date);
      if ($show_duree_preop) {
        $op->updateHeureUS();
      }
      if ($show_hour_anesth) {
        $op->loadRefPlageOp();
        $op->_ref_anesth->loadRefFunction();
      }
    }
    
    if ($dmi_active) {
      foreach($sejour->_ref_operations as $_interv) {
        $_interv->getDMIAlert();
      }
    }
    
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
    $sejour->loadNDA();
    
    if ($show_duree_preop || $show_hour_anesth) {
      $op = $sejour->loadRefCurrOperation($date);
      if ($show_duree_preop) {
        $op->updateHeureUS();
      }
      if ($show_hour_anesth) {
        $op->loadRefPlageOp();
        $op->_ref_anesth->loadRefFunction();
      }
    }
    if ($dmi_active) {
      foreach($sejour->_ref_operations as $_interv) {
        $_interv->getDMIAlert();
      }
    }
  }

  $update_count = count($mouvements)."/".count($mouvementsNP);

  $mouvements_by_service = array();
  $mouvementsNP_by_service = array();
  
  foreach ($mouvements as $_mouvement) {
    if (!isset($mouvements_by_service[$_mouvement->service_id])) {
      $mouvements_by_service[$_mouvement->service_id] = array();
    }
    $mouvements_by_service[$_mouvement->service_id][] = $_mouvement;
  }
  
  $mouvements = $mouvements_by_service;
  
  foreach ($mouvementsNP as $_mouvement) {
    if (!isset($mouvementsNP_by_service[$_mouvement->service_id])) {
      $mouvementsNP_by_service[$_mouvement->service_id] = array();
    }
    $mouvementsNP_by_service[$_mouvement->service_id][] = $_mouvement;
  }
  
  $mouvementsNP = $mouvementsNP_by_service;
  
// R�cup�ration des sorties du jour
} else {
  // Patients plac�s
  $where["affectation.sortie"] = "BETWEEN '$limit1' AND '$limit2'";
  $where["sejour.sortie"]      = "= affectation.sortie";
  $where["sejour.type"]        = " = '$type'";
  if ($vue) {
    $where["sejour.confirme"] = " = '0'";
  }
  $mouvements = $affectation->loadList($where, $order, null, null, $ljoin);

  // Chargements des d�tails des s�jours
  foreach ($mouvements as $_mouvement) {
    $_mouvement->loadRefsFwd();
    $sejour = $_mouvement->_ref_sejour;
    $sejour->loadRefPatient(1);
    $sejour->loadRefPraticien(1);
    $sejour->checkDaysRelative($date);
    $sejour->loadRefsOperations();
    $sejour->loadNDA();
    
    if ($show_duree_preop ||  $show_hour_anesth) {
      $op = $sejour->loadRefCurrOperation($date);
      if ($show_duree_preop) {
        $op->updateHeureUS();
      }
      if ($show_hour_anesth) {
        $op->loadRefPlageOp();
        $op->_ref_anesth->loadRefFunction();
      }
    }

    if ($dmi_active) {
      foreach($sejour->_ref_operations as $_interv) {
        $_interv->getDMIAlert();
      }
    }
    
    $_mouvement->_ref_next->loadRefLit(1)->loadCompleteView();
  }
  
  // Patients non plac�s
  $whereNP["sejour.sortie"] = "BETWEEN '$limit1' AND '$limit2'";
  $whereNP["sejour.type"]   = " = '$type'";
  $mouvementsNP = $sejour->loadList($whereNP, $orderNP, null, null, $ljoinNP);
  
  // Chargements des d�tails des s�jours
  foreach ($mouvementsNP as $sejour) {
    $sejour->loadRefPatient(1);
    $sejour->loadRefPraticien(1);
    $sejour->checkDaysRelative($date);
    $sejour->loadRefsOperations();
    $sejour->loadNDA();
    
    if ($show_duree_preop || $show_hour_anesth) {
      $op = $sejour->loadRefCurrOperation($date);
      if ($show_duree_preop) {
        $op->updateHeureUS();
      }
      if ($show_hour_anesth) {
        $op->loadRefPlageOp();
        $op->_ref_anesth->loadRefFunction();
      }
    }

    if ($dmi_active) {
      foreach($sejour->_ref_operations as $_interv) {
        $_interv->getDMIAlert();
      }
    }
  }

  $update_count = count($mouvements)."/".count($mouvementsNP);

  $mouvements_by_service = array();
  $mouvementsNP_by_service = array();

  $update_count = count($mouvements)."/".count($mouvementsNP);

  foreach ($mouvements as $_mouvement) {
    if (!isset($mouvements_by_service[$_mouvement->service_id])) {
      $mouvements_by_service[$_mouvement->service_id] = array();
    }
    $mouvements_by_service[$_mouvement->service_id][] = $_mouvement;
  }
  
  $mouvements = $mouvements_by_service;
  
  foreach ($mouvementsNP as $_mouvement) {
    if (!isset($mouvementsNP_by_service[$_mouvement->service_id])) {
      $mouvementsNP_by_service[$_mouvement->service_id] = array();
    }
    $mouvementsNP_by_service[$_mouvement->service_id][] = $_mouvement;
  }
  
  $mouvementsNP = $mouvementsNP_by_service;
}

$list_mode_sortie = array();
if (CAppUI::conf("dPplanningOp CSejour use_custom_mode_sortie")) {
  $mode_sortie = new CModeSortieSejour();
  $where = array(
    "actif" => "= '1'",
  );
  $list_mode_sortie = $mode_sortie->loadGroupList($where);
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("praticien"     , $praticien);
$smarty->assign("type"          , $type);
$smarty->assign("type_mouvement", $type_mouvement);
$smarty->assign("type_hospi"    , $type_hospi);
$smarty->assign("order_way"     , $order_way);
$smarty->assign("order_col"     , $order_col);
$smarty->assign("date"          , $date);
$smarty->assign("services"      , $services);
$smarty->assign("list_mode_sortie", $list_mode_sortie);

if ($type == "deplacements") {
  $smarty->assign("dep_entrants", $dep_entrants);
  $smarty->assign("dep_sortants", $dep_sortants);
  $smarty->assign("update_count", $update_count);
}
elseif($type == "presents") {
  $smarty->assign("mouvements"  , $presents);
  $smarty->assign("mouvementsNP", $presentsNP);
  $smarty->assign("update_count", $update_count);
}
else {
  $smarty->assign("mouvements"  , $mouvements);
  $smarty->assign("mouvementsNP", $mouvementsNP);
  $smarty->assign("update_count", $update_count);
}
$smarty->assign("vue"          , $vue);
$smarty->assign("canPlanningOp", CModule::getCanDo("dPplanningOp"));
$smarty->assign("isImedsInstalled", (CModule::getActive("dPImeds") && CImeds::getTagCIDC(CGroups::loadCurrent())));

$smarty->display("inc_list_sorties.tpl");
