<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision$
* @author S�bastien Fillonneau
*/

global $can;
$can->needsAdmin();

$listNbFiles = range(1,5);

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("listNbFiles"  , $listNbFiles);
$smarty->display("configure.tpl");

?>