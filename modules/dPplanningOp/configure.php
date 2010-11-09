<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author S�bastien Fillonneau
*/

global $can;
$can->needsAdmin();

$listHours = range(0, 23);
$listInterval = array("5","10","15","20","30");

$listPatientId = array("0", "1", "2");

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("listHours"      , $listHours);
$smarty->assign("hours_piped"    , implode("|", $listHours));
$smarty->assign("listInterval"   , $listInterval);
$smarty->assign("intervals_piped", implode("|", $listInterval));
$smarty->assign("listPatientId"  , $listPatientId);
$smarty->assign("patientId_piped", implode("|", $listPatientId));
$smarty->display("configure.tpl");
?>