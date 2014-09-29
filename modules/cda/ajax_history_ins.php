<?php 

/**
 * $Id$
 *  
 * @category CDA
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

$patient_id = CValue::get("patient_id");

$ins_patient = new CINSPatient();
$ins_patient->patient_id = $patient_id;
$list_ins = $ins_patient->loadMatchingList("date desc", null, "ins_patient_id");


$smarty = new CSmartyDP();
$smarty->assign("list_ins", $list_ins);
$smarty->display("inc_history_ins.tpl");