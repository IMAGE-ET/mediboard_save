<?php

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Fabien M�nager
*/

global $can, $AppUI;
$can->needsAdmin();

set_time_limit(360);
ini_set('memory_limit', '128M');

$category_id     = mbGetValueFromGet('category_id');

$category = new CProductCategory();
if (!$category->load($category_id)) {
  CAppUI::stepAjax('Veuillez choisir une cat�gorie de produits correspondant au livret th�rapeutique de l\'�tablissement', UI_MSG_ERROR);
  return;
}

$messages = array();

// Chargement de la liste des dmi de l'etablissement
$dmi_list = new CDMI();
$dmi_list = $dmi_list->loadGroupList();

// Chargement des produits du livret th�rapeutique
foreach ($dmi_list as $dmi) {
	$dmi->loadRefProduit();
	if (!$dmi->_produit_existant) {
	  $product = new CProduct();
	  $product->code = $dmi->code;
	  $product->name = $dmi->nom;
	  $product->description = $dmi->description;
	  $product->category_id = $category_id;
	  $product->quantity = 1;
	  if($msg = $product->store()) {
	  	if (!isset($messages[$msg])) $messages[$msg] = 0;
      $messages[$msg]++;
	  }
	  
	  $stock = new CProductStockGroup();
	  $stock->group_id = CGroups::loadCurrent()->_id;
	  $stock->product_id = $product->_id;
	  $stock->quantity = 1;
	  $stock->order_threshold_min = 1;
	  $stock->order_threshold_max = 2;
	  
	  if($msg = $stock->store()) {
      if (!isset($messages[$msg])) $messages[$msg] = 0;
      $messages[$msg]++;
    }
	}
}

foreach ($messages as $msg => $count) {
	CAppUI::stepAjax("$msg x $count", UI_MSG_ALERT);
}
CAppUI::stepAjax('Synchronisation des produits termin�e', UI_MSG_OK);

// Sauvegarde de la cat�gorie en variable de config
$conf = new CMbConfig();
$data = array();
$data['dmi']['CDMI']['product_category_id'] = $category_id;
if ($conf->update($data, true)) {
  CAppUI::stepAjax('Enregistrement de la cat�gorie de produits effectu�e', UI_MSG_OK);
}

?>