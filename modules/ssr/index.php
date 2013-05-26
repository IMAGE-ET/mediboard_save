<?php
/**
 * $Id$
 *
 * @package    Mediboard
 * @subpackage SSR
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision$
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

if (CAppUI::conf("ssr recusation use_recuse")) {
  $module->registerTab("vw_sejours_validation", TAB_EDIT);
}

$module->registerTab("vw_sejours_ssr"         , TAB_READ);
$module->registerTab("vw_aed_sejour_ssr"      , TAB_READ);
$module->registerTab("vw_kine_board"          , TAB_EDIT);
$module->registerTab("vw_idx_repartition"     , TAB_READ);
$module->registerTab("vw_plateau_board"       , TAB_READ);
$module->registerTab("vw_aed_replacement"     , TAB_ADMIN);
$module->registerTab("vw_idx_plateau"         , TAB_ADMIN);
$module->registerTab("vw_cdarr"               , TAB_READ);
$module->registerTab("vw_csarr"               , TAB_READ);
$module->registerTab("edit_codes_intervenants", TAB_ADMIN);
$module->registerTab("vw_stats"               , TAB_ADMIN);
$module->registerTab("vw_facturation_rhs"     , TAB_EDIT);
