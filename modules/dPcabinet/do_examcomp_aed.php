<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI;

require_once( $AppUI->getModuleClass("dPcabinet", "examComp") );
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CExamComp", "exam_id");
$do->createMsg = "Examen compl�mentaire cr��";
$do->modifyMsg = "Examen compl�mentaire modifi�";
$do->deleteMsg = "Examen compl�mentaire supprim�";
$do->doIt();

?>