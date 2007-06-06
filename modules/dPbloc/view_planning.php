<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

$now       = mbDate();

$filter = new COperation;
$filter->_date_min = mbGetValueFromGet("_date_min"    , "$now");
$filter->_date_max = mbGetValueFromGet("_date_max"    , "$now");
$filter->_prat_id = mbGetValueFromGetOrSession("chir", null);
$filter->salle_id = mbGetValueFromGetOrSession("salle", 0);
$filter->_plage = mbGetValueFromGetOrSession("vide", 0);
$filter->_intervention = mbGetValueFromGetOrSession("type", 0);
$filter->_specialite = mbGetValueFromGetOrSession("spe", null);
$filter->_codes_ccam = mbGetValueFromGetOrSession("code_ccam", "");

//On sort les plages opératoires
//  Chir - Salle - Horaires

$plagesop = new CPlageOp;

$where = array();
$where["date"] =  db_prepare("BETWEEN %1 AND %2", $filter->_date_min, $filter->_date_max);

$order = array();
$order[] = "date";
$order[] = "salle_id";
$order[] = "debut";

$chir_id = mbGetValueFromGet("chir");
$user = new CMediusers();
$user->load($AppUI->user_id);

// On ne teste pas les droits pour l'instant...

//if($user->isFromType(array("Anesthésiste"))) {
  if($chir_id) {
    $where["chir_id"] = db_prepare("= %", $filter->_prat_id);
  }
//} else {
//  $listPrat = new CMediusers;
//  $listPrat = $listPrat->loadPraticiens(PERM_READ, $spe);
//  $where["chir_id"] = db_prepare_in(array_keys($listPrat), $chir_id);
//}

// En fonction de la salle
if ($filter->salle_id) {
  $where["salle_id"] = "= '$filter->salle_id'";
}

$plagesop = $plagesop->loadList($where, $order);

// Operations de chaque plage
foreach($plagesop as $keyPlage => $valuePlage) {
  $plage =& $plagesop[$keyPlage];
  $plage->loadRefsFwd();
  
  $listOp = new COperation;
  $where = array();
  $where["plageop_id"] = "= '".$valuePlage->plageop_id."'";
  switch ($filter->_intervention) {
    case "1" : $where["rank"] = "!= '0'"; break;
    case "2" : $where["rank"] = "= '0'"; break;
  }
  
  if ($filter->_codes_ccam) {
    $where["codes_ccam"] = "LIKE '%$filter->_codes_ccam%'";
  }
  
  $order = "operations.rank";
  $listOp = $listOp->loadList($where, $order);
  if ((sizeof($listOp) == 0) && ($filter->_plage == "false"))
    unset($plagesop[$key]);
  else {
    foreach($listOp as $keyOp => $currOp) {
      $operation =& $listOp[$keyOp];
      $operation->loadRefsFwd();
      $operation->_ref_sejour->loadRefsFwd();
      // On utilise la first_affectation pour contenir l'affectation courante du patient
      $operation->_ref_sejour->_ref_first_affectation = $operation->_ref_sejour->getCurrAffectation($operation->_datetime);
      $affectation =& $operation->_ref_sejour->_ref_first_affectation;
      if ($affectation->affectation_id) {
        $affectation->loadRefsFwd();
        $affectation->_ref_lit->loadCompleteView();
      }
    }
    $plage->_ref_operations = $listOp;
  }
}

// Création du template
$smarty = new CSmartyDP();

$smarty->assign("filter"  , $filter);
$smarty->assign("plagesop", $plagesop);

$smarty->display("view_planning.tpl");

?>