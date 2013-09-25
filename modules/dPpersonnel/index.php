<?php
/**
 * $Id:$
 *
 * @package    Mediboard
 * @subpackage dPpersonnel
 * @author     SARL OpenXtrem <dev@openxtrem.com>
 * @license    GNU General Public License, see http://www.gnu.org/licenses/gpl.html
 * @version    $Revision:$
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_edit_personnel"        , TAB_READ);
$module->registerTab("vw_affectations_pers"     , TAB_READ);
$module->registerTab("vw_affectations_multiples", TAB_EDIT);
$module->registerTab("vw_idx_plages_conge"      , TAB_READ);
$module->registerTab("vw_planning_conge"        , TAB_READ);
?>