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

$do = new CDoObjectAddEdit('CProductDelivery', 'delivery_id');
$do->createMsg = 'Administration cr��e';
$do->modifyMsg = 'Administration modifi�e';
$do->deleteMsg = 'Administration supprim�e';
$do->doIt();

?>