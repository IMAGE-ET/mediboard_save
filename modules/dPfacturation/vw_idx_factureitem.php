<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfacturation
* @version $Revision: $
* @author Alexis / Yohann
*/
 
global $AppUI, $can, $m;

$can->needsRead();

// Chargement de l'item choisi
$libelleItem = new CFactureitem;
$libelleItem->loadAides($AppUI->user_id);

// Reception de l'id de la facture a partir de l'url
$facture_id = mbGetValueFromGetOrSession("facture_id");
$factureitem_id = mbGetValueFromGet("factureitem_id");


// Chargement de la facture demand�
$facture = new CFacture();
$facture->load($facture_id);

$factureitem = new CFactureitem();
$factureitem->load($factureitem_id);
//$factureitem->loadRefs();

// Chargement des donnees de la facture
$facture->loadRefs();


// R�cup�ration de la liste des factures
$itemFacture = new CFacture;
$listFacture = $itemFacture->loadList();
foreach($listFacture as $curr_facture) {
  $curr_facture->loadRefs();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("facture", $facture);
$smarty->assign("factureitem", $factureitem);
$smarty->assign("listFacture", $listFacture);
$smarty->assign("libelleItem", $libelleItem);
$smarty->display("vw_idx_factureitem.tpl");
?>