<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien M�nager
*/

global $AppUI;

// Current DateTime
if(isset($_POST['date_received'])){
  if($_POST['date_received'] == 'now'){
    $_POST['date_received'] = mbDateTime();
  }
}

$do = new CDoObjectAddEdit('CProductOrderItem', 'order_item_id');
$do->createMsg = 'Article cr��';
$do->modifyMsg = 'Article modifi�';
$do->deleteMsg = 'Article supprim�';
$do->doIt();

?>