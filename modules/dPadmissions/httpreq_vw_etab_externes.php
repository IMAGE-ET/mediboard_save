<?php 

/**
* @package Mediboard
* @subpackage dPadmissions
* @version $Revision:  $
* @author Alexis Granger
*/

global $AppUI, $can, $m, $g;
$can->needsRead();

// Chargement des etablissements externes
$etab = new CEtabExterne();
$listEtab = $etab->loadList();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("listEtab", $listEtab);

$smarty->display("httpreq_vw_etab_externes.tpl");

?>