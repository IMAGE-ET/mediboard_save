<?php

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Alexis Granger
*/


$codeATC = mbGetValueFromGet("codeATC");
$dialog = mbGetValueFromGet("dialog");
$classeATC = new CBcbClasseATC();

// Nom du chapitre selectionne
$chapitreATC = $classeATC->getLibelle($codeATC);

// Chargements des sous chapitres
$arbreATC = $classeATC->loadArbre($codeATC);
// Chargement des produits
$classeATC->loadRefsProduits($codeATC);

// Calcul du niveau du code
$niveauCodeATC = $classeATC->getNiveau($codeATC);

// Calcul du code de niveau superieur
$codeNiveauSup = $classeATC->getCodeNiveauSup($codeATC);



// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("dialog", $dialog);
$smarty->assign("codeNiveauSup", $codeNiveauSup);
$smarty->assign("niveauCodeATC", $niveauCodeATC);
$smarty->assign("chapitreATC", $chapitreATC);
$smarty->assign("codeATC", $codeATC);
$smarty->assign("arbreATC", $arbreATC);
$smarty->assign("classeATC", $classeATC);

$smarty->display("inc_vw_arbre_ATC.tpl");


?>