<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPsalleOp
* @version $Revision$
* @author Alexis Granger
*/

CCanDo::checkRead();

$operation_id = CValue::getOrSession("operation_id");
$date         = CValue::getOrSession("date", mbDate());
$modif_operation = CCanDo::edit() || $date >= mbDate();

$operation = new COperation();
$prescription = new CPrescription();
$protocoles = array();
$anesth_id = "";

if($operation_id){
  $operation->load($operation_id);
  $operation->loadRefs();
}

// Chargement des praticiens
$listAnesths = new CMediusers;
$listAnesths = $listAnesths->loadAnesthesistes(PERM_DENY);

$listChirs = new CMediusers;
$listChirs = $listChirs->loadPraticiens(PERM_READ);

$listAnesthType = new CTypeAnesth;
$orderanesth = "name";
$listAnesthType = $listAnesthType->loadList(null,$orderanesth);

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("listAnesthType"  , $listAnesthType  );
$smarty->assign("listAnesths"     , $listAnesths     );
$smarty->assign("listChirs"       , $listChirs       );
$smarty->assign("selOp"           , $operation       );
$smarty->assign("date"            , $date            );
$smarty->assign("modif_operation" , $modif_operation );
$smarty->assign("anesth_id"       , $anesth_id);
$smarty->display("inc_vw_anesth.tpl");

?>