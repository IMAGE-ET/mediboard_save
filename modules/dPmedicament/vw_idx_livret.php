<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

if(!CModule::getActive('bcb')){
  CAppUI::stepMessage(UI_MSG_ERROR, "Le module de m�dicament autonome est en cours de developpement. 
  Pour �tre utilis�, ce module a pour le moment besoin d'�tre connect� � une base de donn�es de m�dicaments externe");
  return;
}

$lettre = CValue::get("lettre");
$category_id  = CValue::getOrSession("category_id", CAppUI::conf('bcb CBcbProduitLivretTherapeutique product_category_id'));


$listProduits = array();

// Chargement de l'etablissement courant
$etablissement = CGroups::loadCurrent();

// Chargement des produits du livret therapeutique
$etablissement->loadRefLivretTherapeutique();

$tabLettre = range('A', 'Z');

// --- Chargement de l'arbre ATC ---
$codeATC     = CValue::get("codeATC");
$classeATC   = new CBcbClasseATC();
$chapitreATC = $codeATC ? $classeATC->getLibelle($codeATC) : ''; // Nom du chapitre selectionn�
$arbreATC    = $classeATC->loadArbre($codeATC); // Chargements des sous chapitres

$categories = array();

if(CModule::getActive("dPstock")) {
  $category = new CProductCategory;
  $categories = $category->loadList(null, "name");
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
$smarty->assign("category_id", $category_id);
$smarty->assign("categories", $categories);

$smarty->display("vw_idx_livret.tpl");

?>