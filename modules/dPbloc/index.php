<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPbloc
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_planning_week"     , null, TAB_EDIT);
$module->registerTab("vw_edit_planning"     , null, TAB_EDIT);
$module->registerTab("vw_edit_interventions", null, TAB_EDIT);
$module->registerTab("vw_suivi_salles"      , null, TAB_EDIT);
$module->registerTab("vw_urgences"          , null, TAB_EDIT);
$module->registerTab("vw_idx_materiel"      , null, TAB_EDIT);
$module->registerTab("vw_idx_salles"        , null, TAB_EDIT);
$module->registerTab("print_planning"       , null, TAB_READ);

?>