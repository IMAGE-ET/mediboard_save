<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPlabo
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CExamenLabo", "examen_labo_id");
$do->createMsg = "Analyse cr��e";
$do->modifyMsg = "Analyse modifi�e";
$do->deleteMsg = "Analyse supprim�e";
$do->doIt();

?>