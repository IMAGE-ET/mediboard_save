<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkRead();

$prat_id         = CValue::getOrSession("selPrat");
$compte_rendu_id = CValue::getOrSession("compte_rendu_id");

$group = CGroups::loadCurrent();
$listEtab = array($group);

$where = array(
  "group_id" => "= '$group->_id'"
);
$listFunc = new CFunctions();
$listFunc = $listFunc->loadListWithPerms(PERM_EDIT, $where, "text");

// Liste des praticiens accessibles
$listUser = new CMediusers();
//$listUser = $listUser->loadPraticiens(PERM_EDIT);
$listUser = $listUser->loadUsers(PERM_EDIT);

$mediuser = CMediusers::get();

// L'utilisateur est-il praticien?
if (!$prat_id) {
  if ($mediuser->isPraticien()) {
    $prat_id = $mediuser->user_id;
    CValue::setSession("selPrat", $prat_id);
  }
}

// Compte-rendu selectionn�
$compte_rendu = new CCompteRendu();
$compte_rendu->load($compte_rendu_id);
$compte_rendu->loadContent();

if (!$compte_rendu->_id) {
  $compte_rendu->valueDefaults();
  $compte_rendu->author_id = CAppUI::$user->_id;
}

if ($compte_rendu->object_id) {
  $compte_rendu = new CCompteRendu();
}
else{
  $compte_rendu->loadRefCategory();
}

// Gestion du mod�le
$templateManager = new CTemplateManager($_GET);
$templateManager->editor = "ckeditor";

// L'utilisateur est il une secretaire ou un administrateur?
$secretaire = $mediuser->isFromType(array("Secr�taire", "Administrator"));

// si l'utilisateur courant est la secretaire ou le proprietaire du modele alors droit dessus, sinon, seulement droit en lecture
$droit = (!($compte_rendu->_id) ||
           ($secretaire) ||
           ($compte_rendu->user_id == $mediuser->user_id) ||
           ($compte_rendu->function_id == $mediuser->function_id) ||
           $compte_rendu->canEdit());

$templateManager->printMode = !$droit;

if ($compte_rendu->_id) {
  if ($droit) {
    $prat_id = $compte_rendu->user_id;
    $templateManager->valueMode = false;
    $templateManager->loadLists($compte_rendu->user_id, $compte_rendu->_id);
    $templateManager->loadHelpers($compte_rendu->user_id, $compte_rendu->object_class);
    $templateManager->applyTemplate($compte_rendu);
  }

  $templateManager->initHTMLArea();
}

// Class and fields
$listObjectClass     = array();
$listObjectAffichage = array();

foreach (CCompteRendu::getTemplatedClasses() as $valueClass => $localizedClassName){
  $listObjectClass[$valueClass]     = array();
  $listObjectAffichage[$valueClass] = utf8_encode($localizedClassName);
}

foreach ($listObjectClass as $keyClass => $value) {
  $listCategory = CFilesCategory::listCatClass($keyClass);
  foreach($listCategory as $keyCat=>$valueCat){
    $listObjectClass[$keyClass][$keyCat] = utf8_encode($listCategory[$keyCat]->nom);
  }
}

// Headers and footers
$component = new CCompteRendu();
$footers = array();
$headers = array();

if ($compte_rendu->_id) {
  // Si mod�le de fonction, on charge en fonction d'un des praticiens de la fonction
  if ($compte_rendu->user_id) {
    $owner = 'prat';
    $id = $compte_rendu->user_id;
  }
  else if ($compte_rendu->function_id) {
    $owner = 'func';
    $id = $compte_rendu->function_id;
  }
  else if ($compte_rendu->group_id) {
    $owner = 'etab';
    $id = $compte_rendu->group_id;
  } else {
    $owner = 'etab';
    $id = CGroups::loadCurrent()->_id;
  }

  $footers = CCompteRendu::loadAllModelesFor($id, $owner, $compte_rendu->object_class, "footer");
  $headers = CCompteRendu::loadAllModelesFor($id, $owner, $compte_rendu->object_class, "header");
  
  if ($compte_rendu->_owner != "prat") {
    unset($footers["prat"]);
    unset($headers["prat"]);
  }
  
  if ($compte_rendu->_owner == "etab") {
    unset($footers["func"]);
    unset($headers["func"]);
  }
}

$formats = CCompteRendu::$_page_formats;

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("mediuser"            , $mediuser);
$smarty->assign("isPraticien"         , $mediuser->isPraticien());
$smarty->assign("user_id"             , $mediuser->_id);
$smarty->assign("prat_id"             , $prat_id);
$smarty->assign("compte_rendu_id"     , $compte_rendu_id);
$smarty->assign("listPrat"            , $listUser);
$smarty->assign("listEtab"            , $listEtab);
$smarty->assign("listFunc"            , $listFunc);
$smarty->assign("listObjectClass"     , $listObjectClass);
$smarty->assign("compte_rendu"        , $compte_rendu);
$smarty->assign("listObjectAffichage" , $listObjectAffichage);
$smarty->assign("droit"               , $droit);
$smarty->assign("footers"             , $footers);
$smarty->assign("headers"             , $headers);
$smarty->assign("formats"             , $formats);
$smarty->display("addedit_modeles.tpl");

?>