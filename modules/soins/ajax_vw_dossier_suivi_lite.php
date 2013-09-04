<?php

/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage soins
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */


$sejour_id = CValue::get("sejour_id");

// Chargement des observations d'importance haute de moins de 7 jours
$observation = new CObservationMedicale();
$where = array();
$where["degre"] = " = 'high'";
$where["sejour_id"] = " = '$sejour_id'";
$where["date"] = " >= '".CMbDT::dateTime("- 7 DAYS")."'";
$observations = $observation->loadList($where);

// Chargement des transmissions d'importance haute ou des macrocibles de moins de 7 jours
$transmission = new CTransmissionMedicale();
$where = array();
$where["date"] = " >= '".CMbDT::dateTime("- 7 DAYS")."'";
$where["sejour_id"] = " = '$sejour_id'";
$where[] = "degre = 'high' OR category_prescription.cible_importante = '1'";

$ljoin = array();
$ljoin["category_prescription"] = "transmission_medicale.object_id = category_prescription.category_prescription_id
                                  AND transmission_medicale.object_class = 'CCategoryPrescription'";

$transmissions = $transmission->loadList($where, null, null, null, $ljoin);

foreach ($observations as $_observation) {
  $_observation->loadRefsFwd();
  $_observation->_ref_user->loadRefFunction();
  $suivi[$_observation->date.$_observation->_id] = $_observation;
}

foreach ($transmissions as $_transmission) {
  $_transmission->loadRefsFwd();
  $_transmission->_ref_user->loadRefFunction();
  $suivi[$_transmission->date.$_transmission->_guid] = $_transmission;
}

krsort($suivi);

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("suivi", $suivi);
$smarty->assign("readonly", true);
$smarty->display("inc_vw_dossier_suivi_lite.tpl");