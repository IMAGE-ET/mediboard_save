<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage dPstats
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$debutact      = CValue::getOrSession("debutact", CMbDT::date());
$finact        = CValue::getOrSession("finact", CMbDT::date());

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("debutact", $debutact);
$smarty->assign("finact"  , $finact);

$smarty->display("vw_activite.tpl");
