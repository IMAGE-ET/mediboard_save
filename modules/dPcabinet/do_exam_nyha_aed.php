<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $can, $m;

$do = new CDoObjectAddEdit("CExamNyha", "examnyha_id");
$do->createMsg = "Classification NYHA cr��";
$do->modifyMsg = "Classification NYHA modifi�";
$do->deleteMsg = "Classification NYHA supprim�";
$do->redirect = null;
$do->doIt();
?>