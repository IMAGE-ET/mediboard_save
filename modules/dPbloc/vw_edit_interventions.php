<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkEdit();

if(!($plageop_id = CValue::getOrSession("plageop_id"))) {
  CAppUI::setMsg("Vous devez choisir une plage op�ratoire", UI_MSG_WARNING);
  CAppUI::redirect("m=dPbloc&tab=vw_edit_planning");
}

$anesth = new CTypeAnesth;
$orderanesth = "name";
$anesth = $anesth->loadList(null,$orderanesth);

// Infos sur la plage op�ratoire
$plage = new CPlageOp;
$plage->load($plageop_id);
if(!$plage->temps_inter_op) {
  $plage->temps_inter_op = "00:00:00";
}
$plage->loadRefsFwd();
$plage->loadRefChir()->loadRefFunction();
$plage->loadRefsNotes();

// liste des anesthesistes
$mediuser = new CMediusers();
$listAnesth = $mediuser->loadListFromType(array("Anesth�siste"));

// Chargement des affectation du personnel dans la plage
$plage->loadAffectationsPersonnel();

// Chargement du personnel
$listPersIADE     = CPersonnel::loadListPers("iade");
$listPersAideOp   = CPersonnel::loadListPers("op");
$listPersPanseuse = CPersonnel::loadListPers("op_panseuse");

$affectations_plage["iade"] = $plage->_ref_affectations_personnel["iade"];
$affectations_plage["op"] = $plage->_ref_affectations_personnel["op"];
$affectations_plage["op_panseuse"] = $plage->_ref_affectations_personnel["op_panseuse"];

if (!$affectations_plage["iade"]){
  $affectations_plage["iade"] = array();
}
if (!$affectations_plage["op"]){
  $affectations_plage["op"] = array();
}
if (!$affectations_plage["op_panseuse"]){
  $affectations_plage["op_panseuse"] = array();
}

foreach($affectations_plage["iade"] as $key => $affectation){
  if(array_key_exists($affectation->personnel_id, $listPersIADE)){
    unset($listPersIADE[$affectation->personnel_id]);
  }
}
foreach($affectations_plage["op"] as $key => $affectation){
  if(array_key_exists($affectation->personnel_id, $listPersAideOp)){
    unset($listPersAideOp[$affectation->personnel_id]);
  }
}
foreach($affectations_plage["op_panseuse"] as $key => $affectation){
  if(array_key_exists($affectation->personnel_id, $listPersPanseuse)){
    unset($listPersPanseuse[$affectation->personnel_id]);
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("affectations_plage", $affectations_plage);
$smarty->assign("listPersIADE"      , $listPersIADE);
$smarty->assign("listPersAideOp"    , $listPersAideOp);
$smarty->assign("listPersPanseuse"  , $listPersPanseuse);
$smarty->assign("listAnesth"        , $listAnesth);
$smarty->assign("plage"             , $plage);
$smarty->display("vw_edit_interventions.tpl");

?>