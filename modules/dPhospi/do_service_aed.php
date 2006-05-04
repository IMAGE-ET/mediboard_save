<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $m;

require_once($AppUI->getModuleClass($m, "service"));
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CService", "service_id");
$do->createMsg = "Service cr��";
$do->modifyMsg = "Service modifi�";
$do->deleteMsg = "Service supprim�";
$do->doIt();
?>