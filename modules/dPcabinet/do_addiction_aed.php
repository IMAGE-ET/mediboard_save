<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $canRead, $canEdit, $m;

$do = new CDoObjectAddEdit("CAddiction", "addiction_id");
$do->createMsg = "Addiction cr��e";
$do->modifyMsg = "Addiction modifi�e";
$do->deleteMsg = "Addiction supprim�e";
$do->doIt();

?>