<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPgestionCab
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $m;

$do = new CDoObjectAddEdit("CGestionCab", "gestioncab_id");
$do->createMsg = "Fiche cr��e";
$do->modifyMsg = "Fiche modifi�e";
$do->deleteMsg = "Fiche supprim�e";
$do->doIt();
?>