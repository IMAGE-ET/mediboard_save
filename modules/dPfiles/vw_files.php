<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $can, $m;

$can->needsRead();

$keywords      = mbGetValueFromGetOrSession("keywords", null);
$selClass      = mbGetValueFromGetOrSession("selClass", null);
$selKey        = mbGetValueFromGetOrSession("selKey"  , null);
$selView       = mbGetValueFromGetOrSession("selView" , null);
$typeVue       = mbGetValueFromGetOrSession("typeVue" , 0);
$file_id       = mbGetValueFromGet("file_id"          , null);
$accordDossier = mbGetValueFromGet("accordDossier"    , 0);
$reloadlist    = 0;
$canFile       = new CCanDo;

$file = new CFile;
$file->load($file_id);

$listCategory = CFilesCategory::listCatClass($selClass);

// Cr�ation du template
$smarty = new CSmartyDP();

$object = null;

if($selClass && $selKey){
  // Chargement de l'objet
  $object = new $selClass;
  $object->load($selKey);
  $canFile = $object->canDo();
  $affichageFile = CFile::loadFilesAndDocsByObject($object);

  $smarty->assign("affichageFile",$affichageFile);
}

$smarty->assign("canFile"        , $canFile     );

$smarty->assign("listCategory"   , $listCategory);
$smarty->assign("selClass"       , $selClass    );
$smarty->assign("selKey"         , $selKey      );
$smarty->assign("selView"        , $selView     );
$smarty->assign("file"           , $file        );
$smarty->assign("keywords"       , $keywords    );
$smarty->assign("object"         , $object      );
$smarty->assign("typeVue"        , $typeVue     );
$smarty->assign("reloadlist"     , $reloadlist  );
$smarty->assign("fileSel"        , null);
$smarty->assign("accordDossier"  , $accordDossier);
$smarty->display("vw_files.tpl");

?>