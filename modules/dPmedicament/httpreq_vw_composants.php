<?php

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Alexis Granger
*/


$composant = mbGetValueFromGet("composant", "");
$code = mbGetValueFromGet("code", "");
$libelle = mbGetValueFromGet("libelle", "");

// Chargement des compositions qui contienne le composant recherche
$composition = new CBcbComposition();

if($composant){
  $composition->searchComposant($composant);
}
if($code){
  $composition->searchProduits($code);
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("libelle", $libelle);
$smarty->assign("code", $code);
$smarty->assign("composant", $composant);
$smarty->assign("composition", $composition);

$smarty->display("inc_vw_composants.tpl");


?>