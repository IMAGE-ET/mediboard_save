<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Thomas Despoix
*/

global $can;
$can->needsAdmin();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->display("configure.tpl");

?>