<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI;

$do = new CDoObjectAddEdit("CTypeRepas", "typerepas_id");
$do->createMsg = "Type de repas cr��";
$do->modifyMsg = "Type de repas modifi�";
$do->deleteMsg = "Type de repas supprim�";
$do->doIt();

?>