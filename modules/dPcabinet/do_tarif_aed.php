<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

require_once($AppUI->getModuleClass("dPcabinet", "tarif"));
require_once($AppUI->getSystemClass('doobjectaddedit'));

$do = new CDoObjectAddEdit("CTarif", "tarif_id");
$do->createMsg = "Tarif cr��";
$do->modifyMsg = "Tarif modifi�";
$do->deleteMsg = "Tarif supprim�";
$do->doIt();

?>