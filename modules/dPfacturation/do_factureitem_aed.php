<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPfacturation
 *  @version $Revision: $
 *  @author Alexis	
 */

global $AppUI;

$do = new CDoObjectAddEdit("CFactureItem", "factureitem_id");
$do->createMsg = "El�ment cr��";
$do->modifyMsg = "El�ment modifi�";
$do->deleteMsg = "El�ment supprim�";
$do->doIt();

?>