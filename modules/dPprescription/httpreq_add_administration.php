<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

$line_id              = mbGetValueFromGet("line_id");
$object_class         = mbGetValueFromGet("object_class");
$quantite             = mbGetValueFromGet("quantite");
$key_tab              = mbGetValueFromGet("key_tab");
$date                 = mbGetValueFromGet("date");
$heure                = mbGetValueFromGet("heure");
$quantite             = is_numeric($quantite) ? $quantite : '';
$prise_id             = is_numeric($key_tab) ? $key_tab : '';
$unite_prise          = !is_numeric($key_tab) ? utf8_decode($key_tab) : '';
$date_sel             = mbGetValueFromGet("date_sel");
$mode_plan            = mbGetValueFromGet("mode_plan", false);
$prescription_id      = mbGetValueFromGet("prescription_id");
$list_administrations = mbGetValueFromGet("administrations");
$administrations      = array();

// Chargement de la ligne
$line = new $object_class;
$line->load($line_id);

if($line->_class_name == "CPrescriptionLineMedicament"){
  $line->_ref_produit->loadConditionnement();
}

if($list_administrations){
  $_administrations = explode("|",$list_administrations);
  foreach($_administrations as $_administration_id){
    $administration = new CAdministration();
    $administration->load($_administration_id);
    $administration->loadRefsFwd();
    $administration->loadRefLog();
    $line =& $administration->_ref_object;
    $line->loadRefsFwd();
    if($line->_class_name == "CPrescriptionLineMedicament"){
      $line->_ref_produit->loadConditionnement();
    }
    $administrations[$administration->_id] = $administration;
  }
}

// Si une prise est specifi�e (pas de moment unitaire), on charge la prise pour stocker l'unite de prise
if($prise_id){
	$prise = new CPrisePosologie();
	$prise->load($prise_id);
	$unite_prise = $prise->unite_prise;
}

$prise = new CPrisePosologie();
$prise->quantite = $quantite;

$dateTime = ($heure==24) ? "$date 23:59:00" : "$date $heure:00:00";

// Chargement du sejour
$line->_ref_prescription->loadRefObject();
$sejour = $line->_ref_prescription->_ref_object;
$sejour->loadRefPatient();
$sejour->_ref_patient->loadRefsAffectations();

// Heures disponibles pour l'administration
$hours = array('02','04','06','08','10','12','14','16','18','20','22','24');

// Transmission
$transmission = new CTransmissionMedicale();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("date_sel", $date_sel);
$smarty->assign("administrations", $administrations);
$smarty->assign("transmission", $transmission);
$smarty->assign("line", $line);
$smarty->assign("unite_prise", $unite_prise);
$smarty->assign("prise", $prise);
$smarty->assign("sejour", $sejour);
$smarty->assign("date", $date);
$smarty->assign("prise_id", $prise_id);
$smarty->assign("dateTime", $dateTime);
$smarty->assign("date", $date);
$smarty->assign("notToday", $date != mbDate());
$smarty->assign("mode_plan", $mode_plan);
$smarty->assign("hours", $hours);
$smarty->assign("prescription_id", $prescription_id);
$smarty->display("../../dPprescription/templates/inc_vw_add_administration.tpl");
?>