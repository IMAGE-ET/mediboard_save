<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: $
* @author Romain Ollivier
*/

global $AppUI;

$do = new CDoObjectAddEdit("CCatalogueLabo", "catalogue_labo_id");
$do->createMsg = "Catalogue cr��";
$do->modifyMsg = "Catalogue modifi�";
$do->deleteMsg = "Catalogue supprim�";
$do->doIt();

?>