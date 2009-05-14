<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

// Chargement de l'etablissement courant
$etablissement = CGroups::loadCurrent();
$produits_livret = array();
// Chargement des produits du livret therapeutique
$etablissement->loadRefLivretTherapeutique('%', 2000, false);

foreach($etablissement->_ref_produits_livret as $_produit_livret){
  $_produit_livret->_ref_produit->isInT2A();
  $_produit = $_produit_livret->_ref_produit;
  $produits_livret["$_produit->libelle-$_produit->code_cip"] = $_produit_livret;
}

// Tri par ordre alphabetique du livret therapeutique
ksort($produits_livret);
  
// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("date", mbDate());
$smarty->assign("produits_livret", $produits_livret);
$smarty->display("print_livret.tpl");

?>