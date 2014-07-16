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

global $m, $tab;

$module = CValue::get("module");
if (!$module) {
  $module = $m;
}

$canUnlockActes = $module == "dPpmsi" || CModule::getCanDo("dPsalleOp")->admin;

if (null == $object_class = CValue::get("object_class")) {
  CAppUI::stepMessage(UI_MSG_WARNING, "$tab-msg-mode-missing");
  return;
}

$NDA = "";
$IPP = "";

$confirmCloture = CValue::get("confirmCloture", 0);

switch ($object_class) {
  case "COperation":
    $object = new COperation();

    // Chargement de l'op�ration et g�n�ration du document
    $operation_id = CValue::post("mb_operation_id", CValue::getOrSession("object_id"));
    if ($object->load($operation_id)) {
      $object->loadRefSejour();

      $mbSejour = $object->_ref_sejour;
      $mbSejour->loadNDA();
      $NDA = $mbSejour->_NDA;
      $mbSejour->loadRefPatient();
      $mbSejour->_ref_patient->loadIPP();
      $IPP = $mbSejour->_ref_patient->_IPP;
    }
    break;
  case "CSejour":
    $object = new CSejour();

    // Chargement du s�jour et g�n�ration du document
    $sejour_id = CValue::post("mb_sejour_id", CValue::getOrSession("object_id"));
    if ($object->load($sejour_id)) {
      $object->loadRefPatient();
      $object->loadRefDossierMedical();
      $object->loadNDA();
      $NDA = $object->_NDA;
      $object->_ref_patient->loadIPP();
      $IPP = $object->_ref_patient->_IPP;
    }
    break;
  default:
    // Nothing to do
}

$object->countExchanges("pmsi", "evenementServeurActe");

// Cr�ation du template
$smarty = new CSmartyDP();
$smarty->assign("canUnlockActes", $canUnlockActes);
$smarty->assign("object", $object);
$smarty->assign("IPP"   , $IPP);
$smarty->assign("NDA"   , $NDA);
$smarty->assign("module", $module);
$smarty->assign("confirmCloture", $confirmCloture);
$smarty->display("../../dPpmsi/templates/inc_export_actes_pmsi.tpl");
