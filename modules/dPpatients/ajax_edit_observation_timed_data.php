<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$object_guid   = CValue::get("object_guid");
$timed_data_id = CValue::get("timed_data_id");

/** @var COperation|CSejour $object */
$object = CStoredObject::loadFromGuid($object_guid);

$timed_data = new CSupervisionTimedData();
$timed_data->load($timed_data_id);

$result = new CObservationResult();
$result->value_type_id = $timed_data->value_type_id;
$result->loadRefValueType();

$result_set = new CObservationResultSet();
$result_set->context_class = $object->_class;
$result_set->context_id = $object->_id;
$result_set->datetime = CMbDT::dateTime();
$result_set->patient_id = $object->loadRelPatient()->_id;

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("result", $result);
$smarty->assign("result_set", $result_set);
$smarty->assign("timed_data", $timed_data);

$smarty->display("inc_edit_observation_timed_data.tpl");
