<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$can->needsRead();

$debutact      = mbGetValueFromGetOrSession("debutact", mbDate("-1 YEAR"));
$rectif        = mbTranformTime("+0 DAY", $debutact, "%d")-1;
$debutact      = mbDate("-$rectif DAYS", $debutact);
$finact        = mbGetValueFromGetOrSession("finact", mbDate());
$rectif        = mbTranformTime("+0 DAY", $finact, "%d")-1;
$finact        = mbDate("-$rectif DAYS", $finact);
$finact        = mbDate("+ 1 MONTH", $finact);
$finact        = mbDate("-1 DAY", $finact);
$prat_id       = mbGetValueFromGetOrSession("prat_id", 0);
$service_id    = mbGetValueFromGetOrSession("service_id", 0);
$discipline_id = mbGetValueFromGetOrSession("discipline_id", 0);
$type_adm      = mbGetValueFromGetOrSession("type_adm", 1);

$user = new CMediusers;
$listPrats = $user->loadPraticiens(PERM_READ);

$listServices = new CService;
$listServices = $listServices->loadList();

$listDisciplines = new CDiscipline();
$listDisciplines = $listDisciplines->loadUsedDisciplines();

$sejour = new CSejour;
$listHospis = array();
$listHospis = array_merge($listHospis,$sejour->_enumsTrans["type"]);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("debutact"       , $debutact);
$smarty->assign("finact"         , $finact);
$smarty->assign("prat_id"        , $prat_id);
$smarty->assign("service_id"     , $service_id);
$smarty->assign("discipline_id"  , $discipline_id);
$smarty->assign("type_adm"       , $type_adm);
$smarty->assign("listPrats"      , $listPrats);
$smarty->assign("listServices"   , $listServices);
$smarty->assign("listDisciplines", $listDisciplines);
$smarty->assign("listHospis"     , $listHospis);

$smarty->display("vw_hospitalisation.tpl");

?>