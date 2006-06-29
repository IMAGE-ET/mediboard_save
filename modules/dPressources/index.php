<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPressources
* @version $Revision$
* @author Romain OLLIVIER
*/

require_once($AppUI->getSystemClass("tabindex"));

$tabs = array();
$tabs[] = array("view_planning", "Planning r�servations", 0);
$tabs[] = array("edit_planning", "Administration des plages", 1);
$tabs[] = array("view_compta", "Comptabilit�", 1);
$default = "view_planning";

$index = new CTabIndex($tabs, $default);
$index->show();

?>