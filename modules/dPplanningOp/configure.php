<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $dPconfig, $can, $m, $tab;

$can->needsAdmin();

$listHours = range(0, 23);
$listInterval = array("5","10","15","20","30");

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("listHours"    , $listHours);
$smarty->assign("listInterval" , $listInterval);

$smarty->display("configure.tpl");
?>