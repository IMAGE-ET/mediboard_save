<?php /* $Id: vw_protocoles.php 7210 2009-11-03 12:18:57Z rhum1 $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: 7210 $
* @author Thomas Despoix
*/

global $AppUI, $can, $m, $dialog;

if($dialog) {
  $can->needsRead();
} else {
  $can->needsEdit();
}

// L'utilisateur est-il chirurgien?
$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);

$chir_id      = $mediuser->isPraticien() ? $mediuser->user_id : null;

$chir_id      = CValue::getOrSession("chir_id", $chir_id);
$protocole_id = CValue::getOrSession("protocole_id");
$code_ccam    = CValue::getOrSession("code_ccam");
$type         = CValue::getOrSession("type", "interv"); 
$page         = CValue::get("page");

$listPrat   = $mediuser->loadPraticiens(PERM_EDIT);

$protocole = new CProtocole;
$where = array();

$where["chir_id"] = CSQLDataSource::prepareIn(array_keys($listPrat), $chir_id);

if ($code_ccam) {
	$where["codes_ccam"] = " LIKE '%$code_ccam%'";
}

if ($type == 'interv') {
  $where["for_sejour"] = "= '0'";
}
else {
	$where["for_sejour"] = "= '1'";
}

$ljoin = array(
  "users" => "users.user_id = protocole.chir_id"
);

$list_protocoles  = $protocole->loadList($where, "users.user_username, libelle, libelle_sejour, codes_ccam", "{$page[$type]},40", null, $ljoin);
$total_protocoles = $protocole->countList($where, null, null, null, $ljoin);

foreach ($list_protocoles as $_prot){
  $_prot->loadRefsFwd();
}

// Protocole selectionn�
$protocole = new CProtocole;
if($protocole->load($protocole_id)) {
  $protocole->loadRefs();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("list_protocoles", $list_protocoles);
$smarty->assign("total_protocoles", $total_protocoles);
$smarty->assign("page"      , $page      );
$smarty->assign("protocole" , $protocole   );
$smarty->assign("chir_id"   , $chir_id   );
$smarty->assign("code_ccam" , $code_ccam );
$smarty->assign("type"      , $type      );

$smarty->display("inc_list_protocoles.tpl");

?>