<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $can, $m;

$do = new CDoObjectAddEdit("CPlageconsult", "plageconsult_id");
$do->createMsg = "Plage cr��e";
$do->modifyMsg = "Plage modifi�e";
$do->deleteMsg = "Plage supprim�e";
$do->doIt();

?>