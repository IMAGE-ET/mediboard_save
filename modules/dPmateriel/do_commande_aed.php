<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPmateriel
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CCommandeMateriel", "commande_materiel_id");
$do->createMsg = "Commande cr��e";
$do->modifyMsg = "Commande modifi�e";
$do->deleteMsg = "Commande supprim�e";
$do->doIt();

?>