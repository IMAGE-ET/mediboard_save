<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcompteRendu
* @version $Revision$
* @author Thomas Despoix
*/
global $AppUI, $m;

$do = new CDoObjectAddEdit("CAideSaisie", "aide_id");
$do->createMsg = "Aide cr��e";
$do->modifyMsg = "Aide modifi�e";
$do->deleteMsg = "Aide supprim�e";
$do->doIt();

?>