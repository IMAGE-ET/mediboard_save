<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

//if (!$canRead) {
//  $AppUI->redirect("m=system&a=access_denied");
//}

$fileModule     = CModule::getInstalled("dPfiles");
$cptRenduModule = CModule::getInstalled("dPcompteRendu");

$canEditFile  = false;

$selClass      = mbGetValueFromGetOrSession("selClass", null);
$selKey        = mbGetValueFromGetOrSession("selKey"  , null);
$typeVue       = mbGetValueFromGetOrSession("typeVue" , 0);
$accordDossier = mbGetValueFromGet("accordDossier"    , 0);
$reloadlist = 1;

// Liste des Class
$listClass = getChildClasses("CMbObject", array("_ref_files"));

$listCategory = CFilesCategory::listCatClass($selClass);

// Cr�ation du template
$smarty = new CSmartyDP();

$object = null;

if($selClass && $selKey){
  // Chargement de l'objet
  $object = new $selClass;
  $object->load($selKey);
  $canEditFile = $object->canEdit();
  
  $affichageFile = CFile::loadFilesAndDocsByObject($object);
  
  $smarty->assign("affichageFile",$affichageFile);
}

$smarty->assign("reloadlist"     , $reloadlist  ); 
$smarty->assign("listCategory"   , $listCategory);
$smarty->assign("selClass"       , $selClass    );
$smarty->assign("selKey"         , $selKey      );
$smarty->assign("object"         , $object      );
$smarty->assign("typeVue"        , $typeVue     );
$smarty->assign("accordDossier"  , $accordDossier);
$smarty->assign("canEditFile"    , $canEditFile);
if($typeVue==1){
  $smarty->display("inc_list_view_colonne.tpl");
}elseif($typeVue==2){
  $smarty->display("inc_list_view_gd_thumb.tpl");
}else{
  $smarty->display("inc_list_view.tpl");
}
?>
