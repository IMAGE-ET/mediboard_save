<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPqualite
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI;

$do = new CDoObjectAddEdit("CEiItem", "ei_item_id");
$do->createMsg = "Item cr��";
$do->modifyMsg = "Item modifi�";
$do->deleteMsg = "Item supprim�";
$do->doIt();

?>