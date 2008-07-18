<?php

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Fabien M�nager
*/

global $can, $g;

$can->needsAdmin();

set_time_limit(360);
ini_set('memory_limit', '128M');

$category_id = mbGetValueFromGet('category_id');
$category = new CProductCategory();
if (!$category_id || !$category->load($category_id)) {
  CAppUI::stepAjax('Veuillez choisir une cat�gorie de produits correspondant au livret th�rapeutique de l\'�tablissement', UI_MSG_ERROR);
  return;
}

// Chargement du livret th�rapeutique de l'�tablissement
$group = new CGroups();
$group->load($g);
$group->loadRefLivretTherapeutique();

// Chargement des produits du livret th�rapeutique
foreach ($group->_ref_produits_livret as $produit_livret) {
  $product = new CProduct();
  $prod = $produit_livret->_ref_produit;
  $product->name = $prod->libelle;
  $product->description = $produit_livret->commentaire;
  $product->code = $produit_livret->code_cip;
  $product->category_id = $category_id;
  
  // Sauvegarde du nouveau produit correspondant au m�dicament
  if (!($msg = $product->store())) {
    $stock = new CProductStockGroup();
    $stock->product_id = $product->_id;
    $stock->group_id = $g;
    $stock->quantity = 1;
    $stock->order_threshold_min = 1;
    $stock->order_threshold_max = 1;
    if ($msg = $stock->store()) {
      CAppUI::stepAjax($msg, UI_MSG_ALERT);
    }
  } else {
    CAppUI::stepAjax($msg, UI_MSG_ALERT);
  }
}

CAppUI::stepAjax('Synchronisation des produits termin�e', UI_MSG_OK);

// Sauvegarde de la cat�gorie en variable de config
$conf = new CMbConfig();
$data = array();
$data['dPmedicament']['CBcbProduitLivretTherapeutique']['product_category_id'] = $category_id;
if ($conf->update($data, true)) {
  CAppUI::stepAjax('Enregistrement de la cat�gorie de produits effectu�e', UI_MSG_OK);
}

?>