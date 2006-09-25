<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

if (!$canEdit) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

$pat_id = mbGetValueFromGetOrSession("patSel");

$fileModule    = CModule::getInstalled("dPfiles");
$fileCptRendus = CModule::getInstalled("dPcompteRendu");

$canReadFiles     = $fileModule->canRead();
$canEditFiles     = $fileModule->canEdit();
$canReadCptRendus = $fileCptRendus->canRead();
$canEditCptRendus = $fileCptRendus->canEdit();

// Liste des mod�les
$listModeleAuth = array();
$listModelePrat = new CCompteRendu;
$listModeleFct = new CCompteRendu;
if ($pat_id) {
  $listPrat = new CMediusers();
  $listPrat = $listPrat->loadPraticiens(PERM_READ);
  $listFct = new CMediusers();
  $listFct = $listFct->loadFonctions(PERM_READ);
  
  $where = array();
  $where["chir_id"] = db_prepare_in(array_keys($listPrat));
  $where["object_id"] = "IS NULL";
  $where["type"] = "= 'patient'";
  $order = "chir_id, nom";
  $listModelePrat = $listModelePrat->loadlist($where, $order);
 
  $where = array();
  $where["function_id"] = db_prepare_in(array_keys($listFct));
  $where["object_id"] = "IS NULL";
  $where["type"] = "= 'patient'";
  $order = "chir_id, nom";
  $listModeleFct = $listModeleFct->loadlist($where, $order);
}

// Liste des Category pour les fichiers
$listCategory = new CFilesCategory;
$listCategory = $listCategory->listCatClass("CPatient");

// L'utilisateur est-il praticien?
$chirSel = new CMediusers;
$mediuser = new CMediusers;
$mediuser->load($AppUI->user_id);
if ($mediuser->isPraticien()) {
  $chirSel = $mediuser;
}

$patSel = new CPatient;
$patSel->load($pat_id);
$patient = new CPatient;
$patient->load($pat_id);
$listPrat = new CMediusers();
$listPrat = $listPrat->loadPraticiens(PERM_READ);

// Chargement des r�f�rences du patient
if ($pat_id) {
  
  // Infos patient compl�tes (tableau de droite)
  $patient->loadDossierComplet();
  
  // Infos patient du cabinet (tableau de gauche)
  $patSel = clone $patient;
  foreach ($patSel->_ref_consultations as $key => $value) {
    if (!array_key_exists($value->_ref_plageconsult->chir_id, $listPrat)) {
    	unset($patSel->_ref_consultations[$key]);
    }
  }

  foreach ($patSel->_ref_sejours as $key => $sejour) {
    if (!array_key_exists($sejour->praticien_id, $listPrat)) {
      unset($patSel->_ref_sejours[$key]);
    } else {
      $patSel->_ref_sejours[$key]->loadRefsFwd();
      $patSel->_ref_sejours[$key]->loadRefsOperations();
      foreach($patSel->_ref_sejours[$key]->_ref_operations as $keyOp => $op) {
        if (!array_key_exists($op->chir_id, $listPrat)) {
          unset($patSel->_ref_sejours[$key]->_ref_operations[$keyOp]);
        } else {
          $patSel->_ref_sejours[$key]->_ref_operations[$keyOp]->loadRefsFwd();
        }
      }
    }
  }
}

$moduleCabinet = CModule::getInstalled("dPcabinet");
$canEditCabinet = $moduleCabinet->canEdit();

$affichageNbFile = CFile::loadNbFilesByCategory($patient);

// Cr�ation du template
$smarty = new CSmartyDP(1);

$smarty->assign("affichageNbFile",$affichageNbFile                           );
$smarty->assign("patSel", $patSel);
$smarty->assign("patient", $patient);
$smarty->assign("chirSel", $chirSel);
$smarty->assign("listPrat", $listPrat);
$smarty->assign("canEditCabinet", $canEditCabinet);
$smarty->assign("listCategory"  , $listCategory );
$smarty->assign("canReadFiles"  , $canReadFiles                              );
$smarty->assign("canEditFiles"  , $canEditFiles                              );
$smarty->assign("canReadCptRendus", $canReadCptRendus                        );
$smarty->assign("canEditCptRendus", $canEditCptRendus                        );
$smarty->assign("listModelePrat", $listModelePrat                            );
$smarty->assign("listModeleFct" , $listModeleFct                             );

$smarty->display("vw_dossier.tpl");

?>