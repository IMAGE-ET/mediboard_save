<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

$code = CValue::get("code");
$activite = CActiviteCdARR::get($code);
if (!$activite->code) {
  CAppUI::stepMessage(UI_MSG_ERROR, "Activit� CdARR '$code' non trouv�e");
  return;
}

$activite->loadRefsElementsByCat();
$activite->loadRefsAllExecutants();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("activite", $activite);

$smarty->display("vw_activite_srr_stats.tpl");
