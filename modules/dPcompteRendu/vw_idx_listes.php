<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain OLLIVIER
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

// Liste des praticiens accessibles
$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_EDIT);

$listFunc = new CFunctions();
$listFunc = $listFunc->loadSpecialites(PERM_EDIT);

// Liste des comptes-rendus persos
$where = array();
$user_id = mbGetValueFromGetOrSession("filter_user_id", $AppUI->user_id);
$user = new CMediusers;
$user->load($user_id);
if(!$user->isPraticien()) {
  $user_id = 0;
  $user->load($user_id);
}
if ($user_id) {
	$where["chir_id"] = "= '$user_id'";
} else {
  $inUsers = array();
  foreach($listPrat as $key => $value) {
    $inUsers[] = $key;
  }
  $where ["chir_id"] = "IN (".implode(",", $inUsers).")";
}
$order = "chir_id ASC, nom ASC";
$listesPrat = new CListeChoix();
$listesPrat = $listesPrat->loadList($where, $order);
foreach($listesPrat as $key => $value) {
  $listesPrat[$key]->loadRefsFwd();
}

// Liste des comptes-rendus de cabinet
$where = array();
$inFuncs = array();
if($user_id) {
  $where["function_id"] = "= '$user->function_id'";
} else {
  foreach($listFunc as $key => $value) {
    $inFuncs[] = $listFunc[$key]->function_id;
  }
  $where ["function_id"] = "IN (".implode(",", $inFuncs).")";
}
$order = "function_id ASC, nom ASC";
$listesFunc = new CListeChoix();
$listesFunc = $listesFunc->loadList($where, $order);
foreach($listesFunc as $key => $value) {
  $listesFunc[$key]->loadRefsFwd();
}

// Liste des compte-rendus selectionnables
  // Praticien
$where = array();
$listCrPrat = new CCompteRendu;
if($user_id)
  $where["chir_id"] = "= '$user_id'";
else {
  $inChir = array();
  foreach($listPrat as $key => $value) {
    $inChir[] = $key;
  }
  $where["chir_id"] = "IN (".implode(",", $inChir).")";
}
$order = "type, nom";
$listCrPrat = $listCrPrat->loadList($where, $order);
  // Cabinet
$where = array();
$listCrFunc = new CCompteRendu;
if($user_id)
  $where["function_id"] = "= '$user->function_id'";
else {
  $inFunc = array();
  foreach($listFunc as $key => $value) {
    $inFunc[] = $key;
  }
  $where["function_id"] = "IN (".implode(",", $inFunc).")";
}
$order = "type, nom";
$listCrFunc = $listCrFunc->loadList($where, $order);


// liste s�lectionn�e
$liste_id = mbGetValueFromGetOrSession("liste_id");
$liste = new CListeChoix();
$liste->load($liste_id); 
$liste->loadRefsFwd();

//if (!$liste_id) {
//  $liste->chir_id = $AppUI->user_id;
//}

// Cr�ation du template
$smarty = new CSmartyDP(1);

$smarty->assign("users"     , $listPrat  );
$smarty->assign("user_id"   , $user_id   );
$smarty->assign("listPrat"  , $listPrat  );
$smarty->assign("listFunc"  , $listFunc  );
$smarty->assign("listCrPrat", $listCrPrat);
$smarty->assign("listCrFunc", $listCrFunc);
$smarty->assign("listesPrat", $listesPrat);
$smarty->assign("listesFunc", $listesFunc);
$smarty->assign("liste"     , $liste     );

$smarty->display("vw_idx_listes.tpl");

?>