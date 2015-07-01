<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

$sejour_id      = CValue::get("sejour_id");
$element_id     = CValue::get("_element_id");
$_days          = CValue::get("_days", array());
$_sejours_guids = CValue::get("_sejours_guids");
$_sejours_guids = json_decode(utf8_encode(stripslashes($_sejours_guids)), true);
$date       = CValue::getOrSession("date", CMbDT::date());
$monday     = CMbDT::date("last monday", CMbDT::date("+1 day", $date));

$days = array();
foreach ($_days as $_number) {
  $days[] = CMbDT::date("+$_number DAYS", $monday);
}
if (!count($_days)) {
  $date = !count($_days) ? $date : reset($days);
}

$ljoin = array();
$ljoin["prescription"] = "sejour.sejour_id = prescription.object_id AND prescription.object_class = 'CSejour'";
$ljoin["prescription_line_element"] = "prescription_line_element.prescription_id = prescription.prescription_id";

$where = array();
$where["prescription.prescription_id"] = "IS NOT NULL";
$where["prescription_line_element.element_prescription_id"] = " = '$element_id'";
$where["sejour.type"]            = "= 'ssr'";
$where["sejour.sejour_id"]       = " <> '$sejour_id'";
$where["sejour.annule"]       = " = '0'";
$sejours = CSejour::loadListForDate($date, $where, "entree", null, "sejour_id", $ljoin);

foreach ($sejours as $_sejour) {
  /* @var CSejour $_sejour*/
  $patient = $_sejour->loadRefPatient();
  $patient->loadIPP();
  $_sejour->loadRefPraticien();
  $bilan = $_sejour->loadRefBilanSSR();
  $bilan->loadRefPraticienDemandeur();
  $bilan->loadRefKineReferent();

  // Détail du séjour
  $_sejour->checkDaysRelative($date);
  $_sejour->loadNDA();
  $_sejour->loadRefsNotes();
  // Chargement du lit
  $_sejour->loadRefCurrAffectation()->loadRefLit();
}

$element = new CElementPrescription();
$element->load($element_id);

$smarty = new CSmartyDP();

$smarty->assign("sejours"       , $sejours);
$smarty->assign("date"          , $date);
$smarty->assign("_sejours_guids", $_sejours_guids);
$smarty->assign("element"       , $element);

$smarty->display("vw_patients_seance_collective.tpl");
