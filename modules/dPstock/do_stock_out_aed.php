<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien M�nager
*/

global $AppUI;

if (isset($_GET['date']) && ($_GET['date'] == 'now')) {
	$_GET['date'] = mbDateTime();
}

$do = new CDoObjectAddEdit('CProductStock', 'stock_id');
$do->createMsg = 'Stock cr��';
$do->modifyMsg = 'Stock modifi�';
$do->deleteMsg = 'Stock supprim�';
$do->doIt();

?>