<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien M�nager
*/

global $AppUI;

if (isset($_POST['date']) && ($_POST['date'] == 'now')) {
	$_POST['date'] = mbDateTime();
}

$do = new CDoObjectAddEdit('CProductStockOut', 'stock_out_id');
$do->createMsg = 'D�stockage cr��';
$do->modifyMsg = 'D�stockage modifi�';
$do->deleteMsg = 'D�stockage supprim�';
$do->doIt();

?>