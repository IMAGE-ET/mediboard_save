<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PMSI
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();
$group = CGroups::loadCurrent();

$date = CValue::getOrSession("date", CMbDT::date());
$next = CMbDT::date("+1 day", $date);

$sejour = new CSejour;
$where = array();
$where["entree"] = "< '$next'";
$where["sortie"] = "> '$date'";
$where["group_id"]      = "= '$group->_id'";
$where["annule"]        = "= '0'";
$order = array();
$order[] = "sortie";
$order[] = "entree";

/** @var CSejour[] $listSejours */
$listSejours = $sejour->loadList($where, $order);

foreach ($listSejours as $_sejour) {
  $_sejour->loadRefsFwd();
  $_sejour->loadNDA();
  $_sejour->_ref_patient->loadIPP();
  $_sejour->countActes();
  $_sejour->countExchanges();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("date"       , $date       );
$smarty->assign("listSejours", $listSejours);

$smarty->display("vw_list_hospi.tpl");
