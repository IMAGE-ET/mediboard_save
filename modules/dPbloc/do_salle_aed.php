<?php /* $Id$ */

/**
 *	@package Mediboard
 *	@subpackage dPbloc
 *	@version $Revision$
 *  @author Romain Ollivier
 */

global $AppUI;

$do = new CDoObjectAddEdit("CSalle", "salle_id");
$do->createMsg = "Salle cr��e";
$do->modifyMsg = "Salle modifi�e";
$do->deleteMsg = "Salle supprim�e";
$do->doIt();

?>