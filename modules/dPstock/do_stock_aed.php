<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien M�nager
*/

global $AppUI;

$do = new CDoObjectAddEdit('CProductStockOut', 'stock_out_id');
$do->createMsg = 'D�stockage cr��';
$do->modifyMsg = 'D�stockage modifi�';
$do->deleteMsg = 'D�stockage supprim�';
$do->doIt();

?>