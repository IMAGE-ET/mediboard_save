<?php /* $Id:  $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision: 
* @author Alexis Granger
*/

global $AppUI, $can, $m;

$type = mbGetValueFromGet("type");
$libelle = mbGetValueFromGet("libelle");

$categories = array();
$tabElements = array();
$elements = array();

// Chargement de la liste des categories
$category_prescription = new CCategoryPrescription();
$category_prescription->chapitre = $type;

if($libelle){
  $categories = $category_prescription->loadMatchingList();
}
// Chargement des elements des categories precedements charg�es
if($libelle){
	$ds = CSQLDataSource::get("std");
	$element_prescription = new CElementPrescription();
	$where = array();
	$where["category_prescription_id"] = $ds->prepareIn(array_keys($categories));
	$where["libelle"] = "LIKE '$libelle%'";
	$elements = $element_prescription->loadList($where);
}
// Rangement des elements trouv�s par categorie

foreach($elements as $element){
	$tabElements[$element->category_prescription_id][] = $element;
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("type", $type);
$smarty->assign("libelle", $libelle);
$smarty->assign("elements", $elements);
$smarty->assign("categories", $categories);
$smarty->assign("tabElements", $tabElements);

$smarty->display("element_selector.tpl");

?>