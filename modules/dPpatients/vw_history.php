<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage Patients
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

CCanDo::checkRead();

// D�finition des variables
$patient_id = CValue::get("patient_id", 0);

//R�cup�ration du dossier complet patient
$patient = new CPatient;
$patient->load($patient_id);
$patient->loadDossierComplet();

$patient->loadHistory();

// log pour les s�jours
foreach ($patient->_ref_sejours as $sejour) {
  $sejour->loadHistory();

  // log pour les op�rations de ce s�jour
  $sejour->loadRefsOperations();
  foreach ($sejour->_ref_operations as $operation) {
    $operation->loadRefsFwd();
    $operation->loadHistory();
  }

  // log pour les affectations de ce s�jour
  $sejour->loadRefsAffectations();  
  foreach ($sejour->_ref_affectations as $affectation) {
    $affectation->loadHistory();
    $affectation->loadRefsFwd();
  }
}

// log pour les consultations
foreach ($patient->_ref_consultations as $consultation) {
  $consultation->loadHistory();
}

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("patient" , $patient );

$smarty->display("vw_history.tpl");
