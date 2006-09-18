<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$patient_id = mbGetValueFromGetOrSession("patient_id", 0);

if(!$patient_id) {
  $AppUI->setMsg("Vous devez selectionner un patient", UI_MSG_ALERT);
  $AppUI->redirect("m=dPpatients&tab=0");
}

$fileModule    = CModule::getInstalled("dPfiles");
$fileCptRendus = CModule::getInstalled("dPcompteRendu");

$canReadFiles     = $fileModule->canRead();
$canEditFiles     = $fileModule->canEdit();
$canReadCptRendus = $fileCptRendus->canRead();
$canEditCptRendus = $fileCptRendus->canEdit();

// Liste des modèles
$listModeleAuth = array();

$listModelePrat = new CCompteRendu;
$listModeleFct = new CCompteRendu;

$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_EDIT);
$listFct = new CMediusers();
$listFct = $listFct->loadFonctions(PERM_EDIT);
  
$where = array();
$where["chir_id"] = "IN (".implode(", ",array_keys($listPrat)).")";
$where["object_id"] = "IS NULL";
$where["type"] = "= 'patient'";
$order = "chir_id, nom";  
$listModelePrat = $listModelePrat->loadlist($where, $order);
 
$where = array();
$where["function_id"] = "IN (".implode(", ",array_keys($listFct)).")";
$where["object_id"] = "IS NULL";
$where["type"] = "= 'patient'";
$order = "chir_id, nom";  
$listModeleFct = $listModeleFct->loadlist($where, $order);

// Liste des Category pour les fichiers
$listCategory = new CFilesCategory;
$listCategory = $listCategory->listCatClass("CPatient");

// L'utilisateur est-il un chirurgien
$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);
if ($mediuser->isFromType(array("Chirurgien"))) {
  $chir = $mediuser;
} else {
  $chir = null;
}

// L'utilisateur est-il un anesthésiste
$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);
if ($mediuser->isFromType(array("Anesthésiste"))) {
  $anesth = $mediuser;
} else {
  $anesth = null;
}

// Récuperation du patient sélectionné
$patient = new CPatient;
$patient->load($patient_id);

$patient->loadDossierComplet();

$canEditCabinet = !getDenyEdit("dPcabinet");

// Chargement des fichiers

$accordion_open = 0;
$typeVue        = 1;
$cat_id         = mbGetValueFromGetOrSession("cat_id", 0);
$selClass       = mbGetValueFromGet("selClass", "CPatient");
$selKey         = mbGetValueFromGet("selKey"  , $patient_id);


$listCategory = CFilesCategory::listCatClass($selClass);
if($cat_id != 0){
   $tabCat = array_keys($listCategory);
   $accordion_open = array_search($cat_id , $tabCat);
  if($accordion_open!==""){
    $accordion_open++;
  };
}else{
  $accordion_open = null;
}


$object = new $selClass;
$object->load($selKey);
$object->loadRefsFiles();

$affichageFile = array();
$affichageFile[0] = array();
$affichageFile[0]["name"] = "Aucune Catégorie";
$affichageFile[0]["file"] = array();
foreach($listCategory as $keyCat => $curr_Cat) {
  $affichageFile[$keyCat]["name"] = $curr_Cat->nom;
  $affichageFile[$keyCat]["file"] = array();
}
foreach($object->_ref_files as $keyFile => $FileData){
  $affichageFile[$FileData->file_category_id]["file"][] = $FileData;
}

// Création du template
$smarty = new CSmartyDP(1);

$smarty->assign("patient"         , $patient         );
$smarty->assign("chir"            , $chir            );
$smarty->assign("anesth"          , $anesth          );
$smarty->assign("listPrat"        , $listPrat        );
$smarty->assign("canEditCabinet"  , $canEditCabinet  );
$smarty->assign("listCategory"    , $listCategory    );
$smarty->assign("canReadFiles"    , $canReadFiles    );
$smarty->assign("canEditFiles"    , $canEditFiles    );
$smarty->assign("canReadCptRendus", $canReadCptRendus);
$smarty->assign("canEditCptRendus", $canEditCptRendus);
$smarty->assign("listModelePrat"  , $listModelePrat  );
$smarty->assign("listModeleFct"   , $listModeleFct   );

$smarty->assign("affichageFile"   , $affichageFile   );
$smarty->assign("listCategory"    , $listCategory    );
$smarty->assign("accordion_open"  , $accordion_open  );
$smarty->assign("selClass"        , $selClass        );
$smarty->assign("selKey"          , $selKey          );
$smarty->assign("selView"         , $object->_view   );
$smarty->assign("typeVue"         , $typeVue         );
$smarty->assign("cat_id"          , $cat_id          );

$smarty->display("vw_full_patients.tpl");

?>