<?php

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI, $can, $m;

$do = new CDoObjectAddEdit("CActeNGAP", "acte_ngap_id");
$do->createMsg = "Acte NGAP cr��";
$do->modifyMsg = "Acte NGAP modifi�";
$do->deleteMsg = "Acte NGAP supprim�";
$do->doIt();

?>