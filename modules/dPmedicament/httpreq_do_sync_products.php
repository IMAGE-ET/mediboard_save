<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmedicament
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $AppUI;
$can->needsAdmin();

set_time_limit(360);
ini_set('memory_limit', '128M');

$category_id = CValue::get('category_id');

$category = new CProductCategory();
if (!$category->load($category_id)) {
  CAppUI::stepAjax('Veuillez choisir une cat�gorie de produits correspondant au livret th�rapeutique de l\'�tablissement', UI_MSG_ERROR);
  return;
}

$messages = array();

// Chargement du livret th�rapeutique de l'�tablissement
$group = CGroups::loadCurrent();
$group->loadRefLivretTherapeutique('%', 1000, false);

// Chargement des produits du livret th�rapeutique
foreach ($group->_ref_produits_livret as $produit_livret) {
  $produit_livret->addToStocks($category, $group, $messages);
}

foreach ($messages as $msg => $count) {
	CAppUI::stepAjax("$msg x $count", UI_MSG_ALERT);
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