<?php /* $Id: $ */

/**
 * @package Mediboard
 * @subpackage dPmedicament
 * @version $Revision: $
 * @author Alexis Granger
 */

global $can;

$can->needsAdmin();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->display("configure.tpl");

?>