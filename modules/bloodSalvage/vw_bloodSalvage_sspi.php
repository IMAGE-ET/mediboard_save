<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage bloodSalvage
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CAppUI::requireModuleFile("bloodSalvage", "inc_personnel");

$selOp = new COperation;
$blood_salvage  = new CBloodSalvage();
$date           = CValue::getOrSession("date", CMbDT::date());
$op             = CValue::getOrSession("op");

if ($op) {
  $selOp->load($op);
  $selOp->loadRefs();
  $where = array();
  $where["operation_id"] = "='$selOp->_id'";  
  $blood_salvage->loadObject($where);
  $blood_salvage->loadRefsFwd();
  $blood_salvage->loadRefPlageOp();
}

$smarty = new CSmartyDP();

$smarty->assign("date",           $date);
$smarty->assign("blood_salvage",  $blood_salvage);
$smarty->assign("selOp",          $selOp);

$smarty->display("vw_bloodSalvage_sspi.tpl");
