<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPqualite
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $canAdmin, $m, $g;

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$fiche_ei_id         = mbGetValueFromGetOrSession("fiche_ei_id",null);
$ficheAnnuleVisible  = mbGetValueFromGetOrSession("ficheAnnuleVisible" , 0);
$ficheTermineVisible = mbGetValueFromGetOrSession("ficheTermineVisible" , 0);
$allEi_user_id       = mbGetValueFromGetOrSession("allEi_user_id",null);

$catFiche = array();
$fiche = new CFicheEi;

$droitFiche = !$fiche->load($fiche_ei_id);
$droitFiche = $droitFiche || (!$canEdit && $fiche->user_id!=$AppUI->user_id);
$droitFiche = $droitFiche || ($canEdit && !$canAdmin && $fiche->user_id!=$AppUI->user_id && $fiche->service_valid_user_id!=$AppUI->user_id);


if($droitFiche){
  // Cette fiche n'est pas valide
  $fiche_ei_id = null;
  mbSetValueToSession("fiche_ei_id");
  $fiche = new CFicheEi;
}else{
  $fiche->loadRefsFwd();
  $fiche->loadRefItems();
  
  // Liste des Cat�gories d'EI
  $listCategories = new CEiCategorie;
  $listCategories = $listCategories->loadList(null, "nom");
  //  
  foreach($listCategories as $keyCat=>$valueCat){
    $cattrouvee = null;
    
    foreach($fiche->_ref_items as $keyItem=>$valueItem){
      if($fiche->_ref_items[$keyItem]->ei_categorie_id==$keyCat){
        if(!$cattrouvee){
          $catFiche[$listCategories[$keyCat]->nom] = array();
        }
        $catFiche[$listCategories[$keyCat]->nom][] = $fiche->_ref_items[$keyItem];
        $cattrouvee = 1;
      }
    }
  }
}

$listUsersEdit    = new CMediusers;
$listFiches       = array();
$listUsersTermine = null;
// Liste fiches dont on est l'auteur
$listFiches["AUTHOR"] = array();

// **************************************************************
// Chargement pour Chef de Service
if($canEdit){
	$listFiches["ATT_CS"]      = array();
  $listFiches["ATT_QUALITE"] = array();
  $listFiches["ALL_TERM"]    = array();
}

// **************************************************************
// Chargement pour Administration
if($canAdmin){
	$listFiches["VALID_FICHE"] = array();
  $listFiches["ATT_CS"]      = array();
  $listFiches["ATT_QUALITE"] = array();
  $listFiches["ATT_VERIF"]   = array();
  $listFiches["ATT_CTRL"]    = array();
  $listFiches["ALL_TERM"]    = array();
  $listFiches["ANNULE"]      = array();
  
  // Chargement de la liste des Chef de services / utilisateur
  $module = CModule::getInstalled("dPqualite");
  $permUserEdit = new CPermModule;
  $listUsersEdit = $listUsersEdit->loadListFromType();
  $listUsersTermine = $listUsersEdit;
  foreach($listUsersEdit as $keyUser=>$infoUser){
    if(!$permUserEdit->getInfoModule("permission", $module->mod_id, PERM_EDIT, $keyUser)){
      unset($listUsersEdit[$keyUser]);
    }
  }
}

foreach($listFiches as $keyList=>$valueList){
  $userSel = null;
  $where = null;
  if($keyList=="AUTHOR" || ($canEdit && !$canAdmin)){
    $userSel=$AppUI->user_id;
  }
  
  if($keyList=="ALL_TERM" && $canAdmin){
    $where = array();
    if($allEi_user_id){
      $where["user_id"] = db_prepare("= %",$allEi_user_id);
     }
  }
  
  $listFiches[$keyList] = CFicheEi::loadFichesEtat($keyList,$userSel,$where);
}


// Cr�ation du template
require_once($AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP();

$smarty->assign("user_id"          , $AppUI->user_id);
$smarty->assign("catFiche"         , $catFiche);  
$smarty->assign("fiche"            , $fiche);
$smarty->assign("today"            , mbDate());
$smarty->assign("listFiches"       , $listFiches);
$smarty->assign("listUsersEdit"    , $listUsersEdit);
$smarty->assign("listUsersTermine" , $listUsersTermine);
$smarty->assign("allEi_user_id"    , $allEi_user_id);
$smarty->assign("reloadAjax"       , false);

$smarty->display("vw_incidentvalid.tpl");
?>