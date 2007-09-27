<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_planning"      , null , TAB_READ);
$module->registerTab("edit_planning"    , null , TAB_READ);
$module->registerTab("edit_consultation", null , TAB_EDIT);
$module->registerTab("vw_dossier"       , null , TAB_EDIT);
$module->registerTab("form_print_plages", null , TAB_READ);
$module->registerTab("vw_compta"        , null , TAB_EDIT);
$module->registerTab("vw_categories"    , null , TAB_EDIT);


if (@$AppUI->user_prefs["GestionFSE"]) {
  $module->registerTab("vw_intermax", null , TAB_EDIT);
}

?>