<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("vw_hospitalisation", "Hospitalisation"   , TAB_READ);
$module->registerTab("vw_bloc"           , "Bloc op�ratoire"   , TAB_READ);
$module->registerTab("vw_bloc2"          , "Journ�e op�ratoire", TAB_READ);
$module->registerTab("vw_time_op"        , "Stats de dur�es"   , TAB_READ);
$module->registerTab("vw_users"          , "Utilisateurs"      , TAB_ADMIN);

?>