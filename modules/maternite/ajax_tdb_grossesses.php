<?php

/**
 * Liste des grossesses en cours du tableau de bord
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

$date_min = CMbDT::date("-".CAppUI::conf("maternite CGrossesse min_check_terme", $group)." DAYS", $date);
$date_max = CMbDT::date("+".CAppUI::conf("maternite CGrossesse max_check_terme", $group)." DAYS", $date);

$where = array();
$ljoin = array();
$where["grossesse.terme_prevu"] = "BETWEEN '$date_min' AND '$date_max'";
$where["grossesse.group_id"] = "= '$group->_id' ";
$where["grossesse.active"] = "= '1'";
$ljoin["patients"]    = "patients.patient_id = grossesse.parturiente_id";

$grossesse = new CGrossesse();
/** @var CStoredObject[] $grossesses */
$grossesses = $grossesse->loadList($where, "terme_prevu ASC, nom ASC", null, null, $ljoin);

CStoredObject::massLoadFwdRef($grossesses, "parturiente_id");
CStoredObject::massCountBackRefs($grossesses, "sejours");
$consultations = CStoredObject::massLoadBackRefs($grossesses, "consultations");
CStoredObject::massLoadFwdRef($consultations, "plageconsult_id");

/** @var CGrossesse[] $grossesses */
foreach ($grossesses as $_grossesse) {
  $_grossesse->loadRefParturiente();
  $_grossesse->countRefSejours();
  $_grossesse->loadRefsConsultations(true);
}

$smarty = new CSmartyDP();

$smarty->assign("grossesses", $grossesses);
$smarty->assign("date"      , $date);
$smarty->assign("date_min"  , $date_min);
$smarty->assign("date_max"  , $date_max);

$smarty->display("inc_tdb_grossesses.tpl");