<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
  $AppUI->redirect( "m=system&a=access_denied" );
}

$patient_id = mbGetValueFromGetOrSession("patient_id", 0);

$fileModule    = CModule::getInstalled("dPfiles");
$fileCptRendus = CModule::getInstalled("dPcompteRendu");

$canReadFiles     = $fileModule->canRead();
$canEditFiles     = $fileModule->canEdit();
$canReadCptRendus = $fileCptRendus->canRead();
$canEditCptRendus = $fileCptRendus->canEdit();

// Liste des mod�les
$where = array();
$where["object_id"] = "IS NULL";
$where["object_class"] = "= 'CPatient'";
$order = "nom"; 

$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_EDIT);
$where["chir_id"] = db_prepare_in(array_keys($listPrat));
$listModelePrat = $modele->loadlist($where, $order);
unset($where["chir_id"]);

$listFct = new CMediusers();
$listFct = $listFct->loadFonctions(PERM_EDIT);
$where["function_id"] = db_prepare_in(array_keys($listFct));
$listModeleFct = $modele->loadlist($where, $order);
unset($where["function_id"]);

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

// L'utilisateur est-il un anesth�siste
$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);
if ($mediuser->isFromType(array("Anesth�siste"))) {
  $anesth = $mediuser;
} else {
  $anesth = null;
}


// R�cuperation du patient s�lectionn�
$patient = new CPatient;
if(dPgetParam($_GET, "new", 0)) {
  $patient->load(NULL);
  mbSetValueToSession("id", null);
} else {
  $patient->load($patient_id);
}

if ($patient->patient_id) {
  $patient->loadDossierComplet();
}


$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_EDIT);

$moduleCabinet = CModule::getInstalled("dPcabinet");
$canEditCabinet = $moduleCabinet->canEdit();

$affichageNbFile = CFile::loadNbFilesByCategory($patient);

// Cr�ation du template
$smarty = new CSmartyDP(1);

$smarty->assign("affichageNbFile" ,$affichageNbFile                           );
$smarty->assign("patient"         , $patient                                   );
$smarty->assign("chir"            , $chir                                      );
$smarty->assign("anesth"          , $anesth                                    );
$smarty->assign("listPrat"        , $listPrat                                  );
$smarty->assign("canEditCabinet"  , $canEditCabinet                            );
$smarty->assign("listCategory"    , $listCategory                              );
$smarty->assign("canReadFiles"    , $canReadFiles                              );
$smarty->assign("canEditFiles"    , $canEditFiles                              );
$smarty->assign("canReadCptRendus", $canReadCptRendus                        );
$smarty->assign("canEditCptRendus", $canEditCptRendus                        );
$smarty->assign("listModelePrat"  , $listModelePrat                            );
$smarty->assign("listModeleFct"   , $listModeleFct                             );

$smarty->display("inc_vw_patient.tpl");
?>