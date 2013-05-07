<?php 

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision$
* @author Poiron Yohann
*/

CCanDo::checkRead();

$user = CMediusers::get();
$user->loadRefsFwd();
$user->_ref_function->loadRefsFwd();

$etablissement = $user->_ref_function->_ref_group->text;

$rubrique_id = CValue::get("rubrique_id");
 
$rubrique = new CRubrique();
$rubrique->load($rubrique_id);

// R�cup�ration de la liste des functions
$function = new CFunctions();
$listFunc = $function->loadListWithPerms(PERM_EDIT);

$where        = array();
$itemRubrique = new CRubrique;
$order        = "nom DESC";
 
// R�cup�ration de la liste des rubriques hors fonction
$where["function_id"] = "IS NULL";
$listRubriqueGroup    = $itemRubrique->loadList($where,$order);
 
$listRubriqueFonction = array();
// R�cup�ration de la liste des rubriques li�s aux fonctions
foreach($listFunc as $function) {
	$where["function_id"] = "= '$function->function_id'";
	$listRubriqueFonction[$function->text] = $itemRubrique->loadList($where,$order);
}

$smarty = new CSmartyDP();

$smarty->assign("etablissement",        $etablissement);
$smarty->assign("listFunc",             $listFunc);
$smarty->assign("rubrique",             $rubrique);
$smarty->assign("listRubriqueGroup",    $listRubriqueGroup);
$smarty->assign("listRubriqueFonction", $listRubriqueFonction);

$smarty->display("edit_rubrique.tpl");
