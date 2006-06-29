<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcim10
* @version $Revision$
* @author Romain Ollivier
*/

require_once($AppUI->getSystemClass("tabindex"));

$tabs = array();
$tabs[] = array("view_dpadmin", "Configuration g�n�rale", 0);
$tabs[] = array("view_history", "Historique", 0);
$tabs[] = array("view_messages", "Messagerie", 0);
$tabs[] = array("view_logs", "Logs syst�me", 0);
$tabs[] = array("view_access_logs", "Logs d'acc�s", 0);
$default = "view_dpadmin";

$index = new CTabIndex($tabs, $default);
$index->show();

?>
