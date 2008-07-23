<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage bloodSalvage
 *  @version $Revision: $
 *  @author Alexandre Germonneau
 */

global $can, $m, $g;
CAppUI::requireModuleFile("bloodSalvage", "inc_personnel");

$selOp = new COperation;
$blood_salvage      = new CBloodSalvage();
$date               = mbGetValueFromGetOrSession("date", mbDate());
$op                 = mbGetValueFromGetOrSession("op");


if($op) {
	$selOp->load($op);
  $selOp->loadRefs();
  $where = array();
  $where["operation_id"] = "='$selOp->_id'";  
  $blood_salvage->loadObject($where);
}

$smarty = new CSmartyDP();

$smarty->assign("date", $date);
$smarty->assign("blood_salvage",$blood_salvage);
$smarty->assign("selOp",$selOp);

$smarty->display("vw_bloodSalvage_sspi.tpl");

?>