<?php /* $Id: $ */

/**
 *	@package Mediboard
 *	@subpackage bloodSalvage
 *	@version $Revision: $
 *  @author Alexandre Germonneau
 */

/**
 * On v�rifie que le module est bien install�
 */
$module = CModule::getInstalled(basename(dirname(__FILE__)));

/**
 * Puis on cr�e l'index avec les vues du module vw_*
 */
$module->registerTab("vw_bloodSalvage",      null, TAB_READ);
$module->registerTab("vw_bloodSalvage_sspi", null, TAB_READ);
$module->registerTab("vw_stats",             null, TAB_ADMIN);
$module->registerTab("vw_cellSaver",         null, TAB_EDIT);

if(CModule::getActive("dPqualite")) {
	$module->registerTab("vw_typeEi_manager",  null, TAB_EDIT);	
}

?>