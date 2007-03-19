<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPmateriel
 *	@version $Revision: $
 *  @author S�bastien Fillonneau
 */
 
global $AppUI, $can, $m;

$can->needsRead();

$reference_id = mbGetValueFromGetOrSession("reference_id");

// Chargement de la reference demand�
$reference=new CRefMateriel;
$reference->load($reference_id);
if($reference_id = mbGetValueFromGet("fournisseur_id")){
  $reference->fournisseur_id = $reference_id;
}

//Chargement de toutes les r�ferences
$lstreference = new CRefMateriel;
$where = array();
$listReference = $lstreference->loadList($where);
foreach($listReference as $key => $value) {
  $listReference[$key]->loadRefsFwd();
}

//Liste des categories Pour liste des materiels
$Cat = new CCategory;
$listCategory = $Cat->loadList();
foreach($listCategory as $key => $value) {
  $listCategory[$key]->loadRefsBack();
}

// Liste des Fournisseur
$Fournisseur = new CFournisseur;
$listFournisseur = $Fournisseur->loadList();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("listCategory"   ,$listCategory   );
$smarty->assign("listFournisseur",$listFournisseur);
$smarty->assign("listReference"  , $listReference );
$smarty->assign("reference"      , $reference     );

$smarty->display("vw_idx_refmateriel.tpl");
?>
