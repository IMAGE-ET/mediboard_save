<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision: $
* @author Thomas Despoix
*/

global $AppUI, $canRead, $canEdit, $m;

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->display("configure.tpl");

?>