<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision: $
* @author S�bastien Fillonneau
*/

global $AppUI;

$do = new CDoObjectAddEdit("CCatalogueLabo", "catalogue_labo_id");
$do->createMsg = "Catalogue cr��";
$do->modifyMsg = "Catalogue modifi�";
$do->deleteMsg = "Catalogue supprim�";
$do->doIt();

?>