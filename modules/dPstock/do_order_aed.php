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
    
    // TODO: here : call to the ORDER function !!!!
    
  }
}

$do = new CDoObjectAddEdit('CProductOrder', 'order_id');
$do->createMsg = 'Commande cr��e';
$do->modifyMsg = 'Commande modifi�e';
$do->deleteMsg = 'Commande supprim�e';
$do->doIt();

?>