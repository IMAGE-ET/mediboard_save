<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision$
* @author S�bastien Fillonneau
*/

global $can;
$can->needsAdmin();

$file = new CFile;

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("listNbFiles"  , range(1,10));
$smarty->assign("today", mbDate());
$smarty->assign("nb_files", $file->countList());
$smarty->display("configure.tpl");

?>