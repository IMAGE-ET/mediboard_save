<?php 

/**
 * Duplication de dossier d'anesth�sie
 *  
 * @category Cabinet
 * @package  Mediboard
 * @author   SARL OpenXtrem <dev@openxtrem.com>
 * @license  GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 * @version  SVN: $Id:\$ 
 * @link     http://www.mediboard.org
 */

$dossier_anesth_id = CValue::post("_consult_anesth_id");
$sejour_id         = CValue::post("sejour_id");
$operation_id      = CValue::post("operation_id");
$redirect          = CValue::post("redirect", 1);

$consult_anesth = new CConsultAnesth();
$consult_anesth->load($dossier_anesth_id);

$consult_anesth->_id = $consult_anesth->operation_id = $consult_anesth->sejour_id = null;

if ($sejour_id) {
  $consult_anesth->sejour_id = $sejour_id;
}

if ($operation_id) {
  $consult_anesth->operation_id = $operation_id;
}

$msg = $consult_anesth->store();

$represcription = 0;
if ($msg) {
  CAppUI::setMsg($msg);
}
else {
  CAppUI::setMsg(CAppUI::tr("CConsultAnesth-msg-duplicate"));

  //Cr�ation de la prescription de s�jour selon pref user
  if ($consult_anesth->sejour_id && CAppUI::pref("show_replication_duplicate")) {
    $prescription = new CPrescription();
    $prescription->object_class = 'CSejour';
    $prescription->object_id = $consult_anesth->sejour_id;
    $prescription->type = 'sejour';
    if (!$prescription->loadMatchingObject()) {
      if ($msg = $prescription->store()) {
        CAppUI::setMsg($msg, UI_MSG_ERROR);
      }
    }
    $represcription = 1;
  }
}

echo CAppUI::getMsg();

if ($redirect) {
  CAppUI::redirect(
    "m=cabinet&tab=edit_consultation&selConsult=".
    $consult_anesth->consultation_id."&dossier_anesth_id=".$consult_anesth->_id."&represcription=$represcription"
  );
}

CApp::rip();