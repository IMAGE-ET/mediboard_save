<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision$
 *  @author S�bastien Fillonneau
 */
 
global $AppUI, $can, $m;

$can->needsRead();

getChildClasses();

$stock_id = CValue::getOrSession("stock_id");

// R�cup�ration des donn�es pour le stock choisi 
$stock = new CStock;
$stock->load($stock_id);
if($materiel_id = CValue::get("materiel_id")){
  $stock->materiel_id = $materiel_id;
}

// R�cup�ration de la liste des Stock
$itemStock = new CStock;
$listStock = $itemStock->loadList();
foreach($listStock as &$curr_stock) {
  $curr_stock->loadRefsFwd();
  $curr_stock->_ref_materiel->loadRefsFwd();
}

// Liste des Groupes
$groupe = new CGroups;
$listGroupes = $groupe->loadList();

//Liste des categories
$cat = new CCategory;
$listCategory = $cat->loadList();
foreach($listCategory as &$curr_cat) {
  $curr_cat->loadRefsBack();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("stock"       , $stock       );
$smarty->assign("listGroupes" , $listGroupes );
$smarty->assign("listCategory", $listCategory);
$smarty->assign("listStock"   , $listStock   );

$smarty->display("vw_idx_stock.tpl");

?>