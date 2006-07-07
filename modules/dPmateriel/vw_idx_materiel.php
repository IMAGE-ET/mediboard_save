<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision: $
 *  @author S�bastien Fillonneau
 */
 
global $AppUI, $canRead, $canEdit, $m;

if (!$canRead) {
	$AppUI->redirect( "m=system&a=access_denied" );
}
 
require_once( $AppUI->getModuleClass("dPmateriel", "materiel") );
require_once( $AppUI->getModuleClass("dPmateriel", "category") );

$materiel_id = mbGetValueFromGetOrSession("materiel_id", null);

// Chargement du mat�riel demand�
$materiel=new CMateriel;
$materiel->load($materiel_id);
$materiel->loadRefsBack();

foreach($materiel->_ref_stock as $key => $value) {
  $materiel->_ref_stock[$key]->loadRefsFwd();
  $materiel->_ref_stock[$key]->_ref_group->loadRefsFwd();
}
foreach($materiel->_ref_refMateriel as $key => $value) {
  $materiel->_ref_refMateriel[$key]->loadRefsFwd();
}


// Liste des Cat�gories
$Categories = new CCategory;
$listCategories = $Categories->loadList();

//Chargement de tous les mat�riels
$lstmateriel = new CMateriel;
$where = array();
$listMateriel = $lstmateriel->loadList($where);
foreach($listMateriel as $key => $value) {
  $listMateriel[$key]->loadRefsFwd();
}

// Cr�ation du template
require_once( $AppUI->getSystemClass("smartydp"));
$smarty = new CSmartyDP;
$smarty->assign("listMateriel", $listMateriel);
$smarty->assign("materiel", $materiel);
$smarty->assign("listCategories", $listCategories);
$smarty->display('vw_idx_materiel.tpl');

?>