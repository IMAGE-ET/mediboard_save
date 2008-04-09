<?php 

/**
* @package Mediboard
* @subpackage dPurgences
* @version $Revision:  $
* @author Alexis Granger
*/

global $AppUI, $can, $m, $g;
$can->needsRead();

// Chargement des etablissements externes
$order = "nom";
$etab = new CEtabExterne();
$listEtab = $etab->loadList(null, $order);

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("listEtab", $listEtab);
$smarty->assign("_transfert_id", "");

$smarty->display("../../dPurgences/templates/inc_vw_etab_externes.tpl");

?>