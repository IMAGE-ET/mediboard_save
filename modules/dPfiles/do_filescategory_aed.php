<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPfiles
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI;

require_once($AppUI->getModuleClass("dPfiles", "filescategory"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CFilesCategory", "files_category_id");
$do->createMsg = "Cat�gorie cr��e";
$do->modifyMsg = "Cat�gorie modifi�e";
$do->deleteMsg = "Cat�gorie supprim�e";
$do->doIt();

?>