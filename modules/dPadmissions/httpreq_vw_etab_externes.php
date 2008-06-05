<?php 

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision:  $
* @author Alexis Granger
*/

global $AppUI, $can, $m, $g;
$can->needsRead();

// Chargement su s�jour s'il y en a un
$sejour = new CSejour();
$sejour->load(mbGetValueFromGet("sejour_id"));
$etabSelected = $sejour->etablissement_transfert_id;

// Chargement des etablissements externes
$order = "nom";
$etab = new CEtabExterne();
$listEtab = $etab->loadList(null, $order);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("etabSelected", $etabSelected);
$smarty->assign("listEtab", $listEtab);

$smarty->display("httpreq_vw_etab_externes.tpl");

?>