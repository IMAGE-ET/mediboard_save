<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcim10
* @version $Revision$
* @author Romain Ollivier
*/

global $can;
$can->needsAdmin();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->display("configure.tpl");

?>