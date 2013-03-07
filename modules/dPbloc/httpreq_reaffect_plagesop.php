<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPbloc
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Script � lancer entre minuit et 6h du matin
// pour que les dates limites soient respect�es

global $can;

$mode_real = CValue::get("mode_real", 1);

$can->needsAdmin();

$plageop = new CPlageOp();
$where = array();
$where["plagesop.spec_repl_id"] = "IS NOT NULL";
$where["plagesop.delay_repl"]   = "IS NOT NULL";
$where[] = "`plagesop`.`date` < DATE_ADD('".CMbDT::date()."', INTERVAL `plagesop`.`delay_repl` DAY)";
$where[] = "`plagesop`.`date` >= '".CMbDT::date()."'";
$where["operations.operation_id"] = "IS NULL";
$order = "`plagesop`.`date`, `plagesop`.`debut`";
$limit = null;
$group = null;
$ljoin = array();
$ljoin["operations"] = "operations.plageop_id = plagesop.plageop_id AND operations.annulee = '0'";
$listPlages = $plageop->loadList($where, $order, $limit, $group, $ljoin);
if($mode_real) {
  CAppUI::getMsg("Lancement � : ".CMbDT::dateTime()." en mode r�el");
} else {
  CAppUI::setMsg("Lancement � : ".CMbDT::dateTime()." en mode test");
}
foreach($listPlages as $curr_plage) {
  if($mode_real) {
    // Suppression des interventions annul�es de cette plage pour les mettre en hors plannifi�
    $curr_plage->loadRefsBack();
    foreach($curr_plage->_ref_operations as $curr_op) {
      $curr_op->plageop_id = "";
      $curr_op->date       = $curr_plage->date;
      $curr_op->store();
    }
    // R�affectation de la plage
    $curr_plage->spec_id      = $curr_plage->spec_repl_id;
    $curr_plage->chir_id   = "";
    if($msg = $curr_plage->store()) {
      CAppUI::setMsg($msg, UI_MSG_ERROR);
    } else {
      CAppUI::setMsg("Plage $curr_plage->_id mise � jour", UI_MSG_OK);
    }
  } else {
    $curr_plage->loadRefsFwd(1);
    $curr_plage->loadRefSpecRepl(1);
    if($curr_plage->chir_id) {
      $from = "Dr ".$curr_plage->_ref_chir->_view;
    } else {
      $from = $curr_plage->_ref_spec->_view;
    }
    $msg = "plage du $curr_plage->date de $curr_plage->debut � $curr_plage->fin : $from vers ".$curr_plage->_ref_spec_repl->_view;
    CAppUI::setMsg($msg, UI_MSG_OK);
  }
}

echo CAppUI::getMsg();

?>