<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision$
 *  @author Romain Ollivier
 */

CCanDo::checkRead();

$user = CMediusers::get();

$pack_examens_labo_id = CValue::getOrSession("pack_examens_labo_id");

// Chargement du pack demand�
$pack = new CPackExamensLabo;
$pack->load($pack_examens_labo_id);
$pack->loadRefs();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("pack", $pack);

$smarty->display("inc_vw_examens_packs.tpl");

?>