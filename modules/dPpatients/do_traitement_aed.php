<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPpatients
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CTraitement", "traitement_id");
$do->createMsg = "Traitement cr��";
$do->modifyMsg = "Traitement modifi�";
$do->deleteMsg = "Traitement supprim�";
$do->doIt();

?>