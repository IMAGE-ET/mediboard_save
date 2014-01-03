<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPpersonnel
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */
 
CCanDo::checkEdit();

if (!($plageop_id = CValue::getOrSession("plageop_id"))) {
  CAppUI::setMsg("Vous devez choisir une plage op�ratoire", UI_MSG_WARNING);
  CAppUI::redirect("m=dPbloc&tab=vw_edit_planning");
}

// Infos sur la plage op�ratoire
$plage = new CPlageOp();
$plage->load($plageop_id);

if (!$plage->temps_inter_op) {
  $plage->temps_inter_op = "00:00:00";
}

// liste des anesthesistes
$mediuser = new CMediusers();
$listAnesth = $mediuser->loadListFromType(array("Anesth�siste"));

// Chargement du personnel
$listPers = $plage->loadPersonnelDisponible(null, true);
$affectations_plage = $plage->_ref_affectations_personnel;

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("affectations_plage", $affectations_plage);
$smarty->assign("listPers"          , $listPers);
$smarty->assign("listAnesth"        , $listAnesth);
$smarty->assign("plage"             , $plage);

$smarty->display("inc_view_personnel_plage.tpl");
