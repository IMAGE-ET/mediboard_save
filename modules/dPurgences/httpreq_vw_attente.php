<?php

/**
* @package Mediboard
* @subpackage dPurgences
* @version $Revision: $
* @author Alexis Granger
*/

// R�cuperation du sejour_id
$sejour_id = mbGetValueFromGetOrSession("sejour_id");

// Chargement du sejour
$sejour = new CSejour();
$sejour->load($sejour_id);
$sejour->loadRefRPU();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("sejour", $sejour);

$smarty->display("inc_vw_attente.tpl");


?>