<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPprescription
* @version $Revision:  $
* @author Alexis Granger
*/

global $AppUI, $can, $m;

$can->needsRead();

$category_id = mbGetValueFromGetOrSession("category_id");
$element_prescription_id = mbGetValueFromGetOrSession("element_prescription_id");

$elements_prescription = array();
$element_prescription = new CElementPrescription();

// Chargement des elements de prescription de la categorie selectionnee
$element_prescription->category_prescription_id = $category_id;
$elements_prescription = $element_prescription->loadMatchingList();	

// Chargement de la liste des categories
$category = new CCategoryPrescription();
$categories = $category->loadCategoriesByChap();

// Chargement de la categorie selectionnee
$category->load($category_id);

// Chargement de l'element selectionne
$element_prescription->load($element_prescription_id);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("category", $category);
$smarty->assign("category_id", $category_id);
$smarty->assign("categories"   , $categories);
$smarty->assign("element_prescription"     , $element_prescription);
$smarty->assign("elements_prescription", $elements_prescription);

$smarty->display("vw_edit_element.tpl");

?>