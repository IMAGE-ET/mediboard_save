<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $can, $m;

$can->needsAdmin();

$smarty = new CSmartyDP();
$smarty->display("vw_create_archive.tpl");
?>