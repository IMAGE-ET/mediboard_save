<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage admin
* @version $Revision$
* @author Fabien M�nager
*/

global $can;
$can->needsAdmin();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->display("configure.tpl");

?>