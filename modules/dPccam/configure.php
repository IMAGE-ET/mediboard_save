<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPccam
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

// Cr�ation du template
$smarty = new CSmartyDP(1);

$smarty->display("configure.tpl");

?>