<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage PMSI
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkEdit();

$group = CGroups::loadCurrent();

$sejour  = new CSejour();
$patient = new CPatient();

// Si on passe un num�ro de dossier,
// on charge le patient et le s�jour correspondant
if ($NDA = CValue::get("NDA")) {
  $sejour->loadFromNDA($NDA);
  if ($sejour->_id && $sejour->group_id == $group->_id) {
    $patient = $sejour->loadRefPatient();
    CValue::setSession("sejour_id", $sejour->_id);
    CValue::setSession("patient_id", $patient->_id);
  }
}

// Si on n'a pas r�cup�r� de patient via le numero de dossier,
// on charge le dossier en session
if (!$patient->_id) {
  $patient->load(CValue::getOrSession("patient_id"));
  $sejour->load(CValue::getOrSession("sejour_id"));
  // Si le s�jour a un patient diff�rent de celui selectionn�,
  // on le d�selectionne
  if ($patient->_id && $sejour->_id && $sejour->patient_id != $patient->_id) {
    CValue::setSession("sejour_id");
    $sejour = new CSejour();
  }
  // Si on a un s�jour mais pas de patient,
  // on utilise le patient du s�jour
  if ($sejour->_id && !$patient->_id) {
    $patient->load($sejour->patient_id);
    CValue::setSession("patient_id", $patient->_id);
  }
}

// Si on a un patient,
// on charge ses r�f�rences
if ($patient->_id) {
  $patient->loadRefsSejours();
  $patient->loadRefsConsultations();
  // Si on n'a pas de s�jour,
  // on prend le premier de la liste des s�jours du patient
  if (!$sejour->_id && count($patient->_ref_sejours)) {
    $sejour = reset($patient->_ref_sejours);
  }
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("patient", $patient);
$smarty->assign("sejour" , $sejour);

$smarty->display("vw_dossier_pmsi.tpl");