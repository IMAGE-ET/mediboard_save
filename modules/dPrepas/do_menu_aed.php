<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI;

$do = new CDoObjectAddEdit("CMenu", "menu_id");
$do->createMsg = "Menu cr��";
$do->modifyMsg = "Menu modifi�";
$do->deleteMsg = "Menu supprim�";
$do->doIt();

?>