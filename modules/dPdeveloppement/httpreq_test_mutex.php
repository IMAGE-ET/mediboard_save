<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage developpement
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$action = CValue::get("action");

$duration = 10;

// Remove session lock
CSessionHandler::writeClose();

CAppUI::stepAjax("test_mutex-try", UI_MSG_OK, $action);

$mutex = new CMbMutex("test");

switch ($action) {
  case "stall":
    CAppUI::stepAjax("test_mutex-acquired", UI_MSG_OK, $mutex->acquire($duration));
    sleep(5);
    $mutex->release();
    break;

  case "die": 
    CAppUI::stepAjax("test_mutex-acquired", UI_MSG_OK, $mutex->acquire($duration));
    sleep(5);
    CApp::rip();
    break;

  case "run":
    CAppUI::stepAjax("test_mutex-acquired", UI_MSG_OK, $mutex->acquire($duration));
    $mutex->release();
    break;

  case "dummy":
    break;
  
  default:
    CAppUI::stepAjax("test_mutex-fail", UI_MSG_WARNING, $action);
    return;
}
