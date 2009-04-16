<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPmedicament
 * @version $Revision$
 * @author Alexis Granger
 */

global $can;
$can->needsAdmin();

// Chargement des cat�gories
$category = new CProductCategory();
$categories_list = $category->loadList(null, 'name');

// Analyse des data source BCB
$states = array("bcb1" => null, "bcb2" => null);
foreach ($states as $dsn => &$state) {
  if (null == $ds =  @CSQLDataSource::get($dsn)) {
    continue;
  }
  
  $state["last_modif"] = $ds->loadTable('historiques') ? 
    $ds->loadResult("SELECT MAX( `DATE_`) FROM `historiques`") :
    "Unavailable";
    
  $state["rows_count"] = 0;
  foreach ($ds->loadList("SHOW TABLE STATUS") as $table) {
    $state["rows_count"] += $table["Rows"];
  }
  $state["version"] = $ds->loadResult("SELECT MAX( `DATE_`) FROM `historique_produits_modifies`");
}

mbTrace($states);

// Analyse des livrets th�rapeutiques
if (null == $dsBCBmed = @CBcbObject::getDataSource()) {
  CAppUI::stepMessage(UI_MSG_WARNING, "DataSource-bcb_med-ko");
}
if (null == $dsBCBges = @CSQLDataSource::get("bcbges")) {
  CAppUI::stepMessage(UI_MSG_WARNING, "DataSource-bcb-ges-ko");
}

$nb_produit_livret_med = $dsBCBmed ? CBcbProduitLivretTherapeutique::countProduitsMed() : null;
$nb_produit_livret_ges = $dsBCBges ? CBcbProduitLivretTherapeutique::countProduitsGes() : null;

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign('states', $states);
$smarty->assign('categories_list', $categories_list);
$smarty->assign('nb_produit_livret_med', $nb_produit_livret_med);
$smarty->assign('nb_produit_livret_ges', $nb_produit_livret_ges);

$smarty->display("configure.tpl");

?>