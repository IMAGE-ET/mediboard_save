<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision: $
* @author S�bastien Fillonneau
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_edit_menu"      , null , TAB_EDIT);
$module->registerTab("vw_planning_repas" , null , TAB_READ);
$module->registerTab("vw_edit_repas"     , null , TAB_EDIT);
$module->registerTab("vw_quantite"       , null , TAB_EDIT);
?>