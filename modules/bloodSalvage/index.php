<?php /* $Id$ */

/**
 * @package Mediboard
 * @subpackage bloodSalvage
 * @version $Revision$
 * @author SARL OpenXtrem
 * @license GNU General Public License, see http://www.gnu.org/licenses/gpl.html 
 */

/**
 * On v�rifie que le module est bien install�
 */
$module = CModule::getInstalled(basename(dirname(__FILE__)));

/**
 * Puis on cr�e l'index avec les vues du module vw_*
 */
$module->registerTab("vw_bloodSalvage",      TAB_READ);
$module->registerTab("vw_bloodSalvage_sspi", TAB_READ);
$module->registerTab("vw_stats",             TAB_READ);
$module->registerTab("vw_cellSaver",         TAB_EDIT);

if(CModule::getActive("dPqualite")) {
  $module->registerTab("vw_typeEi_manager", TAB_EDIT);
}

?>