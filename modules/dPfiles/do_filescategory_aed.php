<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI;

$do = new CDoObjectAddEdit("CFilesCategory", "file_category_id");
$do->createMsg = "Cat�gorie cr��e";
$do->modifyMsg = "Cat�gorie modifi�e";
$do->deleteMsg = "Cat�gorie supprim�e";
$do->doIt();

?>