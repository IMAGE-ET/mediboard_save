<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPressources
* @version $Revision: $
* @author Thomas Despoix
*/

$tabs = array();
$tabs[] = array("view_identifiants", "Identifiants Sant�400", 1);
$default = "view_identifiants";

$index = new CTabIndex($tabs, $default);
$index->show();

?>