<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$can->needsRead();

$listByPrat = array();
$date = CValue::get("date", mbDate());
$type = CValue::get("type");
$sejour = new CSejour;
$where = array();
$where[] = "DATE(sejour.entree_prevue) = '". $date ."'";
$where["sejour.annule"] = "= '0'";

if($type == "ambucomp") {
  $where[] = "`sejour`.`type` = 'ambu' OR `sejour`.`type` = 'comp'";
} elseif($type) {
  $where["sejour.type"] = " = '$type'";
} else {
  $where[] = "`sejour`.`type` != 'urg' AND `sejour`.`type` != 'seances'";
}

$ljoin = array();
$ljoin["users"] = "users.user_id = sejour.praticien_id";
$order = "users.user_last_name, users.user_first_name, sejour.entree_prevue";

$sejours = $sejour->loadGroupList($where, $order, null, null, $ljoin);

foreach ($sejours as $key => &$sejour) {
  $sejour->loadRefPraticien();
  $sejour->loadRefsAffectations();
  $sejour->loadRefPatient();
  $sejour->loadRefPrestation();
  $sejour->_ref_first_affectation->loadRefLit();
  $sejour->_ref_first_affectation->_ref_lit->loadCompleteView();
  
  $curr_prat = $sejour->praticien_id;
  $listByPrat[$curr_prat]["praticien"] =& $sejour->_ref_praticien;
  $listByPrat[$curr_prat]["sejours"][] =& $sejour;
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("date"      , $date);
$smarty->assign("listByPrat", $listByPrat);
$smarty->assign("total"     , count($sejours));

$smarty->display("print_entrees.tpl");

?>