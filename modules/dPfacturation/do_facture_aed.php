<?php /* $Id: $ */

/**
 *  @package Mediboard
 *  @subpackage dPfacturation
 *  @version $Revision: $
 *  @author Alexis / Yohann	
 */

global $AppUI;

$do = new CDoObjectAddEdit("CFacture", "facture_id");
$do->createMsg = "Facture cr��e";
$do->modifyMsg = "Facture modifi�e";
$do->deleteMsg = "Facture supprim�e";
$do->doIt();

?>