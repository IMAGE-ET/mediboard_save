<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPlabo
 *	@version $Revision: $
 *  @author Romain Ollivier
 */
 
global $AppUI, $can, $m;

$can->needsRead();

//Chargement de tous les catalogues
$catalogue = new CCatalogueLabo;
$where = array("pere_id" => "IS NULL");
$order = "identifiant";
$listCatalogues = $catalogue->loadList($where, $order);
foreach($listCatalogues as $key => $curr_catalogue) {
  $listCatalogues[$key]->loadRefsDeep();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("listCatalogues", $listCatalogues);

$smarty->display("vw_edit_packs.tpl");

?>
