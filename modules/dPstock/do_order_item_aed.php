<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien M�nager
*/

global $AppUI;

$do = new CDoObjectAddEdit('CProductOrderItem', 'order_item_id');
$do->createMsg = 'Article cr��';
$do->modifyMsg = 'Article modifi�';
$do->deleteMsg = 'Article supprim�';
$do->doIt();

?>