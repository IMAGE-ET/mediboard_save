<?php

/**
 * Liste des hospitalisations en cours du tableau de bord
 *
 * @category Maternite
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

$date  = CValue::get("date", CMbDT::date());
$group = CGroups::loadCurrent();

$sejour = new CSejour();
$where = array();
$where["sejour.grossesse_id"] = "IS NOT NULL";
$where["sejour.entree"] = "<= '$date 23:59:59' ";
$where["sejour.sortie"] = ">= '$date 00:00:00' ";
$where["sejour.group_id"] = " = '$group->_id' ";
$where[] = "sejour.sortie_reelle > '$date " . CMbDT::time() ."' OR sejour.sortie_reelle IS NULL";
$order = "sejour.entree DESC";

/** @var CSejour[] $listSejours */
$listSejours = $sejour->loadList($where, $order, null, null, null);

$grossesses = CStoredObject::massLoadFwdRef($listSejours, "grossesse_id");
CStoredObject::massLoadFwdRef($grossesses, "parturiente_id");
$naissances = CStoredObject::massLoadBackRefs($grossesses, "naissances");
$sejours_enfant = CStoredObject::massLoadFwdRef($naissances, "sejour_enfant_id");
CStoredObject::massLoadFwdRef($sejours_enfant, "patient_id");

foreach ($listSejours as $_sejour) {
  $grossesse = $_sejour->loadRefGrossesse();
  $grossesse->loadRefParturiente();
  $naissances = $grossesse->loadRefsNaissances();
  $grossesse->_ref_sejour = $_sejour;
  $grossesse->loadRefLastOperation();
  foreach ($naissances as $_naissance) {
    $_naissance->loadRefSejourEnfant()->loadRefPatient();
    $_naissance->loadRefOperation();
  }
  $_sejour->loadRefCurrAffectation($date . " " . CMbDT::time());
  $grossesse->getDateAccouchement();
}

$smarty = new CSmartyDP();

$smarty->assign("date"       , $date);
$smarty->assign("listSejours", $listSejours);

$smarty->display("inc_tdb_hospitalisations.tpl");