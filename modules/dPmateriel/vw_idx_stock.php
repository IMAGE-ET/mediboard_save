<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision: $
 *  @author S�bastien Fillonneau
 */
 
global $AppUI, $canRead, $canEdit, $m;

if(!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}

require_once($AppUI->getModuleClass("dPmateriel", "stock"   ));
require_once($AppUI->getModuleClass("dPmateriel", "category"));
require_once($AppUI->getModuleClass("mediusers", "groups"   ));

getChildClasses();

$stock_id = mbGetValueFromGetOrSession("stock_id");

// R�cup�ration des donn�es pour le stock choisi 
$stock = new CStock;
$stock->load($stock_id);
if($stock_id = mbGetValueFromGet("materiel_id")){
  $stock->materiel_id = $stock_id;
}

// R�cup�ration de la liste des Stock
$liststock = new CStock;
$listStock = $liststock->loadList();
foreach($listStock as $key => $value) {
  $listStock[$key]->loadRefsFwd();
  $listStock[$key]->_ref_materiel->loadRefsFwd();
}

// Liste des Groupes
$Groupes = new CGroups;
$listGroupes = $Groupes->loadList();

//Liste des categories
$Cat = new CCategory;
$listCategory = $Cat->loadList();
foreach($listCategory as $key => $value) {
  $listCategory[$key]->loadRefsBack();
}

// Cr�ation du template
require_once( $AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP(1);

$smarty->assign("stock"       , $stock       );
$smarty->assign("listGroupes" , $listGroupes );
$smarty->assign("listCategory", $listCategory);
$smarty->assign("listStock"   , $listStock   );

$smarty->display("vw_idx_stock.tpl");

?>