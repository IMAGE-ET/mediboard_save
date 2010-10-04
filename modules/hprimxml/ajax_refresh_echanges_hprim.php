<?php /* $Id $ */

/**
 * @package Mediboard
 * @subpackage hprimxml
 * @version $Revision: 9914 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

CCanDo::checkRead();

$id_permanent        = CValue::getOrSession("id_permanent");
$t                   = CValue::getOrSession('types', array());
$statut_acquittement = CValue::getOrSession("statut_acquittement");
$msg_evenement       = CValue::getOrSession("msg_evenement", "patients");
$type_evenement      = CValue::getOrSession("type_evenement");
$page                = CValue::get('page', 0);
$_date_min           = CValue::getOrSession('_date_min', mbDateTime("-7 day"));
$_date_max           = CValue::getOrSession('_date_max', mbDateTime("+1 day"));

// Chargement de l'�change HPRIM demand�
$echange_hprim = new CEchangeHprim();

// R�cup�ration de la liste des echanges HPRIM
$itemEchangeHprim = new CEchangeHprim;

$where = array();
if (isset($t["emetteur"])) {
  $where["emetteur_id"] = " IS NULL";
}
if (isset($t["destinataire"])) {
  $where["destinataire_id"] = " IS NULL";
}
if ($_date_min && $_date_max) {
  $where['date_production'] = " BETWEEN '".$_date_min."' AND '".$_date_max."' "; 
}
if ($statut_acquittement) {
  $where["statut_acquittement"] = " = '".$statut_acquittement."'";
}
if ($msg_evenement) {
  $where["type"] = " = '".$msg_evenement."'";
}
if ($type_evenement) {
  $where["sous_type"] = " = '".$type_evenement."'";
}
if (isset($t["message_invalide"])) {
  $where["message_valide"] = " = '0'";
}
if (isset($t["acquittement_invalide"])) {
  $where["acquittement_valide"] = " = '0'";
}
if (isset($t["no_date_echange"])) {
  $where["date_echange"] = "IS NULL";
}
if ($id_permanent) {
  $where["id_permanent"] = " = '$id_permanent'";
}

$where["group_id"] = "= '".CGroups::loadCurrent()->_id."'";

$total_echange_hprim = $itemEchangeHprim->countList($where);
$order = "date_production DESC";
$forceindex[] = "date_production";

$echangesHprim = $itemEchangeHprim->loadList($where, $order, "$page, 20", null, null, $forceindex);
  
foreach($echangesHprim as $_echange) {
  $_echange->loadRefNotifications();
  $_echange->getObservations();
  $_echange->loadRefsDestinataireHprim();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("echange_hprim"       , $echange_hprim);
$smarty->assign("echangesHprim"       , $echangesHprim);
$smarty->assign("total_echange_hprim" , $total_echange_hprim);
$smarty->assign("page"                , $page);
$smarty->assign("selected_types"      , $t);
$smarty->assign("statut_acquittement" , $statut_acquittement);
$smarty->assign("msg_evenement"       , $msg_evenement);
$smarty->assign("type_evenement"      , $type_evenement);

$smarty->display("inc_echanges_hprim.tpl");

?>