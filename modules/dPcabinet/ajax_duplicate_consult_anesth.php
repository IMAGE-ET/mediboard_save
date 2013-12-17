<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

CCanDo::checkEdit();
$consult_id = CValue::getOrSession("consult_id");
$operation_id = CValue::getOrSession("operation_id");
$consult_anesth_id = CValue::getOrSession("consult_anesth_id");

$consult = new CConsultation();
$consult->load($consult_id);

$operation = new COperation();
$operation->load($operation_id);
$operation->loadRefPlageOp();
$operation->loadRefPraticien()->loadRefFunction();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("consult"   , $consult);
$smarty->assign("operation" , $operation);
$smarty->assign("consult_anesth_id" , $consult_anesth_id);

$smarty->display("inc_consult_anesth/inc_duplicate_consult_anesth.tpl");
