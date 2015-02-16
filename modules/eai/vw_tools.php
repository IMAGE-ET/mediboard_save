<?php 
/**
 * View tools EAI
 *  
 * @category EAI
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:$ 
 * @link     http://www.mediboard.org
 */
 
CCanDo::checkAdmin();

$date_min = CValue::getOrSession('date_min', CMbDT::dateTime("-7 day"));
$date_max = CValue::getOrSession('date_max', CMbDT::dateTime("+1 day"));

$exchanges_classes = array();
foreach (CExchangeDataFormat::getAll() as $key => $_exchange_class) {  
  foreach (CApp::getChildClasses($_exchange_class, true) as $under_key => $_under_class) {
    $class = new $_under_class;
    $class->countExchanges();
    $exchanges_classes[$_exchange_class][] = $class;
  }
  if ($_exchange_class == "CExchangeAny") {
    $class = new CExchangeAny();
    $class->countExchanges();
    $exchanges_classes["CExchangeAny"][] = $class;
  }
}

$group = new CGroups();
$groups = $group->loadList();
foreach ($groups as $_group) {
  $_group->loadConfigValues(); 
}

$receiver = new CInteropReceiver(); 
$receivers = $receiver->getObjects();

$tools = array(
  "exchanges" => array(
    "send",
    "inject_master_idex_missing",
    "reprocessing",
    "detect_collision",
  ),
  "smp" => array(
    "resend_exchange",
  ),
);

$exchange = new CExchangeDataFormat();
$exchange->_date_min = $date_min;
$exchange->_date_max = $date_max;

$movement = new CMovement();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("exchange"         , $exchange);
$smarty->assign("exchanges_classes", $exchanges_classes);
$smarty->assign("groups"           , $groups);
$smarty->assign("tools"            , $tools);
$smarty->assign("receivers"        , $receivers);
$smarty->assign("movement"         , $movement);
$smarty->display("vw_tools.tpl");

