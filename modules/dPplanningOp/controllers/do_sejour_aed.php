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

// Pour un s�jour ayant comme mode de sortie urgence:
if (CValue::post("mode_sortie") == "mutation" && CValue::post("type") == "urg" && ($lit_id || $service_sortie_id)) {
  $sejour_id = CValue::post("sejour_id");

  $sejour = new CSejour();
  $sejour->load($sejour_id);
  $rpu = $sejour->loadRefRPU();

  if ($rpu->mutation_sejour_id) {
    $sejour_id = $sejour->_ref_rpu->mutation_sejour_id;
  }

  $sejour_hospit = new CSejour();
  $sejour_hospit->load($sejour_id);

  // Cr�ation de l'affectation d'hospitalisation
  $affectation_hospit = new CAffectation();
  $affectation_hospit->entree     = CMbDT::dateTime();
  $affectation_hospit->lit_id     = $lit_id;
  $affectation_hospit->service_id = $service_sortie_id;

  $sejour_hospit->forceAffectation($affectation_hospit);
}

$do = new CDoObjectAddEdit("CSejour");
$do->doIt();