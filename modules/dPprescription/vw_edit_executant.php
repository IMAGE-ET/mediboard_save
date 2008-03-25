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
$executant_prescription_line_id = mbGetValueFromGet("executant_prescription_line_id");

$executants = array();
$executant_prescription_line = new CExecutantPrescriptionLine();

// Chargement des executants de la categorie selectionnee
$executant_prescription_line->category_prescription_id = $category_id;
$executants = $executant_prescription_line->loadMatchingList();	

// Chargement de la liste des categories
$category = new CCategoryPrescription();
$categories = $category->loadCategoriesByChap();

// Chargement de la categorie selectionnee
$category->load($category_id);

// Chargement de l'executant selectionne
$executant_prescription_line->load($executant_prescription_line_id);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("category"    , $category);
$smarty->assign("category_id" , $category_id);
$smarty->assign("categories"  , $categories);
$smarty->assign("executant_prescription_line"   , $executant_prescription_line);
$smarty->assign("executants"  , $executants);

$smarty->display("vw_edit_executant.tpl");

?>