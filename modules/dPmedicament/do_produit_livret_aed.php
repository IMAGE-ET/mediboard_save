<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPmedicament
* @version $Revision: $
* @author Alexis Granger
*/

global $AppUI;

$do = new CDoObjectAddEdit("CProduitLivretTherapeutique", "produit_livret_id");
$do->createMsg = "Element cr��";
$do->modifyMsg = "Element modifi�";
$do->deleteMsg = "Element supprim�";
$do->doIt();

?>