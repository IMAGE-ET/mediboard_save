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

CCanDo::checkEdit();

// Utilisateur s�lectionn� ou utilisateur courant
$prat_id = CValue::getOrSession("chirSel", 0);
$dossier_anesth_id = CValue::get("dossier_anesth_id");

$userSel = CMediusers::get($prat_id);
$userSel->loadRefs();
$canUserSel = $userSel->canDo();

// V�rification des droits sur les praticiens
if (!$userSel->isMedical()) {
  CAppUI::setMsg("Vous devez selectionner un professionnel de sant�", UI_MSG_ALERT);
  CAppUI::redirect("m=dPcabinet&tab=0");
}

$canUserSel->needsEdit();

$selConsult = CValue::getOrSession("selConsult", 0);

// Consultation courante
$consult = new CConsultation();
$consult->_ref_chir = $userSel;

if ($selConsult) {
  $consult->load($selConsult);
  
  // V�rification de droits
  CCanDo::checkObject($consult);
  $canConsult = $consult->canDo();
  $canConsult->needsEdit();
  
  $consult->loadRefsFwd();

  // Chargement de la consult anesth�sie
  $consult->loadRefConsultAnesth();
  $consultAnesth = $consult->_ref_consult_anesth;

  foreach ($consult->_refs_dossiers_anesth as $_dossier) {
    $_dossier->loadRefConsultation();
    $_dossier->loadRefOperation()->loadRefPlageOp();
  }

  if ($dossier_anesth_id) {
    $consultAnesth = $consult->_refs_dossiers_anesth[$dossier_anesth_id];
    $consult->_ref_consult_anesth = $consultAnesth;
  }
  
  if ($consultAnesth->_id) {
    $consultAnesth->_ref_consultation->loadRefPraticien();
    $consultAnesth->_ref_operation->loadRefChir(true);
    $consultAnesth->_ref_sejour->loadRefDossierMedical();
    $consultAnesth->_ref_sejour->loadRefPraticien(true);
  }
  
  $patient =& $consult->_ref_patient;
  $patient->loadRefsSejours();
  foreach ($patient->_ref_sejours as $_sejour) {
    $_sejour->loadRefsOperations();
    foreach ($_sejour->_ref_operations as $_operation) {
      $_operation->loadRefsConsultAnesth();
      $_operation->loadRefPlageOp(true);
      $_operation->loadRefChir(true);
    }
  }
}
else {
  $consult->_ref_consult_anesth = new CConsultAnesth();
}

$consult_anesth =& $consult->_ref_consult_anesth;
$nextSejourAndOperation = $patient->getNextSejourAndOperation($consult->_ref_plageconsult->date, true, $consult->_id);

$listChirs = $userSel->loadPraticiens(PERM_READ);

// Cr�ation du template
$smarty = new CSmartyDP();

$smarty->assign("consult"               , $consult);
$smarty->assign("consult_anesth"        , $consult_anesth);
$smarty->assign("patient"               , $patient);
$smarty->assign("nextSejourAndOperation", $nextSejourAndOperation);
$smarty->assign("listChirs"             , $listChirs);

$smarty->display("inc_consult_anesth/interventions.tpl");
