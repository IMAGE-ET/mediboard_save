<?php 

/**
 * V�rification d'activit�s 4 sur les actes pour la suppression d'actes non cot�s avant envoi
 *
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$object_id    = CValue::get("object_id");
$object_class = CValue::get("object_class");

/** @var CCodable $object */
$object = new $object_class;
$object->load($object_id);

$actes = explode("|", $object->codes_ccam);
$object->loadRefsActesCCAM();

$activites = CMbArray::pluck($object->_ref_actes_ccam, "code_activite");

$activite_1 = array_search("1", $activites);
$activite_4 = array_search("4", $activites);

$completed_activite_1 = 1;
$completed_activite_4 = 1;

foreach ($actes as $_acte) {
  $acte = CDatedCodeCCAM::get($_acte);

  if (isset($acte->activites["1"]) && $activite_1 === false) {
    $completed_activite_1 = 0;
  }
  if (isset($acte->activites["4"]) && $activite_4 === false) {
    $completed_activite_4 = 0;
    break;
  }
}

$response = array(
  "activite_1" => $completed_activite_1,
  "activite_4" => $completed_activite_4,
);

echo json_encode($response);