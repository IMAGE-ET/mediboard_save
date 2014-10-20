<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SalleOp
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

// @todo bloc n'est pas forc�ment actif
global $can;
$can->read |= CModule::getActive("dPbloc")->_can->read;
$can->needsRead();

$operation_id  = CValue::getOrSession("operation_id", null);

$operation = new COperation();
$operation->load($operation_id);
$operation->loadRefsAnesthPerops();
$operation->loadRefsFwd();
$operation->loadRefsActesCCAM();
foreach ($operation->_ref_actes_ccam as $acte) {
  $acte->loadRefsFwd();
}
$operation->loadAffectationsPersonnel();
if (CAppUI::conf('dPccam CCodeCCAM use_new_association_rules')) {
    $operation->guessActesAssociation();
}
else {
  foreach ($operation->_ref_actes_ccam as $acte) {
    $acte->guessAssociation();
  }
}

$operation->loadRefSortieLocker()->loadRefFunction();

$sejour = $operation->_ref_sejour;
$sejour->loadRefsFwd();
$sejour->loadRefPrescriptionSejour();

/** @var CAdministration[] $administrations */
$administrations = array();
$prescription_id = null;
if (CModule::getActive("dPprescription")) {
  $prescription_id = $sejour->_ref_prescription_sejour->_id;
  if ($prescription_id) {
    $administrations = CAdministration::getPerop($prescription_id, false);
  }
}

// Chargement des constantes saisies durant l'intervention
$whereConst = array();
$whereConst["datetime"] = "BETWEEN '$operation->_datetime_reel' AND '$operation->_datetime_reel_fin'";

$sejour->loadListConstantesMedicales($whereConst);  
  
// Tri des gestes et administrations perop par ordre chronologique
$perops = array();
foreach ($administrations as $_administration) {
  $_administration->loadRefsFwd();
  $perops[$_administration->dateTime][$_administration->_guid] = $_administration;
}
foreach ($operation->_ref_anesth_perops as $_perop) {
  $perops[$_perop->datetime][$_perop->_guid] = $_perop;
}

$constantes = array("pouls", "ta_gauche", "frequence_respiratoire", "score_sedation", "spo2", "diurese");
foreach ($sejour->_list_constantes_medicales as $_constante_medicale) {
  foreach ($constantes as $_constante) {
    $perops[$_constante_medicale->datetime][$_constante_medicale->_guid][$_constante] = $_constante_medicale->$_constante;
  }
}

if ($prescription_id) {
  // Chargements des perfusions pour afficher les poses et les retraits
  $prescription_line_mix = new CPrescriptionLineMix();
  $prescription_line_mix->prescription_id = $prescription_id;
  $prescription_line_mix->perop = 1;
  /** @var CPrescriptionLineMix[] $mixes */
  $mixes = $prescription_line_mix->loadMatchingList();
  
  foreach ($mixes as $_mix) {
    $_mix->loadRefsLines();
    if ($_mix->date_pose && $_mix->time_pose) {
      $perops[$_mix->_pose][$_mix->_guid] = $_mix;
    }
    if ($_mix->date_retrait && $_mix->time_retrait) {
      $perops[$_mix->_retrait][$_mix->_guid] = $_mix;
    } 
  }
}
ksort($perops);

$perop_graphs = array();
$time_debut_op = null;
$time_fin_op = null;
$yaxes_count = null;
$grid = array();
$labels = array();
$list_obr = array();

if (CAppUI::conf("dPsalleOp enable_surveillance_perop")) {
  /** @var CObservationResultSet[] $list_obr */
  list($list, $grid, $graphs, $labels, $list_obr) = CObservationResultSet::getChronological($operation, $operation->graph_pack_id);

  foreach ($list_obr as $_obr) {
    $_obr->loadFirstLog()->loadRefUser()->loadRefMediuser()->loadRefFunction();
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("patient"      , $operation->_ref_sejour->_ref_patient);
$smarty->assign("operation"    , $operation);
$smarty->assign("perops"       , $perops);
$smarty->assign("perop_graphs" , $perop_graphs);
$smarty->assign("yaxes_count"  , $yaxes_count);
$smarty->assign("time_debut_op", $time_debut_op);
$smarty->assign("time_fin_op"  , $time_fin_op);
$smarty->assign("observation_grid", $grid);
$smarty->assign("observation_labels", $labels);
$smarty->assign("observation_list", $list_obr);

$smarty->display("print_feuille_bloc.tpl");
