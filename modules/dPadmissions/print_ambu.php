<?php

/**
 * $Id$
 *
 * @category Admissions
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version  $Revision$
 * @link     http://www.mediboard.org
 */

CCanDo::checkRead();

// R�cup�ration des dates
$date       = CValue::getOrSession("date", CMbDT::date());
$type       = CValue::getOrSession("type", "ambu");
$service_id = CValue::getOrSession("service_id");

// Initialisation
$sejour = new CSejour();
$sejours = array();

// R�cup�ration de la liste des services
$where = array();
$where["externe"]   = "= '0'";
$where["cancelled"] = "= '0'";
$service = new CService();
$services = $service->loadGroupList($where);

// R�cup�ration des sorties du jour
$limit1 = $date." 00:00:00";
$limit2 = $date." 23:59:59";

// ljoin pour filtrer par le service
$ljoin["affectation"] = "sejour.sejour_id = affectation.sejour_id";
$ljoin["service"]     = "affectation.service_id = service.service_id";

if ($service_id) {
  $where["service.service_id"] = " = '$service_id'";
}

$group = CGroups::loadCurrent();

$order = "service.nom, sejour.entree_reelle";
$where["sortie_prevue"]   = "BETWEEN '$limit1' AND '$limit2'";
$where["type"]            = " = '$type'";
$where["sejour.annule"]   = " = '0'";
$where["sejour.group_id"] = " = '$group->_id'";
/** @var CSejour[] $sejours */
$sejours = $sejour->loadList($where, $order, null, null, $ljoin);

CMbObject::massLoadFwdRef($sejours, "patient_id");
CMbObject::massLoadFwdRef($sejours, "praticien_id");

foreach ($sejours as $key => $_sejour) {
  $_sejour->loadRefPatient();
  $_sejour->loadRefPraticien();
  $_sejour->loadRefsAffectations("sortie ASC");
  $_sejour->loadRefsOperations();
  $_sejour->_duree = CMbDT::subTime(CMbDT::time($_sejour->entree_reelle), CMbDT::time($_sejour->sortie_reelle));

  $_sejour->_ref_last_operation->loadRefSortieLocker()->loadRefFunction();

  $affectation = $_sejour->_ref_last_affectation;
  if ($affectation->_id) {
    $affectation->loadReflit();
    $affectation->_ref_lit->loadCompleteView();
  }
  foreach ($_sejour->_ref_affectations as $_affect) {
    $_affect->loadRefLit();
    $_affect->_ref_lit->loadCompleteView();
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("service_id", $service_id);
$smarty->assign("sejours"   , $sejours);
$smarty->assign("services"  , $services);
$smarty->assign("date"      , $date);
$smarty->assign("type"      , $type);

$smarty->display("print_ambu.tpl");
