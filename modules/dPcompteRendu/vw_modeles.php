<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Romain Ollivier
*/

CCanDo::checkRead();

$order_col = CValue::getOrSession("order_col", "object_class");
$order_way = CValue::getOrSession("order_way", "ASC");
$order = "";

switch ($order_col) {
  case "object_class":
    $order = "object_class $order_way, type, nom";
    break;
  case "nom":
    $order = "nom $order_way, object_class, type";
    break;
  case "type":
   $order = "type $order_way, object_class, nom";
}

// Liste des praticiens et cabinets accessibles
$user = CMediusers::get();
$praticiens = $user->loadUsers(PERM_EDIT);

// Filtres
$filtre = new CCompteRendu();
$filtre->user_id      = CValue::getOrSession("user_id", $user->_id);
$filtre->object_class = CValue::getOrSession("object_class");
$filtre->type         = CValue::getOrSession("type");

// Praticien
$user = new CMediusers;
$user->load($filtre->user_id);
if ($user->isPraticien()) {
  CValue::setSession("prat_id", $user->_id);
}

$modeles = CCompteRendu::loadAllModelesFor($filtre->user_id, 'prat', $filtre->object_class, $filtre->type, 1, $order);
$owners = $user->getOwners();

// On ne met que les classes qui ont une methode filTemplate
$filtre->_specs['object_class']->_locales = CCompteRendu::getTemplatedClasses();

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("user"      , $user);
$smarty->assign("filtre"    , $filtre);
$smarty->assign("praticiens", $praticiens);
$smarty->assign("modeles"   , $modeles);
$smarty->assign("owners"    , $owners);
$smarty->assign("order_way" , $order_way);
$smarty->assign("order_col" , $order_col);
$smarty->display("vw_modeles.tpl");

?>