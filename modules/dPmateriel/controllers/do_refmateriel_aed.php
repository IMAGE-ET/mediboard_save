<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CRefMateriel", "reference_id");
$do->createMsg = "R�f�rence cr��e";
$do->modifyMsg = "R�f�rence modifi�e";
$do->deleteMsg = "R�f�rence supprim�e";
$do->doIt();

?>