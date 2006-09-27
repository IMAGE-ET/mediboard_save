<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPqualite
 *  @version $Revision: $
 *  @author S�bastien Fillonneau
 */

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_procedures"         , "Proc�dures"                 , TAB_READ);
$module->registerTab("vw_procencours"        , "Proc�dure en cours"         , TAB_EDIT);
$module->registerTab("vw_procvalid"          , "Validation de proc�dures"   , TAB_ADMIN);
$module->registerTab("vw_edit_classification", "Classifications"            , TAB_ADMIN);
$module->registerTab("vw_modeles"            , "Mod�les"                    , TAB_EDIT);
$module->registerTab("vw_edit_ei"            , "Gestion des Evenements"     , TAB_ADMIN);
$module->registerTab("vw_incident"           , "Nouvel Incident"            , TAB_READ);
$module->registerTab("vw_incidentvalid"      , "Fiches Incidents"           , TAB_EDIT);

?>