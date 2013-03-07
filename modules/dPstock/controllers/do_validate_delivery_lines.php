<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage {subpackage}
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

$list = explode("-", CValue::post("list", ""));
CMbArray::removeValue("", $list);

foreach($list as $_id) {
  $delivery = new CProductDelivery;
  $delivery->load($_id);
  $delivery->date_delivery = CMbDT::dateTime();
  if ($msg = $delivery->store()) {
    CAppUI::setMsg($msg, UI_MSG_WARNING);
  }
  else {
    CAppUI::setMsg("R�ception termin�e");
  }
}

echo CAppUI::getMsg();
CApp::rip();
