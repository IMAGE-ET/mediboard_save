<?php /* $Id: $*/

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: $
* @author Romain OLLIVIER
*/

global $AppUI, $can, $m, $g, $dPconfig;

$can->needsRead();

$date = mbGetValueFromGetOrSession("date", mbDate());
$chirSel = mbGetValueFromGetOrSession("chirSel", $AppUI->user_id);
$userSel = new CMediusers;
$userSel->load($chirSel);
$urgences = mbGetValueFromGetOrSession("urgences", 0);
$board = mbGetValueFromGet("board", 0);
$boardItem = mbGetValueFromGet("boardItem", 0);

if($urgences) {
  $listDay = array();
  // Urgences du mois
  $listUrgences = new COperation;
  $where = array();
  $where["date"] = "LIKE '".mbTranformTime("+ 0 day", $date, "%Y-%m")."-__'";
  $where["chir_id"] = "= '$userSel->user_id'";
  $order = "date";
  $listUrgences = $listUrgences->loadList($where, $order);
  foreach($listUrgences as $keyUrg => $curr_urg) {
    $listUrgences[$keyUrg]->loadRefs();
    $listUrgences[$keyUrg]->_ref_sejour->loadRefsFwd();
  }
} else {
  $listUrgences = array();
  // Urgences du jour
  $listUrgences = new COperation;
  $where = array();
  $where["date"] = "= '$date'";
  $where["chir_id"] = "= '$userSel->user_id'";
  $order = "date";
  $listUrgences = $listUrgences->loadList($where, $order);
  foreach($listUrgences as $keyUrg => $curr_urg) {
    $listUrgences[$keyUrg]->loadRefs();
    $listUrgences[$keyUrg]->_ref_sejour->loadRefsFwd();
  }
  // Liste des op�rations du jour s�lectionn�
  $listDay = new CPlageOp;
  $where = array();
  $where["date"] = "= '$date'";
  $where["chir_id"] = "= '$userSel->user_id'";
  $order = "debut";
  $listDay = $listDay->loadList($where, $order);
  foreach($listDay as $key => $value) {
    $listDay[$key]->loadRefs();
    foreach($listDay[$key]->_ref_operations as $key2 => $value2) {
      $listDay[$key]->_ref_operations[$key2]->loadRefs();
      $listDay[$key]->_ref_operations[$key2]->_ref_sejour->loadRefsFwd();
    }
  }
}

// r�cup�ration des mod�les de compte-rendu disponibles
$where                 = array();
$order                 = "nom";
$where["object_class"] = "= 'COperation'";
$where["chir_id"]      = "= '$userSel->user_id'";
$crList                = CCompteRendu::loadModeleByCat("Op�ration", $where, $order, true);
$hospiList             = CCompteRendu::loadModeleByCat("Hospitalisation", $where, $order, true);

// Packs d'hospitalisation
$packList         = array();
$where            = array();
$where["object_class"] = " = 'COperation'";
$where["chir_id"] = "= '$userSel->user_id'";
$pack             = new CPack;
$packList         = $pack->loadlist($where, $order);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("boardItem"   , $boardItem);
$smarty->assign("date"        , $date);
$smarty->assign("crList"      , $crList);
$smarty->assign("hospiList"   , $hospiList);
$smarty->assign("packList"    , $packList);
$smarty->assign("listUrgences", $listUrgences);
$smarty->assign("listDay"     , $listDay);
$smarty->assign("urgences"    , $urgences);
$smarty->assign("board"       , $board);

$smarty->display("inc_list_operations.tpl");

?>