<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage sherpa
 * @version $Revision: $
 * @author Sherpa
 */

global $can, $AppUI;

$can->needsAdmin();

$AppUI->getAllClasses();
$spClasses = getChildClasses("CSpObject");

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("spClasses", $spClasses);
$smarty->display("configure.tpl");

?>