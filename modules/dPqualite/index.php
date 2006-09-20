<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision: $
 *  @author Sébastien Fillonneau
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_procedures"         , "Procédures"                 , TAB_READ);
$module->registerTab("vw_procencours"        , "Procédure en cours"         , TAB_READ);
$module->registerTab("vw_procvalid"          , "Validation de procédures"   , TAB_EDIT);
$module->registerTab("vw_edit_classification", "Gestion des classifications", TAB_EDIT);
$module->registerTab("vw_edit_ei"            , "Gestion des Evenements"     , TAB_EDIT);
$module->registerTab("vw_incident"           , "Nouvel Incident"            , TAB_READ);
$module->registerTab("vw_incidentvalid"      , "Fiches Incidents"           , TAB_EDIT);

?>