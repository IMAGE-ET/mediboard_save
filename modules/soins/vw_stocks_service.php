<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage soins
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $m;

CCanDo::checkEdit();

$service_id   = CValue::getOrSession('service_id');
$endowment_id = CValue::getOrSession('endowment_id');

// Services list
$service = new CService();
$where = array();
$where["group_id"]  = "= '".CGroups::loadCurrent()->_id."'";
$where["cancelled"] = "= '0'";
$list_services = $service->loadListWithPerms(PERM_READ, $where);

if ($m == "dPurgences") {
  foreach($list_services as $_id => $_service) {
    if (!$_service->urgence) {
      unset($list_services[$_id]);
    }
  } 
}

if (count($list_services) == 0) {
  CAppUI::stepMessage(UI_MSG_WARNING, "Vous n'avez acc�s � aucun service pour effectuer des commandes");
  return;
}

// Cr�ation du template
$smarty = new CSmartyDP("modules/soins");

$smarty->assign('service_id',    $service_id);
$smarty->assign('list_services', $list_services);
$smarty->assign('endowment_id',  $endowment_id);

$smarty->display('vw_stocks_service.tpl');
