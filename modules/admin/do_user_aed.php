<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage admin
* @version $Revision$
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CUser", "user_id");
$do->createMsg = "Utilisateur cr��";
$do->modifyMsg = "Utilisateur modifi�";
$do->deleteMsg = "Utilisateur supprim�";
$do->doIt();

?>