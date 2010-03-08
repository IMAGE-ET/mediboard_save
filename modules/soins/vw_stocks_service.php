<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage soins
 *	@version $Revision$
 *  @author SARL OpenXtrem
 *  @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

global $can, $g;
$can->needsEdit();

$service_id          = CValue::getOrSession('service_id');
$start               = CValue::getOrSession('start', 0);
$only_service_stocks = CValue::getOrSession('only_service_stocks', 1);
$only_common         = CValue::getOrSession('only_common', 1);
$endowment_id        = CValue::getOrSession('endowment_id');
$keywords            = CValue::getOrSession('keywords');

// Services list
$service = new CService();
$list_services = $service->loadGroupList();

$date = mbDate();
$delivrance = new CProductDelivery();
$date_min = CValue::getOrSession('_date_min', $date);
$date_max = CValue::getOrSession('_date_max', $date);

CValue::setSession('_date_min', $date_min);
CValue::setSession('_date_max', $date_max);

$delivrance->_date_min = $date_min;
$delivrance->_date_max = $date_max;

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign('service_id',    $service_id);
$smarty->assign('list_services', $list_services);
$smarty->assign('delivrance',    $delivrance);
$smarty->assign('start',         $start);
$smarty->assign('only_service_stocks', $only_service_stocks);
$smarty->assign('keywords',      $keywords);
$smarty->assign('only_common',   $only_common);
$smarty->assign('endowment_id',  $endowment_id);

$smarty->display('vw_stocks_service.tpl');
