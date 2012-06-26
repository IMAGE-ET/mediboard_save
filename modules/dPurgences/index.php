<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage dPurgences
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_idx_rpu"       , TAB_READ);
$module->registerTab("vw_aed_rpu"       , TAB_READ);

if (CAppUI::conf("dPhospi use_vue_topologique")) {
  $module->registerTab("vw_placement_patients"    , TAB_READ);
}
$module->registerTab("edit_consultation", TAB_EDIT);
$module->registerTab("vw_sortie_rpu"    , TAB_READ);
$module->registerTab("vw_attente"       , TAB_READ);

if (isset(CModule::$active["dPstock"])) {
  $module->registerTab("vw_stock_order" , TAB_READ);
}

$module->registerTab("vw_stats"         , TAB_ADMIN);