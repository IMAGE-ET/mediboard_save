<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage bloodsalvage
* @version $Revision: $
* @author Alexandre Germonneau
*/

global $AppUI, $dPconfig, $can, $m, $tab;

$can->needsAdmin();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->display("configure.tpl");
?>