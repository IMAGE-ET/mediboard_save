<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPfacturation
 *  @version $Revision: $
 *  @author Poiron Yohann	
 */

global $AppUI;

$do = new CDoObjectAddEdit("CRubrique", "rubrique_id");
$do->createMsg = "Rubrique cr��e";
$do->modifyMsg = "Rubrique modifi�e";
$do->deleteMsg = "Rubrique supprim�e";
$do->doIt();

?>