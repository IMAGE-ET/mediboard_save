<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author S�bastien Fillonneau
*/

CCanDo::checkAdmin();

$hours = range(0, 23);
$intervals = array("5","10","15","20","30");
$patient_ids = array("0", "1", "2");

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("hours"      , $hours);
$smarty->assign("intervals"   , $intervals);
$smarty->assign("patient_ids"  , $patient_ids);

$smarty->display("configure.tpl");
?>