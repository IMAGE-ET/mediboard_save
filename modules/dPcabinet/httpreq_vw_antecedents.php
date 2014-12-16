<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Cabinet
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::check();

$sejour_id         = CValue::getOrSession("sejour_id");
$patient_id        = CValue::getOrSession("patient_id");
$show_header       = CValue::getOrSession("show_header", 0);
$dossier_anesth_id = CValue::getOrSession("dossier_anesth_id");

$sejour  = new CSejour();
$patient = new CPatient();

if ($sejour_id) {
  $sejour->load($sejour_id);
  $patient = $sejour->loadRefPatient();
}
else if ($patient_id) {
  $patient->load($patient_id);
}

$patient->loadRefPhotoIdentite();
$patient->loadRefDossierMedical();

// Read-only view if user cannot edit
if ($patient->_ref_dossier_medical && $patient->_ref_dossier_medical->_id) {
  $patient->_ref_dossier_medical->canDo();

  if (!$patient->_ref_dossier_medical->_canEdit) {
    $smarty = new CSmartyDP("modules/dPpatients");
    $smarty->assign("object", $patient->_ref_dossier_medical);
    $smarty->display("CDossierMedical_complete.tpl");
    return;
  }
}

$patient->loadLastGrossesse();
$patient->loadLastAllaitement();

$isPrescriptionInstalled = CModule::getActive("dPprescription");

// Cr�ation du template
$smarty = new CSmartyDP();

if ($isPrescriptionInstalled) {
  $smarty->assign("line", new CPrescriptionLineMedicament());
}

$smarty->assign("current_m"  , "dPcabinet");
$smarty->assign("sejour_id"  , $sejour->_id);
$smarty->assign("patient"    , $patient);
$smarty->assign("antecedent" , new CAntecedent());
$smarty->assign("traitement" , new CTraitement());
$smarty->assign("_is_anesth" , "1");
$smarty->assign("userSel"    , CMediusers::get());
$smarty->assign("today"      , CMbDT::date());
$smarty->assign("isPrescriptionInstalled", $isPrescriptionInstalled);
$smarty->assign("sejour"     , $sejour);
$smarty->assign("show_header", $show_header);
$smarty->assign("dossier_anesth_id", $dossier_anesth_id);

$smarty->display("inc_ant_consult.tpl");
