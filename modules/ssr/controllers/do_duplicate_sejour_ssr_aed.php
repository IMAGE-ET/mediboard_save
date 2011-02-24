<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage ssr
 * @version $Revision:  $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$sejour_id = CValue::post("sejour_id");
$original_sejour_id = CValue::post("original_sejour_id");

$sejour = new CSejour();
$sejour->load($sejour_id);

// Chargement de la prescription de sejour
$sejour->loadRefPrescriptionSejour();

// Verification que la prescription est vide
if($sejour->_ref_prescription_sejour->countBackRefs("prescription_line_element")){
	CAppUI::setMsg("Impossible de dupliquer l'ancien s�jour car ce s�jour poss�de d�j� une prescription", UI_MSG_WARNING);
	CAppUI::redirect('m=ssr&tab=vw_aed_sejour_ssr&sejour_id='.$sejour_id);
}

// Chargement du sejour � dupliquer
$original_sejour = new CSejour();
$original_sejour->load($original_sejour_id);

// Chargement des references: bilan, fiche d'autonomie, prescriptions, evenements
$original_sejour->loadRefBilanSSR();
$bilan_ssr =& $original_sejour->_ref_bilan_ssr;

$original_sejour->loadRefFicheAutonomie();
$fiche_autonomie =& $original_sejour->_ref_fiche_autonomie;

$original_sejour->loadRefPrescriptionSejour();

$prescription_sejour =& $original_sejour->_ref_prescription_sejour;
$prescription_sejour->loadRefsLinesElement();
$lines_element = $prescription_sejour->_ref_prescription_lines_element;

// Chargement evenements de la derniere semaine complete
$original_last_friday = mbDate("last friday", mbDate("+ 1 DAY", $original_sejour->sortie));
$monday = mbDate("last monday", $original_last_friday);
$next_monday = mbDate("next monday", $monday);

// 1er vendredi du nouveau sejour
$next_friday = mbDate("next friday", mbDate("- 1 DAY", $sejour->entree));

// Calcul du nombre de decalage entre les 2 sejours
$nb_decalage = mbDaysRelative($original_last_friday, $next_friday);

$evenement_ssr = new CEvenementSSR();
$where = array();
$where["sejour_id"] = " = '$original_sejour->_id'";
$where["debut"] = " BETWEEN '$monday' AND '$next_monday'";
$evenements = $evenement_ssr->loadList($where);

// Chargement des refs du sejour actuel et suppression des objets existants
$sejour->loadRefBilanSSR();
if($sejour->_ref_bilan_ssr->_id){
  $msg = $sejour->_ref_bilan_ssr->delete();
  CAppUI::displayMsg($msg, "CBilanSSR-msg-delete");
}

$sejour->loadRefFicheAutonomie();
if($sejour->_ref_fiche_autonomie->_id){
  $msg = $sejour->_ref_fiche_autonomie->delete();
  CAppUI::displayMsg($msg, "CFicheAutonomie-msg-delete");
}

if($sejour->_ref_prescription_sejour->_id){
  $msg = $sejour->_ref_prescription_sejour->delete();
  CAppUI::displayMsg($msg, "CPrescription-msg-delete");
}

// Duplication du bilan
$bilan_ssr->_id = "";
$bilan_ssr->sejour_id = $sejour_id;
$msg = $bilan_ssr->store();
CAppUI::displayMsg($msg, "CBilanSSR-msg-create");
		
// Duplication de la fiche d'autonomie
$fiche_autonomie->_id = "";
$fiche_autonomie->sejour_id = $sejour_id;
$msg = $fiche_autonomie->store();
CAppUI::displayMsg($msg, "CFicheAutonomie-msg-create");

// Duplication de la prescription
$prescription_sejour->_id = "";
$prescription_sejour->object_id = $sejour_id;
$msg = $prescription_sejour->store();
CAppUI::displayMsg($msg, "CPrescription-msg-create");

$original_to_new_line = array();
foreach($lines_element as $_line_element){
	$original_line_element_id = $_line_element->_id;
	
	$_line_element->_id = "";
	$_line_element->prescription_id = $prescription_sejour->_id;
	$msg = $_line_element->store();
  CAppUI::displayMsg($msg, "$_line_element->_class_name-msg-create");
	
	$original_to_new_line[$original_line_element_id] = $_line_element->_id;
}

// Duplication des evenements et des codes Cdarrs associ�s
foreach($evenements as $_evenement){
	$_evenement->loadRefsActesCdARR();
	$actes_cdarrs = $_evenement->_ref_actes_cdarr;

	$_evenement->_id = "";
	$_evenement->sejour_id = $sejour_id;
	$_evenement->prescription_line_element_id = $original_to_new_line[$_evenement->prescription_line_element_id];
  $_evenement->debut = mbDateTime("+ $nb_decalage DAYS", $_evenement->debut);
	$msg = $_evenement->store();
	CAppUI::displayMsg($msg, "CEvenementSSR-msg-create");

  foreach($actes_cdarrs as $_acte){
  	$_acte->_id = "";
		$_acte->evenement_ssr_id = $_evenement->_id;
		$msg = $_acte->store();
		CAppUI::displayMsg($msg, "CActeCdARR-msg-create");
  }
}

CAppUI::redirect('m=ssr&tab=vw_aed_sejour_ssr&sejour_id='.$sejour_id);

?>