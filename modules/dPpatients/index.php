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

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_patients"              , TAB_READ);
$module->registerTab("vw_full_patients"             , TAB_READ);
$module->registerTab("vw_edit_patients"             , TAB_EDIT);
$module->registerTab("vw_correspondants"            , TAB_EDIT);
//$module->registerTab("vw_recherche"               , TAB_READ);
$module->registerTab("vw_recherche_dossier_clinique", TAB_READ);

if (CAppUI::$user->_user_type == 0) {
  $module->registerTab("vw_identito_vigilance"      , TAB_ADMIN);
}

if (CAppUI::conf("dPsalleOp enable_surveillance_perop")) {
  $module->registerTab("vw_config_param_surveillance", TAB_ADMIN);
  $module->registerTab("vw_supervision_graph",         TAB_ADMIN);
}

if (CAppUI::pref("allowed_identity_status")) {
  $module->registerTab("vw_patient_state"              , TAB_ADMIN);
}