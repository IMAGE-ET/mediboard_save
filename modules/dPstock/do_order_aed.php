<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien M�nager
*/

global $AppUI;

// Current DateTime
if(isset($_POST['date_ordered'])){
  if($_POST['date_ordered'] == 'now'){
    $_POST['date_ordered'] = mbDateTime();
  }
}

if((isset($_POST['date_received']) && $_POST['date_received'] == 'now') ||
   (isset($_POST['_received']) && $_POST['_received'] == 1)) {
  $_POST['date_received'] = mbDateTime();
  $_POST['received'] = 1;
}

$do = new CDoObjectAddEdit('CProductOrder', 'order_id');
$do->createMsg = 'Commande cr��e';
$do->modifyMsg = 'Commande modifi�e';
$do->deleteMsg = 'Commande supprim�e';
$do->doIt();

?>