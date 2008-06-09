<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

global $AppUI;

// chargement des categories de prescription et des elements associ�s
$chapitres = CCategoryPrescription::loadCategoriesByChap();
foreach($chapitres as &$categories){
	foreach($categories as &$category){
		$category->loadElementsPrescription();
	}
}

$prescription_id = mbGetValueFromGet("prescription_id");

// chargement de la prescription
$prescription = new CPrescription();
$prescription->load($prescription_id);
$prescription->loadRefsLinesElement();

$elements = array();
foreach($prescription->_ref_prescription_lines_element as $_line_element){
	$elements[] = $_line_element->element_prescription_id; 
}


if($prescription->_ref_object->_class_name == "CSejour"){
	// Chargement des dates des operations
	$sejour =& $prescription->_ref_object;
	$sejour->makeDatesOperations();
}

// Chargement de la liste des moments
$moments = CMomentUnitaire::loadAllMomentsWithPrincipal();

// chargement des medicaments favoris du praticien
$medicaments = array();
if($prescription->_current_praticien_id){
  $medicaments = CPrescription::getFavorisMedPraticien($prescription->_current_praticien_id);
}

$filter_line_element = new CPrescriptionLineMedicament();
$filter_line_element->debut = mbDate();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("elements", $elements);
$smarty->assign("chapitres", $chapitres);
$smarty->assign("filter_line_element", $filter_line_element);
$smarty->assign("moments", $moments);
$smarty->assign("prise_posologie", new CPrisePosologie());
$smarty->assign("class_category", new CCategoryPrescription());
$smarty->assign("medicaments", $medicaments);
$smarty->assign("prescription", $prescription);
$smarty->assign("prescription_id", $prescription_id);
$smarty->display("../../dPprescription/templates/vw_easy_mode.tpl");

?>