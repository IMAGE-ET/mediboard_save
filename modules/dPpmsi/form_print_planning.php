<?php /* $Id: */

/**
* @package Mediboard
* @subpackage dPpmsi
* @version $Revision$
* @author Romain Ollivier
*/

CCanDO::checkRead();

$user = CUser::get();

$now = mbDate();

$filter = new COperation;
$filter->_date_min     = CValue::get("_date_min", $now);
$filter->_date_max     = CValue::get("_date_max", $now);
$filter->_prat_id      = CValue::getOrSession("_prat_id");
$filter->salle_id      = CValue::getOrSession("salle_id");
$filter->_plage        = CValue::getOrSession("_plage");
$filter->_intervention = CValue::getOrSession("_intervention");
$filter->_specialite   = CValue::getOrSession("_specialite");
$filter->_codes_ccam   = CValue::getOrSession("_codes_ccam");
$filter->_ccam_libelle = CValue::getOrSession("_ccam_libelle");

$filterSejour = new CSejour;
$filterSejour->type = CValue::getOrSession("type");

$yesterday  = mbDate("-1 day", $now);

$mediuser = new CMediusers();
$listPrat = $mediuser->loadPraticiens(PERM_READ);

$function = new CFunctions();
$listSpec = $function->loadSpecialites(PERM_READ);

// R�cup�ration des salles
$listBlocs = CGroups::loadCurrent()->loadBlocs(PERM_EDIT);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("chir"         , $user->_id);
$smarty->assign("filter"       , $filter);
$smarty->assign("filterSejour" , $filterSejour);
$smarty->assign("now"          , $now);
$smarty->assign("yesterday"    , $yesterday);
$smarty->assign("listPrat"     , $listPrat);
$smarty->assign("listSpec"     , $listSpec);
$smarty->assign("listBlocs"    , $listBlocs);

$smarty->display("form_print_planning.tpl");

?>