<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI, $can, $m;

$do = new CDoObjectAddEdit("CExamIgs", "examigs_id");
$do->createMsg = "Examen IGS cr��";
$do->modifyMsg = "Examen IGS modifi�";
$do->deleteMsg = "Examen IGS supprim�";
$do->redirect = null;
$do->doIt();
?>