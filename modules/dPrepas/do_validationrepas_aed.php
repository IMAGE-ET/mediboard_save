<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI;

$do = new CDoObjectAddEdit("CValidationRepas", "validationrepas_id");
$do->createMsg = "Validation des repas cr��e";
$do->modifyMsg = "Validation des repas modifi�e";
$do->deleteMsg = "Validation des repas supprim�e";
$do->doIt();
?>