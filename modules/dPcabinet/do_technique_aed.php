<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPcabinet
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI;

require_once( $AppUI->getModuleClass("dPcabinet", "techniqueComp") );
require_once($AppUI->getSystemClass("doobjectaddedit"));

$do = new CDoObjectAddEdit("CTechniqueComp", "technique_id");
$do->createMsg = "Technique compl�mentaire cr��";
$do->modifyMsg = "Technique compl�mentaire modifi�";
$do->deleteMsg = "Technique compl�mentaire supprim�";
$do->doIt();

?>