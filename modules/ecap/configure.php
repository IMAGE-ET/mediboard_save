<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision$
 * @author Thomas Despoix
 */

global $can;
$can->needsAdmin();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("paths", CMedicap::$paths);
$smarty->display("configure.tpl");

?>