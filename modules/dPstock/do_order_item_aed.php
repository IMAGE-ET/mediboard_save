<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien M�nager
*/

global $AppUI;

// Current DateTime
if((isset($_POST['date_received']) && ($_POST['date_received'] == 'now')) ||
   (isset($_POST['_received']) && ($_POST['_received'] == 1))) {
  $_POST['date_received'] = mbDateTime();
} else if (isset($_POST['_received']) && ($_POST['_received'] == 0)) {
  $_POST['date_received'] = null;
}

$do = new CDoObjectAddEdit('CProductOrderItem', 'order_item_id');
$do->createMsg = 'Article cr��';
$do->modifyMsg = 'Article modifi�';
$do->deleteMsg = 'Article supprim�';
$do->doIt();

?>