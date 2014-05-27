<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PlanningOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

global $m, $tab;

CCanDo::checkEdit();

$protocole_id = CValue::getOrSession("protocole_id", 0);
$mediuser     = CMediusers::get();
$is_praticien = $mediuser->isPraticien();
$chir_id      = CValue::getOrSession("chir_id", $is_praticien ? $mediuser->user_id : null);

// Chargement du praticien
$chir = new CMediusers();
if ($chir_id) {
  $chir->load($chir_id);
}

// V�rification des droits sur les listes
$listPraticiens = $mediuser->loadPraticiens(PERM_EDIT);
$function = new CFunctions();
$listFunctions  = $function->loadSpecialites(PERM_EDIT);

$protocole = new CProtocole();
if ($protocole_id) {
  $protocole->load($protocole_id);
  // On v�rifie que l'utilisateur a les droits sur le protocole
  if (!$protocole->getPerm(PERM_EDIT)) {
    CAppUI::setMsg("Vous n'avez pas acc�s � ce protocole", UI_MSG_WARNING);
    CAppUI::redirect("m=$m&tab=$tab&protocole_id=0"); 
  }
  $protocole->loadRefs();
  $protocole->loadRefsNotes();
  $protocole->loadRefPrescriptionChir();
  $chir =& $protocole->_ref_chir;
}
else {
  $protocole->temp_operation = '00:00:00';
}

// Dur�e d'une intervention
$start = CAppUI::conf("dPplanningOp COperation duree_deb");
$stop  = CAppUI::conf("dPplanningOp COperation duree_fin");
$step  = CAppUI::conf("dPplanningOp COperation min_intervalle");

// R�cup�ration des services
$service = new CService();
$where = array();
$where["group_id"]  = "= '".CGroups::loadCurrent()->_id."'";
$where["cancelled"] = "= '0'";
$order = "nom";
$listServices = $service->loadListWithPerms(PERM_READ,$where, $order);

// Liste des types d'anesth�sie
$listAnesthType = new CTypeAnesth;
$orderanesth = "name";
$listAnesthType = $listAnesthType->loadList(null,$orderanesth);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("mediuser"    , $mediuser);
$smarty->assign("is_praticien", $is_praticien);
$smarty->assign("protocole"   , $protocole);
$smarty->assign("chir"        , $chir);
$smarty->assign("ufs"         , CUniteFonctionnelle::getUFs());

$smarty->assign("listPraticiens", $listPraticiens);
$smarty->assign("listFunctions" , $listFunctions);
$smarty->assign("listServices"  , $listServices);
$smarty->assign("listAnesthType", $listAnesthType);

$smarty->display("vw_edit_protocole.tpl");
