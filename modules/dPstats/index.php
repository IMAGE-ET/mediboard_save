<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPstats
* @version $Revision$
* @author Romain Ollivier
*/

$tabs = array();
$tabs[] = array("vw_hospitalisation", "Hospitalisation", 0);
$tabs[] = array("vw_bloc", "Bloc op�ratoire", 0);
$tabs[] = array("vw_time_op", "Temps op�ratoires", 0);
$tabs[] = array("vw_users", "Utilisateurs", 0);
//$tabs[] = array("vw_activite", "Activite", 0);
$default = "vw_hospitalisation";

$index = new CTabIndex($tabs, $default);
$index->show();

?>