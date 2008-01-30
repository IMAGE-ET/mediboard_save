<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision: $
 *  @author Alexis Granger
 */

// Recuperation du CIP du produit
$cip = mbGetValueFromGetOrSession("CIP");

// Chargement du produit
$mbProduit = new CBcbProduit();
$mbProduit->load($cip);

// Chargement de la monographie du produit
$mbProduit->loadRefMonographie();

// Chargement de la composition du produit
$mbProduit->loadRefComposition();

// Chargement des donnees technico-reglementaires
$mbProduit->loadRefEconomique();

// Chargement des classes ATC du produit
$mbProduit->loadClasseATC();

// Chargement des classes therapeutiques du produit
$mbProduit->loadClasseTherapeutique();


$mbProduit->loadRefsPosologies();
mbTrace($mbProduits);

$tabEspace = array();
for($i=0; $i<=13; $i++){
  @$tabEspace[$i] = $tabEspace[$i-1]."&nbsp;";
}

// Creation du template
$smarty = new CSmartyDP();
$smarty->assign("tabEspace", $tabEspace);
$smarty->assign("mbProduit", $mbProduit);
$smarty->display("vw_produit.tpl");

?>