<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien M�nager
*/

global $AppUI;

$do = new CDoObjectAddEdit('CProductOrder', 'order_id');
$do->createMsg = 'Commande cr��e';
$do->modifyMsg = 'Commande modifi�e';
$do->deleteMsg = 'Commande supprim�e';
$do->doIt();

?>