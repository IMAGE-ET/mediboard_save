<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision: $
 *  @author Romain Ollivier
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$user = new CMediusers;
$user->load($AppUI->user_id);

$pack_examens_labo_id = mbGetValueFromGetOrSession("pack_examens_labo_id");

// Chargement du pack demand�
$pack = new CPackExamensLabo;
$pack->load($pack_examens_labo_id);
$pack->loadRefs();

//Chargement de tous les packs
$where = array("function_id = '$user->function_id' OR function_id IS NULL");
$order = "libelle";
$listPacks = $pack->loadList($where, $order);
foreach($listPacks as $key => $curr_pack) {
  $listPacks[$key]->loadRefs();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("listPacks", $listPacks);
$smarty->assign("pack"     , $pack     );

$smarty->display("vw_edit_packs.tpl");
?>
