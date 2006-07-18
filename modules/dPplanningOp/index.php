<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPplanningOp
* @version $Revision$
* @author Romain Ollivier
*/

require_once($AppUI->getSystemClass("tabindex"));

$tabs = array();
$tabs[] = array("vw_idx_planning", "Consulter le planning", 1);
$tabs[] = array("vw_edit_planning", "Planifier / Modifier une intervention", 1);
$tabs[] = array("vw_edit_sejour", "Planifier / Modifier un s�jour", 0);
$tabs[] = array("vw_edit_urgence", "Planifier / Modifier une urgence", 0);
$tabs[] = array("vw_protocoles", "Protocoles", 1);
$tabs[] = array("vw_edit_protocole", "Cr�er / Modifier un protocole", 1);
$default = 0;

$index = new CTabIndex($tabs, $default);
$index->show();

?>