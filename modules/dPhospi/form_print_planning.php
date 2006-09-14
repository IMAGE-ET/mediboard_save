<?php /* $Id$*/

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Romain Ollivier
*/
 
global $AppUI, $canRead, $canEdit, $m, $g;

if(!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_READ);

$listSpec = new CFunctions();
$listSpec = $listSpec->loadSpecialites(PERM_READ);

$listServ = new CService();
$where = array();
$where["group_id"] = "= '$g'";
$listServ = $listServ->loadlist($where);

// Cr�ation du template
$smarty = new CSmartyDP(1);

$smarty->assign("today"   , date("Y-m-d")." 06:00:00");
$smarty->assign("tomorrow", date("Y-m-d")." 21:00:00");

$smarty->assign("listPrat", $listPrat);
$smarty->assign("listSpec", $listSpec);
$smarty->assign("listServ", $listServ);

$smarty->display("form_print_planning.tpl");

?>