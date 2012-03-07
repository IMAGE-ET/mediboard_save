<?php

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision:
* @author Romain Ollivier
*/

CCanDo::checkRead();

// Current user
$mediuser = new CMediusers;
$mediuser->load(CAppUI::$instance->user_id);

// Filter
$filter = new CPlageconsult();
$filter->_date_min          = mbDate("last month");
$filter->_date_max          = mbDate();

$functions = CMediusers::loadFonctions(PERM_EDIT, null, "cabinet");
$users = $mediuser->loadPraticiens();

$smarty = new CSmartyDP();

$smarty->assign("filter"   , $filter);
$smarty->assign("users", $users);
$smarty->assign("functions", $functions);

$smarty->display("vw_stats.tpl");
?>