<?php /* $Id: $ */

/**
* @package Mediboard
* @subpackage dPstock
* @version $Revision: $
* @author Fabien M�nager
*/

$do = new CDoObjectAddEdit('CProductStockService', 'stock_id');
$do->doIt();

?>