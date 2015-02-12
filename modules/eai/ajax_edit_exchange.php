<?php 

/**
 * $Id$
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$exchange_guid = CValue::get("exchange_guid");

if (!$exchange_guid) {
  CAppUI::displayAjaxMsg("Pas d'objet pass� en param�tre");
  CApp::rip();
}

$exchange = CMbObject::loadFromGuid($exchange_guid);
$exchange->loadRefsNotes();

if (!$exchange || $exchange && !$exchange->_id) {
  CAppUI::displayAjaxMsg("Aucun �change trouv�");
  CApp::rip();
}

$smarty = new CSmartyDP();
$smarty->assign("exchange", $exchange);
$smarty->display("inc_edit_exchange.tpl");