<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

if ($praticien_id = CValue::post("praticien_id")) {
  CValue::setSession("praticien_id", $praticien_id);
}
$lit_id            = CValue::post("lit_id");
$service_sortie_id = CValue::post("service_sortie_id");

//Pour un s�jour ayant comme mode de sortie urgence:
if (CValue::post("mode_sortie") == "mutation" && CValue::post("type") == "urg" && ($lit_id || $service_sortie_id)) {
  $sejour_id = CValue::post("sejour_id");

  $sejour = new CSejour();
  $sejour->load($sejour_id);

  //Cr�ation de l'affectation du patient
  $affectation = new CAffectation();
  $affectation->entree     = CMbDT::dateTime();
  $affectation->lit_id     = $lit_id;
  $affectation->service_id = $service_sortie_id;
  $affectation->sejour_id  = $sejour_id;
  if ($sejour->loadRefRPU()->mutation_sejour_id) {
    $affectation->sejour_id = $sejour->_ref_rpu->mutation_sejour_id;
  }
  $affectation->sortie = $sejour->sortie_prevue;
  if ($msg = $affectation->store()) {
    CAppUI::stepAjax($msg, UI_MSG_WARNING);
  }
}

$do = new CDoObjectAddEdit("CSejour");
$do->doIt();