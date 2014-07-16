<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage soins
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

$group = CGroups::loadCurrent();

$service_id = CValue::getOrSession("service_id");
$real_time  = CValue::getOrSession("real_time", 0);

if($service_id == "NP"){
  $service_id = "";
}

$cond = array();

// Chargement du service
$service = new CService();
$service->load($service_id);

// Si le service en session n'est pas dans l'etablissement courant
if(CGroups::loadCurrent()->_id != $service->group_id){
  $service_id = "";
  $service = new CService();
}

$date = CValue::getOrSession("debut");
$prescription_id = CValue::get("prescription_id");

// Chargement des configs de services
if (!$service_id) {
  $service_id = "none";
}

$configs = CConfigService::getAllFor($service_id);

if (!$date) {
  $date = CMbDT::date();
}

$filter_line = new CPrescriptionLineMedicament();
$filter_line->debut = $date;

// R�cup�ration de la liste des services
$where = array();
$where["externe"]   = "= '0'";
$where["cancelled"] = "= '0'";
$service = new CService();
$services = $service->loadGroupList($where);

// Smarty template
$smarty = new CSmartyDP();
$smarty->assign("service"     , $service);
$smarty->assign("filter_line" , $filter_line);
$smarty->assign("services"    , $services);
$smarty->assign("service_id"  , $service_id);
$smarty->assign("date"        , $date);
$smarty->assign('day'         , CMbDT::date());
$smarty->assign('real_time'   , $real_time);
$smarty->assign("date_min"    , "");
$smarty->display('vw_pancarte_service.tpl');
