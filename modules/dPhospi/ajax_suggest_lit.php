<?php /* $Id: ajax_suggest_lit.php $ */

/**
 * @package Mediboard
 * @subpackage dPhospi
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$affectation_id = CValue::get("affectation_id");
$sejour_id      = CValue::get("sejour_id");
$all_services   = CValue::get("all_services", 0);

if (!$affectation_id) {
  $all_services = 1;
}

$entree = null;
$sortie = null;
$lit_id = null;

if ($affectation_id) {
  $affectation = new CAffectation;
  $affectation->load($affectation_id)->loadRefLit()->loadRefChambre();
  $services_ids = array($affectation->_ref_lit->_ref_chambre->service_id);
  $entree = $affectation->entree;
  $sortie = $affectation->sortie;
}

if ($sejour_id) {
  $sejour = new CSejour;
  $sejour->load($sejour_id);
  $entree = $sejour->entree;
  $sortie = $sejour->sortie;
}

if ($all_services) {
  $service = new CService;
  $where = array();
  $where["group_id"] = "= '".CGroups::loadCurrent()->_id."'";
  $services_ids = array_keys($service->loadListWithPerms(PERM_READ, $where, "externe, nom"));
}

$where = array();
$where["chambre.service_id"] = CSQLDataSource::prepareIn($services_ids);

$ljoin = array();
$ljoin["chambre"] = "lit.chambre_id = chambre.chambre_id";

$lit = new CLit;
$lits = $lit->loadList($where, null, null, null, $ljoin);

if ($affectation_id) {
  unset($lits[$affectation->lit_id]);
}

foreach ($lits as $key => $_lit) {
  $_lit->_ref_affectations = array();
  $_lit->loadCompleteView();
  
  $where = array();
  $where["lit_id"] = "= '$_lit->_id'";
  $where["entree"] = "<= '$sortie'";
  $where["sortie"] = ">= '$entree'";
  
  $affectation_collide = new CAffectation;
  $affectation_collide->loadObject($where);
  
  if ($affectation_collide->_id) {
    unset($lits[$key]);
    continue;
  }
  
  $where = array(
    "lit_id" => "= '$_lit->_id'",
    "sortie" => "<= '$entree'");
  $_lit->_ref_last_dispo = new CAffectation;
  $_lit->_ref_last_dispo->loadObject($where, "sortie DESC");
  
  $where = array(
    "lit_id" => "= '$_lit->_id'",
    "entree" => " >= '$sortie'");
  $_lit->_ref_next_dispo = new CAffectation;
  $_lit->_ref_next_dispo->loadObject($where, "entree ASC");
  
  $_lit->_dispo_depuis = strtotime($entree) - strtotime($_lit->_ref_last_dispo->sortie);
  
  if ($_lit->_dispo_depuis < 0) {
    unset($lits[$key]);
    continue;
  }
  
  $_lit->_dispo_depuis_friendly = CMbDate::relative($_lit->_ref_last_dispo->sortie, $entree);
  
  if ($_lit->_ref_next_dispo->entree) {
    $_lit->_occupe_dans = strtotime($_lit->_ref_next_dispo->entree) - strtotime($sortie);
    
    if ($_lit->_occupe_dans < 0) { 
      unset($lits[$key]);
      continue;
    }
    $_lit->_occupe_dans_friendly = CMbDate::relative($sortie, $_lit->_ref_next_dispo->entree); 
  }
  else {
    $_lit->_occupe_dans = "libre";
  }
}

$sorter = CMbArray::pluck($lits, "_dispo_depuis");
array_multisort($sorter, SORT_ASC, $lits);

$max_entree = max($sorter);
$max_sortie = max(CMbArray::pluck($lits, "_occupe_dans"));

$smarty = new CSmartyDP;

$smarty->assign("all_services", $all_services);
$smarty->assign("lits", $lits);
$smarty->assign("affectation_id", $affectation_id);
$smarty->assign("sejour_id"     , $sejour_id);
$smarty->assign("max_entree", $max_entree);
$smarty->assign("max_sortie", $max_sortie);

$smarty->display("inc_suggest_lit.tpl");
?>