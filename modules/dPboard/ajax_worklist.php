<?php

/**
 * dPboard
 *
 * @category Board
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  SVN: $Id:$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();
$ds = CSQLDataSource::get("std");
// R�cup�ration des param�tres
$chirSel   = CValue::getOrSession("chirSel");
$date      = CValue::getOrSession("date", CMbDT::date());

// Cr�ation du template
$smarty = new CSmartyDP();

$account = new CSourcePOP();
$account->object_class = "CMediusers";
$account->object_id = $chirSel;
$account->loadMatchingObject();

$smarty->assign("account", $account);
$smarty->assign("date"   , $date);
$smarty->assign("chirSel", $chirSel);

$smarty->display("inc_worklist.tpl");
