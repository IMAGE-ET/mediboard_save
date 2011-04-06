<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsEdit();
$ds = CSQLDataSource::get("std");
$date      = CValue::getOrSession("date", mbDate());
$lastmonth = mbDate("-1 month", $date);
$nextmonth = mbDate("+1 month", $date);

// S�lection du praticien
$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);
$listPrat = $mediuser->loadPraticiens(PERM_EDIT);

$selPrat = CValue::getOrSession("selPrat", $mediuser->isPraticien() ? $mediuser->user_id : null);

$selPraticien = new CMediusers();
$selPraticien->load($selPrat);

if($selPraticien->isAnesth()) {
  // Selection des diff�rentes interventions de la journ�e par service
  $service = new CService();
  $services = $service->loadGroupList();
  $interv = new COperation();
  $order = "operations.chir_id, operations.time_operation";
  $ljoin = array(
             "plagesop"    => "plagesop.plageop_id = operations.plageop_id",
             "sejour"      => "sejour.sejour_id = operations.sejour_id",
             "affectation" => "affectation.sejour_id = sejour.sejour_id AND '$date' BETWEEN DATE(affectation.entree) AND DATE(affectation.sortie)",
             "lit"         => "lit.lit_id = affectation.lit_id",
             "chambre"     => "chambre.chambre_id = lit.chambre_id",
             "service"     => "service.service_id = chambre.service_id",
           );
  $whereAmbu = array(
                 "plagesop.date = '$date' OR operations.date = '$date'",
                 "sejour.type"   => "= 'ambu'",
                 "sejour.group_id" => "= '".CGroups::loadCurrent()->_id."'",
               );
  $whereHospi = array(
                 "plagesop.date = '$date' OR operations.date = '$date'",
                 "sejour.type"   => "= 'comp'",
                 "sejour.group_id" => "= '".CGroups::loadCurrent()->_id."'",
               );
  $whereUrg   = array(
                 "plagesop.plageop_id" => "IS NULL",
                 "operations.date"     => "= '$date'",
                 "sejour.group_id" => "= '".CGroups::loadCurrent()->_id."'",
               );
  foreach($services as $_service) {
    $whereAmbu["service.service_id"]          = "= '$_service->_id'";
    $whereHospi["service.service_id"]         = "= '$_service->_id'";
    $whereUrg["service.service_id"]           = "= '$_service->_id'";
    $listInterv["ambu"][$_service->_id]       = $interv->loadList($whereAmbu , $order, null, null, $ljoin);
    foreach($listInterv["ambu"][$_service->_id] as &$_interv) {
      $_interv->loadRefAffectation();
      $_interv->loadRefsFwd(1);
      $_interv->loadRefsConsultAnesth();
      $_interv->_ref_chir->loadRefFunction();
    }
    $listInterv["hospi"][$_service->_id]      = $interv->loadList($whereHospi, $order, null, null, $ljoin);
    foreach($listInterv["hospi"][$_service->_id] as &$_interv) {
      $_interv->loadRefAffectation();
      $_interv->loadRefsFwd(1);
      $_interv->loadRefsConsultAnesth();
      $_interv->_ref_chir->loadRefFunction();
    }
    $listInterv["hors plage"][$_service->_id] = $interv->loadList($whereUrg  , $order, null, null, $ljoin);
    foreach($listInterv["hors plage"][$_service->_id] as &$_interv) {
      $_interv->loadRefAffectation();
      $_interv->loadRefsFwd(1);
      $_interv->loadRefsConsultAnesth();
      $_interv->_ref_chir->loadRefFunction();
    }
  }
  $whereAmbu["service.service_id"]       = "IS NULL";
  $whereHospi["service.service_id"]      = "IS NULL";
  $whereUrg["service.service_id"]        = "IS NULL";
  $listInterv["ambu"]["non_place"]       = $interv->loadList($whereAmbu , $order, null, null, $ljoin);
  foreach($listInterv["ambu"]["non_place"] as &$_interv) {
    $_interv->loadRefAffectation();
    $_interv->loadRefsFwd(1);
    $_interv->loadRefsConsultAnesth();
      $_interv->_ref_chir->loadRefFunction();
  }
  $listInterv["hospi"]["non_place"]      = $interv->loadList($whereHospi, $order, null, null, $ljoin);
  foreach($listInterv["hospi"]["non_place"] as &$_interv) {
    $_interv->loadRefAffectation();
    $_interv->loadRefsFwd(1);
    $_interv->loadRefsConsultAnesth();
      $_interv->_ref_chir->loadRefFunction();
  }
  $listInterv["hors plage"]["non_place"] = $interv->loadList($whereUrg  , $order, null, null, $ljoin);
  foreach($listInterv["hors plage"]["non_place"] as &$_interv) {
    $_interv->loadRefAffectation();
    $_interv->loadRefsFwd(1);
    $_interv->loadRefsConsultAnesth();
      $_interv->_ref_chir->loadRefFunction();
  }

  // Cr�ation du template
  $smarty = new CSmartyDP();
  
  $smarty->assign("date"        , $date        );
  $smarty->assign("listPrat"    , $listPrat    );
  $smarty->assign("listInterv"  , $listInterv  );
  $smarty->assign("services"    , $services    );
  $smarty->assign("selPrat"     , $selPrat     );
  
  $smarty->display("vw_idx_visite_anesth.tpl");
  
} else {
  // Selection des plages du praticien et de celles de sa sp�cialit�
  $selPratLogin   = null;
  $specialite     = null;
  $secondary_specs = array();
  if ($selPraticien->isPraticien()) {
    $selPratLogin = $selPraticien->user_id;
    $specialite = $selPraticien->function_id;
    $selPraticien->loadBackRefs("secondary_functions");
    foreach($selPraticien->_back["secondary_functions"] as  $curr_sec_spec) {
      $curr_sec_spec->loadRefsFwd();
      $curr_function = $curr_sec_spec->_ref_function;
      $secondary_specs[$curr_function->_id] = $curr_function;
    }
  }
  
  // Planning du mois
  $month_min = mbTransformTime("+ 0 month", $date, "%Y-%m-00");
  $month_max = mbTransformTime("+ 1 month", $date, "%Y-%m-00");
  
  $sql = "SELECT plagesop.*, plagesop.date AS opdate,
        SEC_TO_TIME(SUM(TIME_TO_SEC(operations.temp_operation))) AS duree,
        COUNT(operations.operation_id) AS total,
        functions_mediboard.text AS nom_function, functions_mediboard.color as color_function
      FROM plagesop
      LEFT JOIN operations
        ON plagesop.plageop_id = operations.plageop_id
          AND operations.annulee = '0'
          AND operations.chir_id = '$selPratLogin'
      LEFT JOIN functions_mediboard
        ON functions_mediboard.function_id = plagesop.spec_id
      WHERE (plagesop.chir_id = '$selPratLogin' OR plagesop.spec_id = '$specialite' OR plagesop.spec_id ".CSQLDataSource::prepareIn(array_keys($secondary_specs)).")
        AND plagesop.date BETWEEN '$month_min' AND '$month_max'
      GROUP BY plagesop.plageop_id
      ORDER BY plagesop.date, plagesop.debut, plagesop.plageop_id";
  if($selPratLogin) {
    $listPlages = $ds->loadList($sql);
  } else {
    $listPlages = array();
  }
  
  // Urgences du mois
  $sql = "SELECT operations.*, operations.date AS opdate,
        SEC_TO_TIME(SUM(TIME_TO_SEC(operations.temp_operation))) AS duree,
        COUNT(operations.operation_id) AS total
      FROM operations
      WHERE operations.annulee = '0'
        AND operations.chir_id = '$selPratLogin'
        AND operations.date BETWEEN '$month_min' AND '$month_max'
      GROUP BY operations.date
      ORDER BY operations.date";
  if($selPratLogin) {
    $listUrgences = $ds->loadList($sql);
  } else {
    $listUrgences = array();
  }
  
  $listDays = array();
  foreach($listPlages as $curr_ops) {
    $listDays[$curr_ops["opdate"]][$curr_ops["plageop_id"]] = $curr_ops;
  }
  foreach($listUrgences as $curr_ops) {
    $listDays[$curr_ops["opdate"]]["hors_plage"] = $curr_ops;
  }
  
  ksort($listDays);
  
  // Cr�ation du template
  $smarty = new CSmartyDP();
  
  $smarty->assign("date"        , $date        );
  $smarty->assign("lastmonth"   , $lastmonth   );
  $smarty->assign("nextmonth"   , $nextmonth   );
  $smarty->assign("listPrat"    , $listPrat    );
  $smarty->assign("selPrat"     , $selPrat     );
  $smarty->assign("listDays"    , $listDays    );
  
  $smarty->display("vw_idx_planning.tpl");
}

?>