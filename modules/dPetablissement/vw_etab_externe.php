<?php

/**
* @package Mediboard
* @subpackage dPetablissement
* @version $Revision: 
* @author Alexis Granger
*/

global $AppUI, $can, $m;

$can->needsRead();

// Recuperation de l'id de l'etablissement externe
$etab_id = mbGetValueFromGetOrSession("etab_id");

// R�cup�ration des etablissements externes
$etabExterne = new CEtabExterne();
$listEtabExternes = $etabExterne->loadList();

if($etab_id){
  $etabExterne->load($etab_id);
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("listEtabExternes", $listEtabExternes );
$smarty->assign("etabExterne"     , $etabExterne      );

$smarty->display("vw_etab_externe.tpl");

?>