<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision$
* @author Fabien M�nager
*/

global $can;
$can->needsAdmin();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->display('configure.tpl');

?>