<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPrepas
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI;

$do = new CDoObjectAddEdit("CPlat", "plat_id");
$do->createMsg = "Plat cr��";
$do->modifyMsg = "Plat modifi�";
$do->deleteMsg = "Plat supprim�";
$do->doIt();

?>