<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPqualite
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI;

$do = new CDoObjectAddEdit("CThemeDoc", "doc_theme_id");
$do->createMsg = "Th�me cr��";
$do->modifyMsg = "Th�me modifi�";
$do->deleteMsg = "Th�me supprim�";
$do->doIt();

?>