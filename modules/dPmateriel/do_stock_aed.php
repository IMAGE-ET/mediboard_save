<?php /* $Id$ */

/**
* @package Mediboard
* @subpackage mediusers
* @version $Revision$
* @author S�bastien Fillonneau
*/

global $AppUI;

$do = new CDoObjectAddEdit("CStock", "stock_id");
$do->createMsg = "Stock cr��";
$do->modifyMsg = "Stock modifi�";
$do->deleteMsg = "Stock supprim�";
$do->doIt();

?>