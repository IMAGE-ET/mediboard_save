<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPprescription
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $AppUI;

$adm = CValue::post("adm");
$list_administrations = array();
$mode_dossier = CValue::get("mode_dossier");
$refresh_popup = CValue::get("refresh_popup", "0");

$adm = json_decode(stripslashes(utf8_encode($adm)), true);

$sejour = new CSejour();
$date_sel = null;
$tabs_refresh = array();

if (count($adm) > 0) {
	foreach ($adm as $ad) {
		$ad['quantite']    =  is_numeric($ad['quantite']) ? $ad['quantite'] : '';
		$ad['prise_id']    =  is_numeric($ad['key_tab'])  ? $ad['key_tab'] : '';
		$ad['unite_prise'] = !is_numeric($ad['key_tab'])  ? utf8_decode($ad['key_tab']) : '';
		
		$ad['key_tab'] = str_replace('/', '-', $ad['key_tab']);
		
		$date = mbDate($ad['dateTime']);
		$time = mbTime($ad['dateTime']);
    
		// Un peu d'initialisation lourde ...
	  if (!isset($list_administrations[$ad['line_id']])) {
	    $list_administrations[$ad['line_id']] = array();
	  }
	  if (!isset($list_administrations[$ad['line_id']][$ad['key_tab']])) {
	    $list_administrations[$ad['line_id']][$ad['key_tab']] = array();
	  }
	  if (!isset($list_administrations[$ad['line_id']][$ad['key_tab']][$date])) {
	    $list_administrations[$ad['line_id']][$ad['key_tab']][$date] = array();
	  }
	  if (!isset($list_administrations[$ad['line_id']][$ad['key_tab']][$date][$time])) {
	    $list_administrations[$ad['line_id']][$ad['key_tab']][$date][$time] = array();
	  }

	  $curr_adm = &$list_administrations[$ad['line_id']][$ad['key_tab']][$date][$time];
	
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

		// Recherche des chapitres a rafraichir apres la creation des administrations
		switch($line->_class_name){
		  case 'CPrescriptionLineMedicament':
		    if($line->_is_injectable){
		      $tabs_refresh["inj"] = "inj";
		    } else {
		      $tabs_refresh["med"] = "med";
		    }
		    break;
		  case 'CPrescriptionLineMix':
		    $tabs_refresh["perf"] = "perf";
		    break;
		  case 'CPrescriptionLineElement':
		    $chapitre = $line->_ref_element_prescription->_ref_category_prescription->chapitre;
		    $tabs_refresh[$chapitre] = $chapitre;
		    break;
		}
		
		if($line instanceof CPrescriptionLineMedicament){
		  $line->_ref_produit->loadConditionnement();
		  $line->loadRefProduitPrescription();
		}
		$curr_adm['line'] = $line;
		$curr_adm['prise'] = new CPrisePosologie();
		$curr_adm['prise']->quantite = $ad['quantite'];
		$curr_adm['prise_id'] = $ad['prise_id'];
		$curr_adm['dateTime'] = "$date $time";
		$curr_adm['notToday'] = ($date != mbDate());
		
		if (!$date_sel)  $date_sel  = isset($ad['date_sel']) ? $ad['date_sel'] : null;
		if (!$sejour->_id) {
			$line->_ref_prescription->loadRefObject();
			$sejour = $line->_ref_prescription->_ref_object;
			$sejour->loadRefPatient();
			$sejour->_ref_patient->loadRefsAffectations();
			$sejour->_ref_patient->_ref_curr_affectation->updateFormFields();
		}
	}
}

$transmission = new CTransmissionMedicale();
$transmission->loadAides($AppUI->user_id);

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("administrations", $list_administrations);
$smarty->assign("date_sel", $date_sel);
$smarty->assign("sejour", $sejour);
$smarty->assign("transmission", $transmission);
$smarty->assign("mode_dossier", $mode_dossier);
$smarty->assign("tabs_refresh", $tabs_refresh);
$smarty->assign("user_id", $AppUI->user_id);
$smarty->assign("refresh_popup", $refresh_popup);
$smarty->display("inc_vw_add_multiple_administrations.tpl");

?>