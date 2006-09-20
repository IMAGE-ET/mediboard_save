<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcim10
* @version $Revision$
* @author Romain Ollivier
*/

$module = CModule::getInstalled(basename(dirname(__FILE__)));

$module->registerTab("view_dpadmin"    , "Configuration g�n�rale", TAB_READ);
$module->registerTab("view_modules"    , "Modules"               , TAB_READ);
$module->registerTab("view_history"    , "Historique"            , TAB_READ);
$module->registerTab("view_messages"   , "Messagerie"            , TAB_READ);
$module->registerTab("view_logs"       , "Logs syst�me"          , TAB_READ);
$module->registerTab("view_access_logs", "Logs d'acc�s"          , TAB_READ);
$module->registerTab("view_metrique"   , "M�trique"              , TAB_READ);

?>
