<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI, $can, $m;

$do = new CDoObjectAddEdit("CExamPossum", "exampossum_id");
$do->createMsg = "Examen score possum cr��";
$do->modifyMsg = "Examen score possum modifi�";
$do->deleteMsg = "Examen score possum supprim�";
$do->redirect = null;
$do->doIt();
?>