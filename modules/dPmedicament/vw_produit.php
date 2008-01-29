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

// Creation du template
$smarty = new CSmartyDP();
$smarty->assign("mbProduit", $mbProduit);
$smarty->display("vw_produit.tpl");

?>