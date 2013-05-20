<?php

/**
 * dPboard
 *   
 * @category dPboard
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$chirSel   = CValue::getOrSession("praticien_id", null);
$all_prats = CValue::get("all_prats", 0);
$board     = CValue::get("board", 0);

$fin   = CValue::getOrSession("fin", CMbDT::date());
$debut = CValue::getOrSession("debut", CMbDT::date("-1 week", $fin));

$mediuser = CMediusers::get();

if ($mediuser->isPraticien()) {
  $chirSel = $mediuser->_id;
}

$user = new CMediusers();
$user->load($chirSel);

$operation = new COperation();

$ljoin = array();
$ljoin["plagesop"] = "plagesop.plageop_id = operations.plageop_id";

$where = array();
$where[] = "operations.date BETWEEN '$debut' AND '$fin' OR plagesop.date BETWEEN '$debut' AND '$fin'";
$where["operations.annulee"] = "= '0'";

if ($all_prats) {
  $prats = $user->loadPraticiens(PERM_READ);
  
  $where["operations.chir_id"]   = CSQLDataSource::prepareIn(array_keys($prats));
  $where[] = "operations.anesth_id IS NULL OR operations.anesth_id ".CSQLDataSource::prepareIn(array_keys($prats));
}
else {
  if ($user->isAnesth()) {
    $where[] = "operations.chir_id = '$user->_id' 
      OR operations.anesth_id = '$user->_id' 
      OR (operations.anesth_id IS NULL && plagesop.anesth_id = '$user->_id')";
  }
  else {
    $where["operations.chir_id"] = "= '$user->_id'";
  }
}

$interventions = $operation->loadList($where, null, null, null, $ljoin);
CMbObject::massLoadFwdRef($interventions, "plageop_id");
$sejours = CMbObject::massLoadFwdRef($interventions, "sejour_id");
CMbObject::massLoadFwdRef($sejours, "patient_id");
$chirs = CMbObject::massLoadFwdRef($interventions, "chir_id");
CMbObject::massLoadFwdRef($chirs, "function_id");

$where = array();
if (!$all_prats) {
  //$where["executant_id"] = "= '$user->_id'";
  $where["code_activite"] = $user->_is_anesth ? "= '4'" : "!= '4'";
}

CMbObject::massCountBackRefs($interventions, "actes_ccam", $where);

foreach ($interventions as $key => $_interv) {
  $_plage = $_interv->loadRefPlageOp();
  
  $_interv->loadExtCodesCCAM(true);
  $codes_ccam = $_interv->_ext_codes_ccam;

  // Nombre d'acte cot�s par le praticien et r�initialisation du count pour le cache
  $nb_actes_ccam = $_interv->_count["actes_ccam"];
  $_interv->_count["actes_ccam"] = null;

  // Aucun acte pr�vu ou cot�
  if (!count($codes_ccam) && !$_interv->_count_actes) {
    $_interv->loadRefSejour();
    $_interv->loadRefChir()->loadRefFunction();
    $_interv->loadRefAnesth()->loadRefFunction();
    $_interv->loadRefPatient();
    continue;
  }
  
  // Actes pr�vus restant en suspend
  $activites = CMbArray::pluck($codes_ccam, "activites");

  $nbCodes = 0;
  foreach ($activites as $_activite) {
    if ($all_prats) {
      $nbCodes += count($_activite);
      continue;
    }
    foreach ($_activite as $_key_activite => $_type_activite) {
      if($user->_is_anesth && $_key_activite == 4) {
        $nbCodes++;
      }
      if(!$user->_is_anesth && $_key_activite != 4) {
        $nbCodes++;
      }
    }
  }

  // Si tout est cot�, on n'affiche pas l'op�ration
  if ($nb_actes_ccam == $nbCodes) {
    unset($interventions[$key]);
    continue;
  }

  $_interv->_actes_non_cotes = $nbCodes - $nb_actes_ccam;
  $_interv->loadRefSejour();
  $_interv->loadRefChir()->loadRefFunction();
  $_interv->loadRefAnesth()->loadRefFunction();
  $_interv->loadRefPatient();

  // Liste des actes CCAM cot�es
  foreach ($_interv->loadRefsActesCCAM() as $_acte) {
    $_acte->loadRefExecutant();
  }
}

$interventions = CModelObject::naturalSort($interventions, array("_datetime"));

$smarty = new CSmartyDP();

$smarty->assign("interventions", $interventions);
$smarty->assign("debut"        , $debut);
$smarty->assign("fin"          , $fin);
$smarty->assign("all_prats"    , $all_prats);
$smarty->assign("board"        , $board);

$smarty->display("../../dPboard/templates/inc_list_interv_non_cotees.tpl");
