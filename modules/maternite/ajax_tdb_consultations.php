<?php

/**
 * Liste des consultations du jour du tableau de bord
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

$consultation = new CConsultation();

$where = array();
$where["consultation.grossesse_id"]    = "IS NOT NULL";
$where["consultation.annule"]          = "= '0'";
$where["plageconsult.date"]            = "= '$date'";
$where["functions_mediboard.group_id"] = "= '$group->_id'";

$ljoin = array();
$ljoin["plageconsult"]        = "plageconsult.plageconsult_id = consultation.plageconsult_id";
$ljoin["users_mediboard"]     = "plageconsult.chir_id = users_mediboard.user_id";
$ljoin["functions_mediboard"] = "functions_mediboard.function_id = users_mediboard.function_id";

$curr_user = CMediusers::get();
if ($curr_user->isPraticien() || $curr_user->isSageFemme()) {
  $where["plageconsult.chir_id"] = "= '$curr_user->_id'";
}

/** @var CConsultation[] $listConsults */
$listConsults = $consultation->loadList($where, "heure ASC", null, null, $ljoin);

$plages = CStoredObject::massLoadFwdRef($listConsults, "plageconsult_id");
CStoredObject::massLoadFwdRef($plages, "chir_id");
CStoredObject::massLoadFwdRef($listConsults, "sejour_id");
$grossesses = CStoredObject::massLoadFwdRef($listConsults, "grossesse_id");
CStoredObject::massLoadFwdRef($grossesses, "parturiente_id");

foreach ($listConsults as $_consult) {
  $_consult->loadRefPraticien();
  $_consult->loadRefSejour()->loadRefGrossesse();
  $_consult->loadRefGrossesse()->loadRefParturiente();
}

$smarty = new CSmartyDP();

$smarty->assign("date"        , $date);
$smarty->assign("listConsults", $listConsults);

$smarty->display("inc_tdb_consultations.tpl");