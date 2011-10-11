<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$orderby = CValue::get("orderby", "libelle");
$function_guid = CValue::get("function_guid", null);

$produits_livret = array();

// Chargement des produits du livret therapeutique
$produits_livret_temp = CBcbProduit::loadRefLivretTherapeutique($function_guid, '%', 2000, false);

foreach($produits_livret_temp as $_produit_livret){
  $_produit_livret->_ref_produit->isInT2A();
  $_produit = $_produit_livret->_ref_produit;
  
  $_produit->loadClasseATC();
  $classe_atc = reset($_produit->_ref_classes_ATC);
	
  if(isset($classe_atc->classes)){
	  $classe_atc = end($classe_atc->classes);
	  $_produit_livret->_atc = $classe_atc["code"];
	} else {
		$_produit_livret->_atc = "";
	}
	  
  if ($orderby === "libelle")
    $produits_livret["$_produit->libelle-$_produit->code_cip"] = $_produit_livret;
  else {
    $produits_livret["$_produit_livret->_atc-$_produit->libelle-$_produit->code_cip"] = $_produit_livret;
  }
}

// Tri par ordre alphabetique du livret therapeutique
ksort($produits_livret);
  
// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("date", mbDate());
$smarty->assign("produits_livret", $produits_livret);

$smarty->display("print_livret.tpl");

?>