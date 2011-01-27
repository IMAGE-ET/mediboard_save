<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */
 
// Recuperation des variables
$prescription_id = CValue::get("prescription_id");
$chapitre        = CValue::get("chapitre", "");
$_date_plan_soin = CValue::get("_date_plan_soin");

if(!$_date_plan_soin){
  $_date_plan_soin = mbdate();
}

$logs = array();
$last_log = new CUserLog();

// Chargement de la prescription
$prescription = new CPrescription();
$prescription->load($prescription_id);

$prescription->_date_plan_soin = $_date_plan_soin;

// Chargement du patient
$prescription->loadRefPatient();
$patient =& $prescription->_ref_patient;
$patient->loadIPP();
$patient->loadRefConstantesMedicales();
$poids = $patient->_ref_constantes_medicales->poids;

// Chargement du s�jour
$prescription->loadRefObject();
$sejour =& $prescription->_ref_object;
$sejour->loadNumDossier();
$sejour->loadCurrentAffectation(mbDateTime());

// Chargement des lignes
if($chapitre == "" || $chapitre == "med" || $chapitre == "inj" || $chapitre == "all_med"){
  $prescription->loadRefsLinesMed("1","1","service");
}
$prescription->loadRefsLinesElementByCat("1", $chapitre,"service");

$pharmacien = new CMediusers();

// Parcours et affichage des medicaments
if($prescription->_ref_prescription_lines){
	foreach($prescription->_ref_prescription_lines as $_line){
		$_line->loadRefLogValidationPharma();
		$_line->loadRefPraticien();
		$logs[$_line->_ref_log_validation_pharma->date] = $_line->_ref_log_validation_pharma;
	}
}
// Chargement des lignes de prescription_line_mixes
if($chapitre == "perf" || $chapitre == "all_med" || $chapitre == ""){
	$prescription->loadRefsPrescriptionLineMixes("","1");
	foreach($prescription->_ref_prescription_line_mixes as $_prescription_line_mix){
	  $_prescription_line_mix->loadRefsLines();  
	  $_prescription_line_mix->loadRefPraticien();
	}
}
// Chargement du dernier pharmacien qui a valid� une ligne
if($logs){
  ksort($logs);
  $last_log = end($logs);
  $pharmacien->load($last_log->user_id);
}

if($sejour->_ref_curr_affectation->_id){
  $service_id = $sejour->_ref_curr_affectation->_ref_lit->_ref_chambre->service_id;
} else {
  $service_id = "none";
}

$configs = CConfigService::getAllFor($service_id);

$matin = range($configs["Borne matin min"], $configs["Borne matin max"]);
$soir = range($configs["Borne soir min"], $configs["Borne soir max"]);
$nuit_soir = range($configs["Borne nuit min"], 23);
$nuit_matin = range(00, $configs["Borne nuit max"]);

foreach($matin as &$_hour_matin){
  $_hour_matin = str_pad($_hour_matin, 2, "0", STR_PAD_LEFT);  
}
foreach($soir as &$_soir_matin){
  $_soir_matin = str_pad($_soir_matin, 2, "0", STR_PAD_LEFT);  
}
foreach($nuit_soir as &$_hour_nuit_soir){
  $nuit[] = str_pad($_hour_nuit_soir, 2, "0", STR_PAD_LEFT);
}
foreach($nuit_matin as &$_hour_nuit_matin){
  $nuit[] = str_pad($_hour_nuit_matin, 2, "0", STR_PAD_LEFT);
}

// Recuperation de l'heure courante
if($_date_plan_soin == mbDate()){
  $time = mbTransformTime(null,null,"%H");
} else {
  $time = "16";
}

// Construction de la structure de date � parcourir dans le tpl
if(in_array($time, $matin)){
  $dates = array(mbDate("- 1 DAY", $_date_plan_soin) => array("nuit" => $nuit), 
                 $_date_plan_soin                    => array("matin" => $matin, "soir" => $soir));        
  $count_colspan = array(count($nuit),count($matin),count($soir)); 
}
if(in_array($time, $soir)){
  $dates = array($_date_plan_soin                    => array("matin" => $matin, "soir" => $soir, "nuit" => $nuit));
  $count_colspan = array(count($matin),count($soir),count($nuit)); 
}
if(in_array($time, $nuit)){
  $dates = array($_date_plan_soin                    => array("soir" => $soir, "nuit" => $nuit), 
                 mbDate("+ 1 DAY", $_date_plan_soin) => array("matin" => $matin));
  $count_colspan = array(count($soir),count($nuit),count($matin)); 
}

$composition_dossier = array();
foreach($dates as $curr_date => $_date){
  foreach($_date as $moment_journee => $_hours){
    $composition_dossier[] = "$curr_date-$moment_journee";
    foreach($_hours as $_hour){
      $date_reelle = $curr_date;
      if($moment_journee == "nuit" && $_hour < "12:00:00"){
        $date_reelle = mbDate("+ 1 DAY", $curr_date);
      }
      $_dates[$date_reelle] = $date_reelle;
      $tabHours[$curr_date][$moment_journee][$date_reelle]["$_hour:00:00"] = $_hour;
    }
  }
}

$prescription->calculPlanSoin($_dates, 0);

// Chargement des categories
$categories = CCategoryPrescription::loadCategoriesByChap();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("prescription", $prescription);
$smarty->assign("last_log", $last_log);
$smarty->assign("pharmacien", $pharmacien);
$smarty->assign("tabHours", $tabHours);
$smarty->assign("prescription_id", $prescription_id);
$smarty->assign("dates", $dates);
$smarty->assign("categories", $categories);
$smarty->assign("patient", $patient);
$smarty->assign("sejour", $sejour);
$smarty->assign("poids", $poids);
$smarty->assign("composition_dossier" , $composition_dossier);
$smarty->assign("matin", $matin);
$smarty->assign("soir", $soir);
$smarty->assign("nuit", $nuit);
$smarty->assign("count_colspan", $count_colspan);
$smarty->assign("chapitre", $chapitre);
$smarty->assign("_line_med", new CPrescriptionLineMedicament());
$smarty->assign("_category", new CCategoryPrescription());
$smarty->display("vw_plan_soin.tpl");

?>