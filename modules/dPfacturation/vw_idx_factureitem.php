<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfacturation
* @version $Revision: $
* @author Alexis
*/
 
global $AppUI, $can, $m;

$can->needsRead();

// Reception de l'id de la facture a partir de l'url
$facture_id = mbGetValueFromGetOrSession("facture_id");
$factureitem_id = mbGetValueFromGetOrSession("factureitem_id");


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

//$smarty->assign("facture_id", $facture_id);
$smarty->assign("factureitem", $factureitem);
$smarty->assign("facture", $facture);
$smarty->assign("listFacture", $listFacture);
$smarty->display("vw_idx_factureitem.tpl");
?>