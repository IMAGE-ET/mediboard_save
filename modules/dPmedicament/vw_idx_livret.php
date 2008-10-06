<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision: $
 *  @author Alexis Granger
 */

$lettre = mbGetValueFromGet("lettre");

$listProduits = array();

// Chargement de l'etablissement courant
$etablissement = CGroups::loadCurrent();

// Chargement des produits du livret therapeutique
$etablissement->loadRefLivretTherapeutique();

$tabLettre = range('A', 'Z');

// --- Chargement de l'arbre ATC ---
$codeATC = mbGetValueFromGet("codeATC");
$classeATC = new CBcbClasseATC();
if ($codeATC) {
	$chapitreATC = $classeATC->getLibelle($codeATC); // Nom du chapitre selectionn�
	$arbreATC = $classeATC->loadArbre($codeATC); // Chargements des sous chapitres
} else {
	$chapitreATC = '';
	$arbreATC = array();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("listProduits", $listProduits);
$smarty->assign("arbreATC", $arbreATC);
$smarty->assign("codeATC", $codeATC);
$smarty->assign("chapitreATC", $chapitreATC);
$smarty->assign("lettre", $lettre);
$smarty->assign("produits_livret", $etablissement->_ref_produits_livret);
$smarty->assign("tabLettre", $tabLettre);

$smarty->display("vw_idx_livret.tpl");

?>