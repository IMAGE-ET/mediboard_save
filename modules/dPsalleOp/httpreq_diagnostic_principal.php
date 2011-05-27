<?php

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Romain Ollivier
*/

$sejour_id = CValue::getOrSession("sejour_id");
$modeDAS   = CValue::getOrSession("modeDAS", 1);

$sejour = new CSejour;
$sejour->load($sejour_id);
$sejour->loadExtDiagnostics();
$sejour->loadRefDossierMedical();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("sejour" , $sejour);
$smarty->assign("modeDAS", $modeDAS);

$smarty->display("inc_diagnostic_principal.tpl");

?>