<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI, $canRead, $canEdit, $m;

$do = new CDoObjectAddEdit("CTarif", "tarif_id");
$do->createMsg = "Tarif cr��";
$do->modifyMsg = "Tarif modifi�";
$do->deleteMsg = "Tarif supprim�";
$do->doIt();

?>