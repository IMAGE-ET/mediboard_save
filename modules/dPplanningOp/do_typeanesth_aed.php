<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPPlanningOp
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI;

require_once($AppUI->getModuleClass("dPplanningOp", "typeanesth"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CTypeAnesth", "type_anesth_id");
$do->createMsg = "Type d'anesth�sie cr��";
$do->modifyMsg = "Type d'anesth�sie modifi�";
$do->deleteMsg = "Type d'anesth�sie supprim�";
$do->doIt();

?>