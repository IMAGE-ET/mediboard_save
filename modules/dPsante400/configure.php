<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
* @author Thomas Despoix
*/

global $AppUI, $can, $m;

$can->needsAdmin();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->display("configure.tpl");

?>