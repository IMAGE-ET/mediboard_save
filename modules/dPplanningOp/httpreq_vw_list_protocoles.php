<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

global $dialog;

if ($dialog) {
  CCanDo::checkRead();
}
else {
  CCanDo::checkEdit();
}

// L'utilisateur est-il chirurgien?
$mediuser = CMediusers::get();
$chir_id  = CValue::getOrSession("chir_id", $mediuser->isPraticien() ? $mediuser->user_id : null);
$chir     = new CMediusers();
$chir->load($chir_id);
$function_id  = CValue::getOrSession("function_id");
$type         = CValue::getOrSession("type", "interv"); 
$page         = CValue::get("page");
$sejour_type  = CValue::get("sejour_type");
$step = 30;

$protocole = new CProtocole;
$where = array();

if ($chir->_id) {
  $chir->loadRefFunction();
  $functions = array($chir->function_id);
  $chir->loadBackRefs("secondary_functions");
  foreach ($chir->_back["secondary_functions"] as $curr_sec_func) {
    $functions[] = $curr_sec_func->function_id;
  }
  $list_functions = implode(",", $functions);
  $where [] = "protocole.chir_id = '$chir->_id' OR protocole.function_id IN ($list_functions)";
}
else {
  $where["function_id"] = " = '$function_id'";
}

$where["for_sejour"] = $type == 'interv' ? "= '0'" : "= '1'";

if ($sejour_type) {
  $where["type"] = "= '$sejour_type'";
}

$order = "libelle_sejour, libelle, codes_ccam";

$list_protocoles       = $protocole->loadList($where, $order, "{$page[$type]},$step");

$total_protocoles = $protocole->countList($where);

$systeme_materiel_expert = CAppUI::conf("dPbloc CPlageOp systeme_materiel") == "expert";

foreach ($list_protocoles as $_prot) {
  $_prot->loadRefsFwd();
  if ($systeme_materiel_expert == "expert") {
    $_prot->_types_ressources_ids = implode(",", CMbArray::pluck($_prot->loadRefsBesoins(), "type_ressource_id"));
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("list_protocoles"      , $list_protocoles);
$smarty->assign("total_protocoles"     , $total_protocoles);
$smarty->assign("page"                 , $page);
$smarty->assign("step"                 , $step);
$smarty->assign("chir_id"              , $chir_id);
$smarty->assign("type"                 , $type);

$smarty->display("inc_list_protocoles.tpl");
