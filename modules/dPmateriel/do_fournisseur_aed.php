<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI;

$do = new CDoObjectAddEdit("CFournisseur", "fournisseur_id");
$do->createMsg = "Fournisseur cr��";
$do->modifyMsg = "Fournisseur modifi�";
$do->deleteMsg = "Fournisseur supprim�";
$do->doIt();

?>