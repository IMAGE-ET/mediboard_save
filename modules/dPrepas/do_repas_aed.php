<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI;

$do = new CDoObjectAddEdit("CRepas", "repas_id");
$do->createMsg = "Repas cr��";
$do->modifyMsg = "Repas modifi�";
$do->deleteMsg = "Repas supprim�";
$do->doIt();

?>