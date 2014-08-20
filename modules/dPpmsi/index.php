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

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_dossier_pmsi"    , TAB_EDIT);
//$module->registerTab("vw_dossier"         , TAB_READ);
$module->registerTab("vw_list_hospi"      , TAB_READ);
$module->registerTab("vw_list_interv"     , TAB_READ);
$module->registerTab("edit_actes"         , TAB_READ);
//$module->registerTab("labo_groupage"      , TAB_ADMIN);
$module->registerTab("form_print_planning", TAB_READ);
//$module->registerTab("vw_last_docs"       , TAB_EDIT);
if (CAppUI::conf("ref_pays") == "2") {
  $module->registerTab("vw_idx_sortie"     , TAB_READ);
}
