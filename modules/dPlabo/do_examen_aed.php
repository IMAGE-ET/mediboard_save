<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CExamenLabo", "examen_labo_id");
$do->createMsg = "Examen cr��";
$do->modifyMsg = "Examen modifi�";
$do->deleteMsg = "Examen supprim�";
$do->doIt();

?>