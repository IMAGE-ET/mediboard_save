<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkAdmin();

$praticien_id = CValue::get("praticien_id");
$date         = CValue::get("date", CMbDT::date("+5 year"));
$limit        = CValue::get("limit", 100);

$plage = new CPlageconsult();
$plage->_spec->loggable = false;

$where = array();
if ($praticien_id) {
  $where["plageconsult.chir_id"] = "= '$praticien_id'";
}
$where["plageconsult.date"] = "> '$date'";

$count = $plage->countList($where);
CAppUI::setMsg("'$count' plages � supprimer apr�s '$date'", UI_MSG_OK);

/** @var CPlageconsult[] $listPlages */
$listPlages = $plage->loadList($where, null, $limit);

foreach ($listPlages as $_plage) {
  if ($msg = $_plage->delete()) {
    CAppUI::setMsg("Plage non supprim�e", UI_MSG_ERROR);
  }
  else {
    CAppUI::setMsg("Plage supprim�e", UI_MSG_OK);
  }
}

echo CAppUI::getMsg();