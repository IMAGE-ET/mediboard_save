<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPhospi
* @version $Revision$
* @author Thomas Despoix
*/

global $AppUI, $m;

$do = new CDoObjectAddEdit("CService", "service_id");
$do->createMsg = "Service cr��";
$do->modifyMsg = "Service modifi�";
$do->deleteMsg = "Service supprim�";
$do->doIt();
?>