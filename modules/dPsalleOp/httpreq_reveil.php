<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPsalleOp
 * @version $Revision: $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

$date    = CValue::getOrSession("date", mbDate());
$bloc_id = CValue::getOrSession("bloc_id");
$type    = CValue::get("type"); // Type d'affichage => encours, ops, reveil, out

$modif_operation = CCanDo::edit() || $date >= mbDate();

// Selection des plages op�ratoires de la journ�e
$plage = new CPlageOp();
$plage->date = $date;
$plages = $plage->loadMatchingList();

// Selection des salles du bloc
$salle = new CSalle();
$whereSalle = array("bloc_id" => " = '$bloc_id'");
$listSalles = $salle->loadListWithPerms(PERM_READ, $whereSalle);

$where = array();
$where["annulee"] = "= '0'";
$where["salle_id"] = CSQLDataSource::prepareIn(array_keys($listSalles));
$where[] = "plageop_id ".CSQLDataSource::prepareIn(array_keys($plages))." OR (plageop_id IS NULL AND date = '$date')";

switch($type){
  case 'preop':
    $where["entree_salle"] = "IS NULL";
    $order = "time_operation";
    break;
  case 'encours':
    $where["entree_salle"] = "IS NOT NULL";
    $where["sortie_salle"] = "IS NULL";
    $order = "entree_salle";
    break;
  case 'ops':
    $where["sortie_salle"] = "IS NOT NULL";
    $where["entree_reveil"] = "IS NULL";
    $where["sortie_reveil"] = "IS NULL";
    $order = "sortie_salle";
    break;
  case 'reveil':
    $where["entree_reveil"] = "IS NOT NULL";
    $where["sortie_reveil"] = "IS NULL";
    $order = "entree_reveil";
    break;
  case 'out':
    $where["sortie_reveil"] = "IS NOT NULL";
    $order = "sortie_reveil DESC";
    break;
}
  
// Chargement des interventions    
$operation = new COperation();
$listOperations = $operation->loadList($where, $order);

foreach($listOperations as $key => &$op) {
  $op->loadRefSejour(1);
  if($op->_ref_sejour->type == "exte"){
    unset($listOperations[$key]);
    continue;
  }
  
  $op->loadRefChir(1);
  $op->_ref_chir->loadRefFunction();
  $op->loadRefPlageOp(1);
  $op->loadRefPatient(1);
  $op->loadAffectationsPersonnel();
  
  if (($type == "ops" || $type == "reveil") && CModule::getActive("bloodSalvage")) {
    $op->blood_salvage= new CBloodSalvage;
    $where = array();
    $where["operation_id"] = "= '$key'";
    $op->blood_salvage->loadObject($where);
    $op->blood_salvage->loadRefPlageOp();
    $op->blood_salvage->totaltime = "00:00:00";
    if($op->blood_salvage->recuperation_start && $op->blood_salvage->transfusion_end) {
      $op->blood_salvage->totaltime = mbTimeRelative($op->blood_salvage->recuperation_start, $op->blood_salvage->transfusion_end);
    } elseif($op->blood_salvage->recuperation_start){
      $op->blood_salvage->totaltime = mbTimeRelative($op->blood_salvage->recuperation_start,mbDate($op->blood_salvage->_datetime)." ".mbTime());
    }
  }
  
  if($type == "reveil" || $type == "out"){
    $op->_ref_sejour->loadRefsAffectations();
    if($op->_ref_sejour->_ref_first_affectation->_id) {
      $op->_ref_sejour->_ref_first_affectation->loadRefLit();
      $op->_ref_sejour->_ref_first_affectation->_ref_lit->loadCompleteView();
    }
  }
}

// Chargement de la liste du personnel pour le reveil
$personnels = array();
if(Cmodule::getActive("dPpersonnel")) {
  $personnel  = new CPersonnel();
  $personnels = $personnel->loadListPers("reveil");
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("personnels"             , $personnels);
$smarty->assign("listOperations"         , $listOperations);
$smarty->assign("plages"                 , $plages);
$smarty->assign("date"                   , $date);
$smarty->assign("isbloodSalvageInstalled", CModule::getActive("bloodSalvage"));
$smarty->assign("hour"                   , mbTime());
$smarty->assign("modif_operation"        , $modif_operation);
$smarty->display("inc_reveil_$type.tpl");

?>