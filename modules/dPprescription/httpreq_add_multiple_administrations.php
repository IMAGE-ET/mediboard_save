<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Fabien M�nager
 */

$adm = mbGetValueFromGet("adm");
$list_administrations = array();

$sejour_id = null;
$date_sel = null;

if (count($adm) > 0) {
	foreach ($adm as $ad) {
		$ad['quantite']    =  is_numeric($ad['quantite']) ? $ad['quantite'] : '';
		$ad['prise_id']    =  is_numeric($ad['key_tab'])  ? $ad['key_tab'] : '';
		$ad['unite_prise'] = !is_numeric($ad['key_tab'])  ? utf8_decode($ad['key_tab']) : '';
		
		// Un peu d'initialisation lourde ...
	  if (!isset($list_administrations[$ad['line_id']])) {
	    $list_administrations[$ad['line_id']] = array();
	  }
	  if (!isset($list_administrations[$ad['line_id']][$ad['key_tab']])) {
	    $list_administrations[$ad['line_id']][$ad['key_tab']] = array();
	  }
	  if (!isset($list_administrations[$ad['line_id']][$ad['key_tab']][$ad['date']])) {
	    $list_administrations[$ad['line_id']][$ad['key_tab']][$ad['date']] = array();
	  }
	  if (!isset($list_administrations[$ad['line_id']][$ad['key_tab']][$ad['date']][$ad['heure']])) {
	    $list_administrations[$ad['line_id']][$ad['key_tab']][$ad['date']][$ad['heure']] = array();
	  }

	  $curr_adm = &$list_administrations[$ad['line_id']][$ad['key_tab']][$ad['date']][$ad['heure']];
	
		// Si une prise est specifi�e (pas de moment unitaire), on charge la prise pour stocker l'unite de prise
		$curr_adm['unite_prise'] = $ad['unite_prise'];
		if ($ad['prise_id']) {
		  $prise = new CPrisePosologie();
		  $prise->load($ad['prise_id']);
		  $curr_adm['unite_prise'] = $prise->unite_prise;
		}
		
		// Chargement de la ligne
		$line = new $ad['object_class'];
		$line->load($ad['line_id']);
	
		if($line->_class_name == "CPrescriptionLineMedicament"){
		  $line->_ref_produit->loadConditionnement();
		}
		$curr_adm['line'] = $line;
		$curr_adm['prise'] = new CPrisePosologie();
		$curr_adm['prise']->quantite = $ad['quantite'];
		$curr_adm['prise_id'] = $ad['prise_id'];
		
		$curr_adm['dateTime'] = ($ad['heure']==24) ? $ad['date'].' 23:59:00' : $ad['date'].' '.$ad['heure'].':00:00';
		
		if (!$date_sel)  $date_sel  = isset($ad['date_sel']) ? $ad['date_sel'] : null;
		if (!$sejour_id) $sejour_id = $line->_ref_prescription->_ref_object->_id;
	}
}

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("administrations", $list_administrations);
$smarty->assign("date_sel", $date_sel);
$smarty->assign("sejour_id", $sejour_id);
$smarty->assign("transmission", new CTransmissionMedicale());
$smarty->display("inc_vw_add_multiple_administrations.tpl");

?>