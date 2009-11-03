<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI, $can, $m;

$can->needsRead();

$prescription_id = CValue::getOrSession("prescription_id");

// Liste des alertes
$alertesAllergies    = array();
$alertesInteractions = array();
$alertesIPC          = array();
$alertesProfil       = array();

// Chargement de la cat�gorie demand�
$prescription = new CPrescription();
$prescription->load($prescription_id);

// Chargement des lignes de medicaments
$prescription->loadRefsLinesMed();

// Calcul des alertes
$allergies    = new CBcbControleAllergie();
$profil       = new CBcbControleProfil();
$interactions = new CBcbControleInteraction();
$IPC          = new CBcbControleIPC();
$posologie    = new CBcbControleSurdosage();

if($prescription->object_id) {
  $prescription->loadRefsFwd();
  $object =& $prescription->_ref_object;
  
  $object->loadRefSejour();
  $object->loadRefPatient();
  $patient =& $object->_ref_patient;
  $patient->loadRefDossierMedical();
  $dossier_medical =& $patient->_ref_dossier_medical;
  
  $dossier_medical->updateFormFields();
  $dossier_medical->loadRefsAntecedents();
  $dossier_medical->loadRefsTraitements();
  
  $allergies->setPatient($prescription->_ref_object->_ref_patient);
  $profil->setPatient($prescription->_ref_object->_ref_patient);
	$posologie->setPrescription($prescription);
}

$lines = array();
$lines["prescription"] = $prescription->_ref_prescription_lines;
	  
foreach($lines as &$lines_by_type) {
  foreach($lines_by_type as &$line){
    $line->loadRefsPrises();
	  // Ajout des produits pour les alertes
	  if($prescription->object_id){
	    $allergies->addProduit($line->code_cip);
	    $profil->addProduit($line->code_cip);
	  }
	  $interactions->addProduit($line->code_cip);
	  $IPC->addProduit($line->code_cip);
  }
}
  
$prescription->loadRefsPerfusions();
foreach($prescription->_ref_perfusions as $_perfusion){
  $_perfusion->loadRefsLines();
  foreach($_perfusion->_ref_lines as $_perf_line){
    if($prescription->object_id){
      $allergies->addProduit($_perf_line->code_cip);
      $profil->addProduit($_perf_line->code_cip);  
    }
    $interactions->addProduit($_perf_line->code_cip);
    $IPC->addProduit($_perf_line->code_cip);
  }
}

if($prescription->object_id){
  $alertesAllergies    = $allergies->getAllergies();
  $alertesProfil       = $profil->getProfil();
}
$alertesInteractions = $interactions->getInteractions();
$alertesIPC          = $IPC->getIPC();
$alertesPosologie    = $posologie->getSurdosage();



// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("alertesAllergies"   , $alertesAllergies);
$smarty->assign("alertesInteractions", $alertesInteractions);
$smarty->assign("alertesIPC"         , $alertesIPC);
$smarty->assign("alertesProfil"      , $alertesProfil);
$smarty->assign("alertesPosologie"   , $alertesPosologie);

$smarty->display("vw_full_alertes.tpl");

?>