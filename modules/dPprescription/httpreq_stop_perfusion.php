<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage dPprescription
 *	@version $Revision: $
 *  @author Alexis Granger
 */

global $AppUI, $can, $m;

$perfusion_id = mbGetValueFromGet("perfusion_id");

// Chargement de la perfusion
$perfusion = new CPerfusion();
$perfusion->load($perfusion_id);

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("perfusion", $perfusion);
$smarty->display("inc_vw_stop_perf.tpl");

?>