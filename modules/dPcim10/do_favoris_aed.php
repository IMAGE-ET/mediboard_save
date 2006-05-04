<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcim10
* @version $Revision$
* @author Romain Ollivier
*/

require_once($AppUI->getModuleClass("dPcim10", "favoricim10"));
require_once($AppUI->getSystemClass('doobjectaddedit'));

$do = new CDoObjectAddEdit("CFavoricim10", "favoris_id");
$do->createMsg = "Favori cr��";
$do->modifyMsg = "Favori modifi�";
$do->deleteMsg = "Favori supprim�";
$do->doIt();

?>