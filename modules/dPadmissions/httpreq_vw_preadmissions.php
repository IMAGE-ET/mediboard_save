<?php /* $Id: httpreq_vw_admissions.php 7207 2009-11-03 12:03:30Z rhum1 $ */

/**
 * @package Mediboard
 * @subpackage dPadmissions
 * @version $Revision: 7207 $
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

CCanDo::checkRead();

// Initialisation de variables

$order_col_pre = CValue::getOrSession("order_col_pre", "heure");
$order_way_pre = CValue::getOrSession("order_way_pre", "ASC");
$date          = CValue::getOrSession("date", mbDate());
$next          = mbDate("+1 DAY", $date);

$date_actuelle = mbDateTime("00:00:00");
$date_demain   = mbDateTime("00:00:00","+ 1 day");

$hier   = mbDate("- 1 day", $date);
$demain = mbDate("+ 1 day", $date);

$date_min = mbDateTime("00:00:00", $date);
$date_max = mbDateTime("23:59:59", $date);

// R�cup�ration de la liste des anesth�sistes
$mediuser = new CMediusers;
$anesthesistes = $mediuser->loadAnesthesistes(PERM_READ);

$consult = new CConsultation();

// R�cup�ration des consultation d'anesth�sie du jour
$ljoin = array();
$ljoin["plageconsult"] = "consultation.plageconsult_id = plageconsult.plageconsult_id";
$ljoin["patients"]     = "consultation.patient_id = patients.patient_id";
$where = array();
$where["consultation.patient_id"] = "IS NOT NULL";
$where["consultation.annule"] = "= '0'";
$where["plageconsult.chir_id"] = CSQLDataSource::prepareIn(array_keys($anesthesistes));
$where["plageconsult.date"] = "= '$date'";
if ($order_col_pre == "patient_id"){
  $order = "patients.nom $order_way_pre, patients.prenom $order_way_pre, consultation.heure";
} else {
  $order = "consultation.".$order_col_pre." ".$order_way_pre;
}

$listConsultations = $consult->loadList($where, $order, null, null, $ljoin);

foreach($listConsultations as $curr_consult) {
  $curr_consult->loadRefPatient();
  $curr_consult->loadRefPlageconsult();
  $curr_consult->_ref_chir->loadRefFunction();
  $curr_consult->loadRefConsultAnesth();
  $curr_consult->_ref_consult_anesth->loadRefOperation();
  $curr_sejour = $curr_consult->_ref_consult_anesth->_ref_sejour;
  if($curr_sejour->_id) {
    $curr_sejour->loadRefPatient();
    $curr_sejour->loadRefPraticien();
    $curr_sejour->loadNDA();
    $curr_sejour->loadRefsNotes();
    $curr_sejour->loadRefsAffectations();
    $curr_sejour->loadRefsOperations();
    foreach($curr_sejour->_ref_affectations as $_aff) {
      $_aff->loadView();
    }
    $curr_sejour->getDroitsCMU();
  } else {
    $curr_consult->_next_sejour_and_operation = $curr_consult->_ref_patient->getNextSejourAndOperation($curr_consult->_ref_plageconsult->date);
    if($curr_consult->_next_sejour_and_operation["COperation"]->_id) {
      $curr_consult->_next_sejour_and_operation["COperation"]->loadRefSejour();
      $curr_consult->_next_sejour_and_operation["COperation"]->_ref_sejour->loadRefPraticien();
      $curr_consult->_next_sejour_and_operation["COperation"]->_ref_sejour->loadNDA();
      $curr_consult->_next_sejour_and_operation["COperation"]->_ref_sejour->loadRefsNotes();
    } elseif($curr_consult->_next_sejour_and_operation["CSejour"]) {
      $curr_consult->_next_sejour_and_operation["CSejour"]->loadRefPraticien();
      $curr_consult->_next_sejour_and_operation["CSejour"]->loadNDA();
      $curr_consult->_next_sejour_and_operation["CSejour"]->loadRefsNotes();
    }
  }
}

// Chargement des prestations
$prestations = CPrestation::loadCurrentList();

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("hier", $hier);
$smarty->assign("demain", $demain);

$smarty->assign("date_min"         , $date_min);
$smarty->assign("date_max"         , $date_max);
$smarty->assign("date_demain"      , $date_demain);
$smarty->assign("date_actuelle"    , $date_actuelle);
$smarty->assign("date"             , $date);
$smarty->assign("order_col_pre"    , $order_col_pre);
$smarty->assign("order_way_pre"    , $order_way_pre);
$smarty->assign("listConsultations", $listConsultations);
$smarty->assign("prestations"      , $prestations);
$smarty->assign("canAdmissions"    , CModule::getCanDo("dPadmissions"));
$smarty->assign("canPatients"      , CModule::getCanDo("dPpatients"));
$smarty->assign("canPlanningOp"    , CModule::getCanDo("dPplanningOp"));

$smarty->display("inc_vw_preadmissions.tpl");

?>