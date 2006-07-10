<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage system
* @version $Revision$
* @author Thomas Despoix
*/

require_once($AppUI->getModuleClass("system", "message"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CMessage", "message_id");
$do->createMsg = "Message cr��";
$do->modifyMsg = "Message modifi�";
$do->deleteMsg = "Message supprim�";
$do->doIt();
?>