<?php

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Alexis Granger
*/

global $can;
$can->needsAdmin();

// Comptage
$count = CBcbProduitLivretTherapeutique::countProduits();
CAppUI::stepAjax("Il y a $count produits dans le livret");

// Purge
if (-1 === $purge = CBcbProduitLivretTherapeutique::purgeProduits()) {
  CAppUI::stepAjax("Impossible de supprimer les produits", UI_MSG_ERROR);
}

CAppUI::stepAjax("$purge produits supprim�s dans le livret", UI_MSG_WARNING);
?>