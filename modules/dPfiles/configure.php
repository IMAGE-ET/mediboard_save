<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision$
* @author S�bastien Fillonneau
*/

global $can;
$can->needsAdmin();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("listNbFiles"  , range(1,10));
$smarty->assign("today", mbDate());
$smarty->display("configure.tpl");

?>