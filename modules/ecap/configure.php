<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage ecap
 * @version $Revision$
 * @author Thomas Despoix
 */

global $can;
$can->needsAdmin();

CMedicap::makeTags();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("paths", CMedicap::$paths);
$smarty->assign("tags", CMedicap::$tags);
$smarty->display("configure.tpl");

?>