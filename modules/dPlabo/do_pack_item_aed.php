<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPlabo
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CPackItemExamenLabo", "pack_item_examen_labo_id");
$do->createMsg = "Examen ajout�";
$do->modifyMsg = "Examen modifi�";
$do->deleteMsg = "Examen supprim�";
$do->doIt();

?>