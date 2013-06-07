<?php

/**
 * Color selector
 *
 * @category Mediusers
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkAdmin();

$mediuser = new CMediusers();
$mediusers = $mediuser->loadGroupList();
foreach ($mediusers as $mediuser) {
  $mediuser->insFunctionPermission();
  $mediuser->insGroupPermission();
}

CAppUI::stepAjax(count($mediusers)." utilisateurs v�rifi�s");
