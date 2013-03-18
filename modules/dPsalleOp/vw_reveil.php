<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkRead();

$date    = CValue::getOrSession("date", CMbDT::date());
$bloc_id = CValue::getOrSession("bloc_id");

$modif_operation = CCanDo::edit() || $date >= CMbDT::date();
$hour = CMbDT::time();
$blocs_list = CGroups::loadCurrent()->loadBlocs();

$bloc = new CBlocOperatoire();
if (!$bloc->load($bloc_id) && count($blocs_list)) {
  $bloc = reset($blocs_list);
}

// Chargement de la liste du personnel pour le reveil
$personnels = array();
if (CModule::getActive("dPpersonnel")) {
  $personnel  = new CPersonnel();
  $personnels = $personnel->loadListPers("reveil");
}

// Vérification de la check list journalière
$daily_check_lists = array();
$daily_check_list_types = array();
$require_check_list = CAppUI::conf("dPsalleOp CDailyCheckList active_salle_reveil") && $date >= CMbDT::date();

if ($require_check_list) {
  $daily_check_list_type = new CDailyCheckListType();
  $where = array(
    "object_class" => "= 'CBlocOperatoire'",
    "object_id IS NULL OR object_id = '$bloc->_id'",
  );
  /** @var CDailyCheckListType[] $daily_check_list_types  */
  $daily_check_list_types = $daily_check_list_type->loadList($where, "title");

  /** @var CDailyCheckList[] $daily_check_lists  */
  $daily_check_lists = array();

  $check_list_not_validated = 0;
  foreach ($daily_check_list_types as $_list_type) {
    $_list_type->loadRefsCategories();
    $daily_check_list = CDailyCheckList::getList($bloc, $date, null, $_list_type->_id);
    $daily_check_list->loadItemTypes();
    $daily_check_list->loadBackRefs('items');

    if (!$daily_check_list->_id || !$daily_check_list->validator_id) {
      $check_list_not_validated++;
    }

    $daily_check_lists[] = $daily_check_list;
  }

  if ($check_list_not_validated == 0) {
    $require_check_list = false;
  }
}

// Création du template
$smarty = new CSmartyDP();

// Daily check lists
$smarty->assign("require_check_list"    , $require_check_list);
$smarty->assign("daily_check_lists"     , $daily_check_lists);
$smarty->assign("daily_check_list_types", $daily_check_list_types);

$smarty->assign("personnels"           , $personnels);
$smarty->assign("date"                 , $date);
$smarty->assign("hour"                 , $hour);
$smarty->assign("modif_operation"      , $modif_operation);
$smarty->assign("blocs_list"           , $blocs_list);
$smarty->assign("bloc"                 , $bloc);
$smarty->assign("isImedsInstalled"     , (CModule::getActive("dPImeds") && CImeds::getTagCIDC(CGroups::loadCurrent())));

$smarty->display("vw_reveil.tpl");
